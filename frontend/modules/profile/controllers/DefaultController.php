<?php

namespace frontend\modules\profile\controllers;
use Yii;

/**
 * CRUD for dtb_customer
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/controllers/DefaultController.php $
 * $Id: DefaultController.php 3994 2018-08-17 08:17:22Z mori $
 */

class DefaultController extends BaseController
{
    const PATH_TEMPLATE = '@runtime/%s.%s';

    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
//            'pageCache' => [
//                'class' => 'yii\filters\HttpCache',
//                'only' => ['member-card'],
//                'lastModified' => function ($action, $params) {
//                    return strtotime(\common\models\Customer::find()->max('update_date'));
//                },
//            ],
        ]);
    }

    /**
     * display customer's profile
     */
    public function actionIndex()
    {
        return $this->render('index', [ 'model' => $this->customer ]);
    }

    /**
     * display customer's registration details
     */
    public function actionView($id=null)
    {
        return $this->render('view', [ 'model' => $this->customer ]);
    }

    /**
     * display customer's member card
     */
    public function actionMemberCard($target=null)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/login');
        }

        if('barcode' !== $target)
            return $this->render('member-card', [ 'model' => $this->customer ]);

        return $this->drawBarcode($this->findBarcode());
    }

    /**
     * not implemented
     */
    public function actionCreate($target='child')
    {
        if('child' != $target)
            throw new \yii\web\NotFoundHttpException();

        $model = new \frontend\modules\profile\models\CustomerChildForm();

        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $customer = new \common\models\Customer([
                'scenario' => \common\models\Customer::SCENARIO_CHILDMEMBER,
            ]);
            $customer->load($model->attributes, '');

            if($customer->save())
            {
                $family = new \common\models\CustomerFamily([
                    'parent_id' => Yii::$app->user->id,
                    'child_id'  => $customer->customer_id,
                ]);

                if($family->save())
                    return $this->redirect('view');
            }
        }

        return $this->render('child', [
            'model' => $model,
            'title' => "同居家族を追加",
        ]);
    }

    /**
     * @brief
     * update customer's profile when id === null
     * update customer's child   when id === child_id
     */
    public function actionUpdate($id=null,$pw=null)
    {
        if(null === $id)
            return $this->updateCustomer();

        if(null === $pw)
            return $this->updateChild($id);

        return $this->updateMembercode($id, $pw);


    }

    /* @return mixed */
    private function updateChild($id)
    {
        $model = \frontend\modules\profile\models\CustomerChildForm::findOne(['customer_id' => $id]);
        if(! $model || ! $model->parent || ($model->parent->id !== $this->customer->id))
            // this is Sematic URL attack!!
            throw new \yii\web\NotFoundHttpException(); // return with no detailed info

        if('expire' === Yii::$app->request->post('scenario'))
            $model->expire();

        if(Yii::$app->request->isPost)
        {
            $params = Yii::$app->request->post('CustomerChildForm');
            $model->birth = sprintf('%04d-%02d-%02d',
                                    \yii\helpers\ArrayHelper::getValue($params, 'birth_y'),
                                    \yii\helpers\ArrayHelper::getValue($params, 'birth_m'),
                                    \yii\helpers\ArrayHelper::getValue($params, 'birth_d'));
        }
        if(!$model->isExpired() && $model->load(Yii::$app->request->post()) && $model->dirtyAttributes && $model->save())
            return $this->redirect('view');

        return $this->render('child', [
            'model' => $model,
            'title' => "家族を編集",
        ]);
    }

    /* @return mixed */
    private function updateCustomer()
    {
        $model = \frontend\modules\profile\models\CustomerForm::findOne($this->customer->customer_id);

        if($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            if (Yii::$app->request->post('campaign_code') === '0501') {
                $customer_campaign = new \common\models\CustomerCampaign([
                    'customer_id' => $model->customer_id
                ]);
                $customer_campaign->save();
            }
            return $this->redirect('view');
        } else
            $model->validate();

        if('' === $model->password_hash)
            $model->addError('password1', "本パスワードが未設定です");

        return $this->render('update',['model'=>$model]);
    }

    /**
     * @brief update customer's membercode
     * @var $membercode string
     * @var $pw string
     */
    private function updateMembercode($code, $pw)
    {
        $model = \common\models\Membercode::find()
                ->where([
                    'code'       => $code,
                    'pw'         => $pw,
                    'customer_id'=> null,
                    'status'     => 0,
                ])
                ->one();
        if(! $model)
            $model = \common\models\Membercode::find()
                ->where([
                    'code'       => $code,
                    'pw'         => $pw,
                    'customer_id'=> Yii::$app->user->id,
                ])
                ->one();
        if(! $model)
            Yii::$app->session->addFlash('error','該当する会員証は見つかりませんでした');

        elseif(! $model->customer_id && $model->migratedModel)
            $ret = \common\components\CustomerMigration::syncModel(Yii::$app->user->identity, $model->migratedModel);
        else
            $ret = \common\components\CustomerMigration::attachMembercode(Yii::$app->user->identity, $model);

        if(isset($ret) && $ret)
            Yii::$app->session->addFlash('success',sprintf('会員証(%s)に更新しました',$model->code));

        return $this->redirect('index');
    }

    /*
     * @brief execute CustomerMigration::relateModel()
     * @return bool
     */
    private function relateModel(\common\models\Membercode $model)
    {
        $src = \common\models\webdb20\SearchCustomer::findOne($model->migrate_id);
        if(! $src)
            return false;

        if(\common\components\CustomerMigration::relateModel(Yii::$app->user->identity, $src))
            return true;

        return false;
    }

    /**
     * not implemented
     */
    public function actionDelete()
    {
        throw new \yii\web\NotFoundHttpException();
    }

    /**
     * @brief generate barcode image to a file and send it as binary
     */
    private function drawBarcode($barcode)
    {
        $format = 'png';

        $fullpath = Yii::getAlias(sprintf(self::PATH_TEMPLATE, $barcode, $format));

        if(! is_file($fullpath))
        \common\components\pommespanzer\barcode\Barcode::run($barcode, $fullpath, [
            'type'  => 'ean13', //'ean13' will be applied if (13 == strlen($text))
            'format'=> $format,
            'dpi'   => 72,
            'label' => '',
        ]);

        $response = \Yii::$app->getResponse();
        $response->sendFile($fullpath, basename($fullpath),['inline'=>true]);
        return $response->send();
    }

    private function findBarcode()
    {
        $mcode = $this->customer->membercode;
        if(! $mcode)
            throw new \yii\web\NotFoundHttpException();

        return $mcode->barcode;
    }
}
