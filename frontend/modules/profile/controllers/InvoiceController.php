<?php
namespace frontend\modules\profile\controllers;

use Yii;
use \common\models\Invoice;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/controllers/InvoiceController.php $
 * $Id: InvoiceController.php 3970 2018-07-13 08:46:33Z mori $
 */

class InvoiceController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'請求書の履歴','url'=>[$this->defaultAction]];

        return true;
    }

    /**
     * display customer's all Invoices
     */
    public function actionIndex()
    {
        $user     = Yii::$app->user->identity;
        $provider = new \yii\data\ActiveDataProvider([
            'query'      => Invoice::find()->active()
                                           ->andWhere(['customer_id' => $user->id]),
            'sort'       => ['defaultOrder' => ['target_date' => SORT_DESC]],
            'pagination' => ['pageSize' => 12],
        ]);

        return $this->render('index', [
            'provider' => $provider,
        ]);
    }

    /**
     * display customer's Invoice in pdf
     */
    public function actionView($id=null)
    {
        $model = $this->findModel($id);

        $widget = \common\widgets\doc\invoice\InvoiceDocument::begin([
            'model' => $model,
        ]);

        $filename = $widget->renderPdf();

        $inline = true;
        $mime   = 'application/pdf';
        Yii::$app->response->setDownloadHeaders(basename($filename), $mime, $inline);
        return Yii::$app->response->sendFile($filename, $inline);
    }

    /**
     * 404 Not Found
     */
    public function actionCreate()
    {
        throw new \yii\web\NotFoundHttpException();
    }

    /**
     * 404 Not Found
     */
    public function actionDelete()
    {
        throw new \yii\web\NotFoundHttpException();
    }

    /**
     * 404 Not Found
     */
    public function actionUpdate($id)
    {
        throw new \yii\web\NotFoundHttpException();
    }

    private function findModel($id)
    {
        $model = Invoice::findOne([
            'customer_id' => Yii::$app->user->id,
            'invoice_id'  => $id,
        ]);

        if(! $model)
            throw new \yii\base\UserException("ご指定の請求書は見つかりません");

        if(! $model->validate())
            throw new \yii\base\UserException("ご指定の請求書は無効のようです");

        return $model;
    }

}

