<?php

namespace frontend\modules\profile\controllers;
use Yii;
use \common\models\ysd\Account;
use \common\models\ysd\AccountStatus;
use \common\models\CustomerGrade;

/**
 * CRUD for dtb_customer
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/controllers/DebitController.php $
 * $Id: DebitController.php 4249 2020-04-24 16:42:58Z mori $
 */

class DebitController extends BaseController
{
    public function beforeAction($action)
    {
        if('update' === $action->id)
        {
            if(Yii::$app->request->isPost)
                $this->enableCsrfValidation = false; // accept HTTP POST from outer server
        }

        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'口座振替','url'=>[$this->defaultAction]];

        return true;
    }

    /**
     * display customer's Bank Account
     */
    public function actionIndex()
    {
        $user    = Yii::$app->user->identity;
        $rrq     = \common\models\ysd\RegisterRequest::startup($user);
        $account = $user->ysdAccount;

        // 代理店でない場合は、会員区分「スタンダード」か否かで判定する
        if(!$user->isAgency())
        {

    /**
     * delete (by sakai) 2020/03/19        
            if(CustomerGrade::PKEY_AA == $user->grade_id)
            {
                iisset($account->detail))
                    self::addFlash('error',"会員区分「スタンダード」のため、現在ご利用いただけません。会員区分「スペシャル」以上でご利用可能になります。");
                else
                    self::addFlash('error',"会員区分「スタンダード」のため、現在お申し込みいただけません。会員区分「スペシャル」以上でお申し込み可能になります。");
            
            }
     */

        }
        if(! $rrq->validate())
            $this->renderValidationError($rrq);

        $view = 'index';
        if('echom-frontend' == Yii::$app->id) {
            $view = 'echom/index';
        }
        return $this->render($view, [
            'rrq'   => $rrq,
            'model' => $account
        ]);
    }

    /**
     * 404 Not Found
     */
    public function actionView($id=null)
    {
        throw new \yii\web\NotFoundHttpException();
    }

    /**
     * not implemented
     */
    public function actionCreate()
    {
        $ret = $this->sendYsdRequest();

        if($ret)
        {
            self::addFlash('success','手順をご案内するメールが配信されました。数分待っても届かない場合、＜info@nekonet.co.jp＞からの受信を許可してもう一度お試しください');

            if(! $account = Account::findOne(['customer_id' => Yii::$app->user->id]))
                 $account = new Account(['customer_id' => Yii::$app->user->id,
                                         'expire_id'   => AccountStatus::PKEY_ENTRY]);

            if($account->isNewRecord && ! $account->save())
                Yii::error(['failed to save Account',$account->firstErrors,$account->attributes]);
        }

        return $this->redirect(['index']);
    }

    /**
     * @brief
     * update customer's Bank Account
     */
    public function actionUpdate($id=null)
    {
        if(! Yii::$app->request->isPost)
            throw new \yii\web\ForbiddenHttpException("無効な操作です。結果データが不足しています。");

        $user  = Yii::$app->user->identity;
        $param = Yii::$app->request->post();
        $model = \common\models\ysd\RegisterRequest::finalize($param);

        if(! in_array($model->feedback,['ok','ng']) || ($model->userno != $user->id))
        {
            self::addFlash('warning',"登録手続きが終了したようですが、結果判定データが正しく取得できませんでした。申し訳ありませんが、最新の口座情報に更新されるまでしばらくお待ちください。（通常、24時間以内に反映されます）");
        }
        elseif('ok' === $model->feedback)
        {
            self::addFlash('success',"ありがとうございます。登録は完了しました。最新の口座情報に更新されるまでしばらくお待ちください。（通常、24時間以内に反映されます）");
        }
        else // ('ng' === $model->feedback)
        {
            if($user->ysdAccount)
                self::addFlash('error',"登録は完了しませんでした。なお、ご登録口座に変更はありません。");
            else
                self::addFlash('error',"登録は完了しませんでした。もう一度お手続きをおねがいします。");
        }

        return $this->redirect(['index']);
    }

    /**
     * 404 Not Found
     */
    public function actionDelete()
    {
        throw new \yii\web\NotFoundHttpException();
    }

    /**
     * addFlash(warning)
     */
    private function renderValidationError($model)
    {
        self::addFlash('warning', "手続きに必要な個人情報が不足しています。会員情報を編集してください"
                                . \yii\helpers\Html::ul($model->firstErrors)
                                . \yii\helpers\Html::a('編集するには、このページの「会員情報の確認・変更」をクリックします',['default/update'],['class'=>''])
        );
    }

    /* @return bool */
    private function sendYsdRequest()
    {
        $user  = Yii::$app->user->identity;
        $model = \common\models\ysd\RegisterRequest::startup($user);

        if(! $model->save())
        {
            $this->renderValidationError($model);
            return false;
        }

        // send http post to YSD server
        $conn = $this->module->ysdConnection;
        $conn->model = $model;

        if(! $conn->send() || ('ok' !== $model->feedback))
        {
            if(! $model->feedback)
                 $model->feedback = 'er';
            if(! $model->emsg)
                 $model->emsg = ($e = $conn->error) ?  $e->getMessage() : 'unexpected failure!!';

            $model->update();

            $msg = 'システムエラーが発生しました（収納機関との通信に失敗）。申し訳ありませんが、解決するまでしばらくお待ちください。';
            self::addFlash('error', $msg);
            Yii::error([$msg, $model->attributes], 'FATAL');

            return false;
        }

        if(! $model->update())
            Yii::error(['class'      => $model->className(),
                        'firstErrors'=> $model->firstErrors,
                        'attributes' => $model->attributes,
                        ], 'CRITICAL');

        return true;
    }

}
