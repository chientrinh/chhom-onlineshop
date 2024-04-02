<?php

namespace common\modules\member\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/member/controllers/BaseController.php $
 * $Id: BaseController.php 3059 2016-10-30 06:10:53Z mori $
 */

use Yii;
use \yii\helpers\Html;

use \backend\models\Staff;
use \common\models\Branch;
use \common\models\CustomerFamily;
use \common\models\Membercode;
use \common\models\Membership;
use \common\models\Payment;
use \common\models\SearchMember;
use \common\modules\member\models\ViewForm;
use \common\modules\member\models\ToranokoApplicationForm;

abstract class BaseController extends \yii\web\Controller
{
    public $title;

    function init()
    {
        parent::init();
    }

    public function actionIndex()
    {
        $model    = new SearchMember();
        $provider = new \yii\data\ArrayDataProvider(['allModels'=>[]]);

        if($tel = Yii::$app->request->post('tel'))
        {
            $model->tel = $tel;

            if(true == $model->validate())
                $provider = $this->loadProvider($model->tel);
        }

        if(Yii::$app->response->cookies->has('ebisu-intra-request-json'))
            ;; // do something in the view file

        return $this->render('index',[
            'dataProvider' => $provider,
            'searchModel'  => $model,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view',[
            'model'=> $this->findModel($id)
        ]);
    }

    public function actionAttachMembercode($id, $mcode = null, $pw = null)
    {
        $customer = $this->findModel($id);
        $prev     = $customer->membercode->code;
        if(13 == strlen($mcode))
        {
            $substr10 = substr($mcode, 2, 10);
            $mcode    = $substr10;
        }

        if(strlen($mcode) && strlen($pw))
        {
            $model = \common\models\Membercode::find()->where(['code'=>$mcode,'pw'=>$pw])->one();

            if( $model &&
                \common\components\CustomerMigration::attachMembercode($customer, $model))
            {
                Yii::$app->session->addFlash('success', sprintf('会員証NOを更新しました(%s -> %s)', $prev, $mcode));
                return $this->redirect(['view', 'id' => $id]);
            }
            Yii::$app->session->addFlash('error',"会員証NO無効、またはPWが一致しません");
        }

        $model = new \common\models\Membercode(['code'=>$mcode, 'pw'=>$pw]);

        return $this->render('attach-membercode',['customer'=>$customer,'model'=>$model]);
    }

    protected function findModel($id)
    {
        if((! $model = ViewForm::findOne($id)) || $model->isExpired())
            throw new \yii\web\NotFoundHttpException();

        return $model;
    }

    private function loadProvider($key)
    {
        $query = ViewForm::find()->where(['CONCAT(tel01,tel02,tel03)' => $key]);

        if(! $query->exists())
        {
            if(13 == strlen($key)) // if barcode is given
                $key = substr($key, 2, 10);

            $q2 = Membercode::find()->where(['code' => $key]);

            if($q2->exists())
                $query = ViewForm::find()->where(['customer_id' => $q2->select('customer_id') ]);
        }
        $q3 = clone($query);
        $q4 = CustomerFamily::find()->where(['parent_id' => $q3->select('customer_id') ]);

        $query->orFilterWhere(['customer_id' => $q4->select('child_id') ]);

        return new \yii\data\ActiveDataProvider(['query'=> $query]);
    }

}
