<?php

namespace frontend\modules\profile\controllers;
use Yii;

use \common\models\Customer;
use \common\models\CustomerAddrbook;
use \common\models\Purchase;
use \common\models\MailLog;

/**
 * CRUD for dtb_customer_addrbook
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/controllers/HistoryController.php $
 * $Id: HistoryController.php 3970 2018-07-13 08:46:33Z mori $
 */

class HistoryController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $rule = [
            'actions'=> ['mail'],
            'allow'  => true,
            'roles'  => ['@'], // allow authenticated users
            'verbs'  => ['GET'],
        ];

        // append a rule
        array_push($behaviors['access']['rules'], $rule);
            
        return $behaviors;
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => "ご購入の履歴",'url'=>['index']];

        return true;
    }

    /**
     * display customer's profile 
     */
    public function actionIndex()
    {
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \common\models\Purchase::find()->where([
                'customer_id'=> Yii::$app->user->id,
            ])
            ->orderBy('purchase_id DESC'),
        ]);

        return $this->render('index', [
            'customer'     => $this->customer,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * display customer's purchase instance
     */
    public function actionView($id,$mail_id=0)
    {
        $model = Purchase::find()->where([
            'customer_id' => $this->customer->customer_id,
            'purchase_id' => $id,
        ])->one();
        if(! $model)
            throw new \yii\web\BadRequestHttpException("当該の注文IDは見当たりません");

        if($mail_id)
            return $this->actionViewMail($id,$mail_id);

        return $this->render('view', [ 'model' => $model ]);
    }

    /**
     * display customer's email instance
     */
    private function actionViewMail($id,$mail_id)
    {
        $model = MailLog::find()->where([
            'pkey'      => $id,
            'mailer_id' => $mail_id,
        ])->one();

        if(! $model)
            throw new \yii\web\BadRequestHttpException("当該のメール履歴は見当たりません");

        return $this->render('email', [ 'model' => $model ]);
    }

    /**
     * not implemented
     */
    public function actionCreate()
    {
        throw new \yii\web\NotFoundHttpException();
    }

    /**
     * do not allow update purchase by customer
     */
    public function actionUpdate($id)
    {
        throw new \yii\web\NotFoundHttpException();
    }

    /**
     * not implemented
     */
    public function actionDelete()
    {
        throw new \yii\web\NotFoundHttpException();
    }

}
