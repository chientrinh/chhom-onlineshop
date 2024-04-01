<?php
namespace frontend\modules\signup\controllers;

use Yii;
use frontend\modules\signup\models\SignupForm;

/**
 * Site controller
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/signup/controllers/MigrateController.php $
 * $Id: MigrateController.php 2243 2016-03-13 00:58:06Z mori $
 */
abstract class MigrateController extends BaseController
{
    public function actionIndex()
    {
        return $this->redirect(['/'.$this->module->id]);
    }

    public function actionCreate($agreed=0)
    {
        return $this->redirect(['/'.$this->module->id]);
    }

    public function actionSearch($agreed=0)
    {
        if(! $agreed)
            throw new \yii\web\BadRequestHttpException("利用規約に同意の上、この画面へお進みください");

        $company = \common\models\Company::find()->where(['key'=>$this->id])->one();
        if(! $company)
            throw new \yii\web\BadRequestHttpException("提携社名が正しくありません");

        if(Yii::$app->request->isPost)
        {
            if(! $this->module->searchModel(Yii::$app->request->post()))
                Yii::$app->getSession()->addFlash('error', "当該の顧客情報は見つかりません");

            elseif($this->module->srcCustomer->wasMigrated())
                Yii::$app->getSession()->addFlash('error', "当該の顧客情報は移行を完了しています。"
                                                  . \yii\helpers\Html::a("ログイン", ['/site/login'])
                                                  . "して登録内容をご確認ください");
            else
                return $this->redirect(['update', 'token'=>$this->module->wtbCustomer->token]);
        }

        if('ecorange' === \yii\helpers\ArrayHelper::getValue(Yii::$app->request->queryParams, 'target'))
            $matrix = $this->module->matrix['ecorange'];

        return $this->render('/default/search', [
            'model'   => $this->module->finder,
            'company' => $this->module->srcCompany,
            'text'    => isset($matrix) ? (object)$matrix : (object)$this->module->matrix[$company->key],
        ]);
    }

    public function actionUpdate($token)
    {
        if(! $this->module->wtbCustomer || ($token != $this->module->wtbCustomer->token))
        {
            throw new \yii\web\BadRequestHttpException("トークンが無効です(時間切れまたはIP接続情報が一致しません)。お手数ですが移行手続きを最初からやり直してください。");
        }

        $form = new SignupForm();
        $form->load([$form->formName() => $this->module->dstCustomer ]); // apply dstCustomer

        $scenario = Yii::$app->request->post('scenario', null);
        if($form::SCENARIO_ZIP2ADDR == $scenario)
        {
            $form->load(Yii::$app->request->post());
            $form->scenario = 'zip2addr';
            $form->zip2addr();
        }
        elseif(Yii::$app->request->isPost)
        {
            $form->load(Yii::$app->request->post());
            $form->validate();
            foreach(['name01','name02','kana01','kana02'] as $attr)
                if($form->hasErrors($attr))
                    $form->clearErrors($attr); // ignore these errors

            if(! $form->hasErrors())
            {
                $form->detachBehavior('membercode');

                // Customer::save()
                if($customer = $form->signup(false)) // runValidation = false
                {
                    $this->module->appendAttributesAfterSave($customer);// should be fault safe

                    // refresh Customer model, to fetch newly added relations
                    if($model = \common\models\Customer::findOne($customer->customer_id))
                    {
                        // finally, define the grade of this customer
                        if($model->getDirtyAttributes(['grade_id']))
                            $model->save(false, ['grade_id']);

                        // send email to the customer (and support staff)
                        \common\components\sendmail\SignupMail::thankyou($model);

                        return $this->render('/default/thankyou', ['model'=>$model]);
                    }
                }
            }
        }

        return $this->render('update',[
            'model'      => $form,
            'srcCustomer'=> $this->module->srcCustomer,
            'dstCustomer'=> $this->module->dstCustomer,
            'text'       => (object)$this->module->matrix[$this->module->srcCompany->key],
        ]);
    }

}
