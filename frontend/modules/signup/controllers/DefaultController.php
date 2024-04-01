<?php
namespace frontend\modules\signup\controllers;

use Yii;

/**
 * Site controller
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/signup/controllers/DefaultController.php $
 * $Id: DefaultController.php 3977 2018-08-03 05:18:15Z mori $
 */
class DefaultController extends BaseController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreate($agreed=0)
    {
        if(! $agreed)
            throw new \yii\web\BadRequestHttpException("利用規約に同意の上、この画面へお進みください");

        $model = new \frontend\modules\signup\models\SignupForm();
        //var_dump($model->rules());exit;

        $scenario = Yii::$app->request->post('scenario', null);
        if($model::SCENARIO_ZIP2ADDR == $scenario)
        {
            $model->load(Yii::$app->request->post());
            $model->scenario = 'zip2addr';
            $model->zip2addr();
        }
        elseif ($model->load(Yii::$app->request->post())) {

            $post = Yii::$app->request->post();
            // キャンペーンコード「0501」を入力した顧客はスペシャル会員にする（2018年5月中のみ）
//            if ($post['campaign_code'] && $post['campaign_code'] !== '0501') {
//                Yii::$app->getSession()->addFlash('error', "キャンペーンコードが正しくありません");
//                return $this->render('create', ['model' => $model]);
//            }

            if ($user = $model->signup()) {
                    Yii::$app->getSession()->addFlash('success', "登録が完了しました。");

                    $mailer = new \common\components\sendmail\SignupMail();
                    $mailer->thankyou($user);
//                    if ($post['campaign_code'] === '0501') {
//                        $customer_campaign = new \common\models\CustomerCampaign([
//                            'customer_id' => $user->customer_id
//                        ]);
//                        $customer_campaign->save();
//                    }

                    return $this->render('thankyou', [
                        'model' => $model,
                    ]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionSearch($agreed=0)
    {
        return $this->redirect(['/'.$this->module->id]);
    }

    public function actionUpdate($token='')
    {
        return $this->redirect(['/'.$this->module->id]);
    }

}
