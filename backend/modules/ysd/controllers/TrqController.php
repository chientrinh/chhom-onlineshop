<?php

namespace backend\modules\ysd\controllers;

use Yii;
use common\models\Invoice;
use common\models\Payment;
use common\models\ysd\TransferRequest;
use common\models\ysd\TransferRequestPackager;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/views/rrs/view.php $
 * @version $Id: view.php 1961 2016-01-11 01:39:26Z mori $
 *
 * TrqController implements the CRUD actions for TransferRequest model.
 */
class TrqController extends \backend\controllers\BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'振替依頼','url'=>['index']];

        return true;
    }

    /**
     * Lists all TransferRequest models.
     * @return mixed
     */
    public function actionIndex($year=null, $month=null)
    {
        if(!$year)
            $year  = (1 == date('m')) ? (date('Y') -1) : date('Y');
        if(!$month)
            $month = (1 == date('m')) ? 12 : (date('m') -1);

        $model  = new TransferRequest();
        $model->load(Yii::$app->request->get());

        $provider = $this->loadProvider($model, $year, $month);

        $cdate = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $year, $month)));
        $query = Invoice::find()->andWhere([
            'payment_id'  => Payment::PKEY_DIRECT_DEBIT,
            'target_date' => $cdate,
        ])->andWhere(['>', 'due_total', 0]);

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
            'year'         => $year,
            'month'        => $month,
            'invoices'     => $query->count(),
        ]);
    }

    /**
     * Create Trq for year-month
     */
    public function actionCreate($year, $month)
    {
        $cdate = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $year, $month)));
        $query = Invoice::find()->andWhere([
            'payment_id'  => Payment::PKEY_DIRECT_DEBIT,
            'target_date' => $cdate,
        ])->andWhere(['>', 'due_total', 0]);

        $done = 0;
        foreach($query->each() as $invoice)
        {
            $param = ['cdate'  => $cdate,
                      'custno' => $invoice->customer_id ];

            if(! $model = TransferRequest::findOne($param))
            {
                $model = new TransferRequest($param);
                $model->charge = $invoice->due_total;

                if(! $model->save())
                    Yii::$app->session->addFlash('error', implode(';', $model->firstErrors)
                                                        . implode(';', $model->attributes));
                $done++;
            }
            elseif($model->charge != $invoice->due_total)
                    Yii::$app->session->addFlash('error', "既存の振替依頼が請求金額と一致しません"
                                                        . implode(';', $model->attributes));
        }

        if(0 < $done)
            Yii::$app->session->setFlash('success',"{$done}件の振替依頼を発行しました");
        else
            Yii::$app->session->addFlash('error',"振替依頼を発行できませんでした");

        return $this->redirect(['index','year'=>$year,'month'=>$month]);
    }

    /**
     * Export CSV for year-month
     */
    public function actionExport($year, $month)
    {
        $cdate = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $year, $month)));
        $query = TransferRequest::find()->andWhere(['cdate' => $cdate]);

        $widget = new TransferRequestPackager([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query'      => $query,
                'pagination' => false,
            ])
        ]);
        $basename = "trq-{$cdate}.csv";

        $widget->run(); // save to widet->output

        Yii::$app->response->sendFile($widget->output, $basename, ['mimeType'=>'text/csv','inline'=>false]);
    }

    /**
     * Displays a single TransferRequest model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->validate();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the TransferRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TransferRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TransferRequest::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function loadProvider(TransferRequest $model, $year, $month)
    {
        $query = TransferRequest::find()
            ->andFilterWhere($model->attributes)
            ->andWhere([
            'EXTRACT(YEAR  FROM cdate)' => $year,
            'EXTRACT(MONTH FROM cdate)' => $month,
        ])->with(['customer','response']);

        return new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'enableMultiSort' => true,
                'defaultOrder' => ['trq_id'=>SORT_DESC],
            ],
        ]);
    }

}
