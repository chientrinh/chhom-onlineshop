<?php

namespace common\modules\invoice\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/invoice/controllers/FinanceController.php $
 * $Id: FinanceController.php 3848 2018-04-05 09:12:44Z mori $
 */

use Yii;
use \common\models\Invoice;

class FinanceController extends AdminController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        return true;

    }

    public function setViewOption($action)
    {
        if('index' != $action->id)
            return;

        $this->view->params['breadcrumbs'][] = ['label' => '入金確認','url'=>['index']];
    }


    public function actionIndex()
    {
        \yii\helpers\Url::remember();

        if(!isset(Yii::$app->session['invoice'])) {
           return $this->redirect(['admin/auth']);
        }

        $searchModel = new Invoice();
        $searchModel->load(Yii::$app->request->queryParams);
        $year  = Yii::$app->request->get('year', null);
        $month = Yii::$app->request->get('month',null);

        return $this->renderViaCache('index',[
            'searchModel'  => $searchModel,
            'dataProvider' => $this->loadProvider($year, $month, $searchModel),
            'year'         => $year ? $year : date('Y'),
            'month'        => $month ? $month : date('m'),
        ]);
    }

    public function actionPaid($id)
    {
        \yii\helpers\Url::remember();

        if(!isset(Yii::$app->session['invoice'])) {
            return $this->redirect(['admin/auth']);
        }

        $model = $this->findModel($id);

        if($model->paid())
        {
            if(Yii::$app->request->isAjax)
                return 'ok';
            Yii::$app->session->setFlash('success',"{$model->invoice_id}を入金済みに更新しました");
        }
        else
            Yii::$app->session->setFlash('error',"不適切な操作です。更新できません");

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionActivate($id)
    {
        \yii\helpers\Url::remember();

        if(!isset(Yii::$app->session['invoice'])) {
            return $this->redirect(['admin/auth']);
        }

        $model = $this->findModel($id);

        if($model->activate())
        {
            if(Yii::$app->request->isAjax)
                return 'ok';
            Yii::$app->session->setFlash('success',"{$model->invoice_id}を入金待ちに更新しました");
        }
        else
            Yii::$app->session->setFlash('error',"不適切な操作です。更新できません");

        return $this->redirect(Yii::$app->request->referrer);
    }

}
