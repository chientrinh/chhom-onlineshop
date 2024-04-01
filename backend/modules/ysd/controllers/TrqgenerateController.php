<?php

namespace backend\modules\ysd\controllers;

use Yii;
use common\models\Invoice;
use backend\modules\ysd\InvoiceMaker;
use common\models\Payment;
use common\models\Purchase;
use common\models\PurchaseStatus;
use common\models\ysd\TransferRequest;
use common\models\ysd\TransferRequestPackager;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use backend\models\CsvUploadForm;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
 
/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/views/rrs/view.php $
 * @version $Id: view.php 1961 2016-01-11 01:39:26Z mori $
 *
 * TrqController implements the CRUD actions for TransferRequest model.
 */
class TrqgenerateController extends \backend\controllers\BaseController
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

        $csv_model = new CsvUploadForm();

        if (Yii::$app->request->isPost) {
            $csv_model->file = UploadedFile::getInstance($csv_model, 'file');

            if ($csv_model->file && $csv_model->validate()) {
                $csv_model->file->saveAs(Yii::getAlias(sprintf('@runtime/%s.%s',$csv_model->file->baseName, $csv_model->file->extension)));
            }
        }

        $model  = new TransferRequest();
        $model->load(Yii::$app->request->get());

        $provider = $this->loadProvider($model, $year, $month);

        $cdate = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $year, $month)));
        $query = TransferRequest::find()->andWhere([
            'cdate' => $cdate]);

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
            'year'         => $year,
            'month'        => $month,
            'invoices'     => $query->count(),
            'csvModel'     => $csv_model,
        ]);
    }

    /**
     * Create Trq for year-month
     */
    public function actionCreate($year, $month)
    {
        $ret = $this->validate($year, $month);
        $params = Yii::$app->request->get();

        if(null === $ret || !$ret) {
            Yii::$app->session->addFlash('error',"請求対象はありませんでした");
            return $this->redirect(['index','year'=>$year,'month'=>$month]);
        }
        if($ret){
            // セットしたCSVデータから未回収分を取得する
            if(isset($params['csv_file'])) {
                $csv_file = new \SplFileObject(Yii::getAlias(sprintf('@runtime/%s',$params['csv_file'])));
                    while($array = $csv_file->fgetcsv()) {
                         if(count($array) == 40 && $array[17] != 0) {
                             // このレコードが、未落ち分として今回のマージ対象となる
                             $unsettled_customer_array[] = \intval($array[5]);
                             $unsettled_invoice_array[\intval($array[5])] = \intval($array[27]);
                             $unsettled_array[\intval($array[5])][] = ['purchase_id' => $array[35]];
                         }
                    }
            }

            // 請求書データから対象顧客・伝票を絞る
            $start_date = date('Y-m-01 00:00:00', strtotime(sprintf('%04d-%02d-01', $year, $month)));
            $end_date = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $year, $month)));
            $end_time = date('Y-m-t 23:59:59', strtotime(sprintf('%04d-%02d-01', $year, $month)));
            $basename = "trq-{$end_date}.csv";
            $record_count = 0;

            // 請求期間内の請求書データを全て取得する
            $invoices = Invoice::find()
                                 ->active()
                                 ->year($year)
                                 ->month($month)
                                 ->andWhere(['payment_id' => Payment::PKEY_DIRECT_DEBIT])
                                 ->select(['customer_id', 'due_total'])
                                 ->all();

            $invoice_debit_customers = ArrayHelper::getColumn($invoices, 'customer_id');
            $invoice_charges = ArrayHelper::map($invoices, 'customer_id', 'due_total');
            $query = Purchase::find()
                    ->active()
                    ->andWhere(['between', 'create_date', $start_date, $end_time])
                    ->andWhere(['payment_id' => [Payment::PKEY_DIRECT_DEBIT, Payment::PKEY_BANK_TRANSFER]])
                    ->andWhere(['IN', 'customer_id', $invoice_debit_customers])
                    ->orderBy([
                        'customer_id' => SORT_ASC,
                        'purchase_id' => SORT_ASC,
                    ]);

            $purchase_array = $query->all();

            $customer_array = $query->select('customer_id')->distinct()->all();
            $purchase_item_param = array();
 
            $csv_userData = "";

            $customer_array = ArrayHelper::getColumn($customer_array, 'customer_id');

            // 未落ち分の顧客IDリストをマージする
            if(isset($unsettled_customer_array)) {
                $merge = array_merge($customer_array, $unsettled_customer_array);
                $customer_array = array_values(array_unique($merge));
            }
            foreach($customer_array as $customer_id) {
                $total_charge = 0;
                $transferReq_param = ['cdate'  => $end_date,
                              'custno' => $customer_id];

                // まず請求書の金額をセット
                if(isset($invoice_charges[$customer_id]))
                    $total_charge = $invoice_charges[$customer_id];
               
                // 未落ち分を検索、あれば金額加算
                $unsettled_purchases = array();
                if(isset($unsettled_array[$customer_id])) {

                    $total_charge += $unsettled_invoice_array[$customer_id];

                //　未落ち伝票を検索
                    $unsettled_query = Purchase::find()
                        ->andWhere(['IN', 'purchase_id', ArrayHelper::getColumn($unsettled_array[$customer_id], 'purchase_id')])
                        ->orderBy([
                            'purchase_id' => SORT_ASC,
                        ]);
                    $unsettled_purchases = $unsettled_query->all();

                }


                if(! $model = TransferRequest::findOne($transferReq_param))
                {
                    $model = new TransferRequest($transferReq_param);
                    $model->charge = $total_charge;
                    $model->created_at = time();

                    if(! $model->save())
                        Yii::$app->session->addFlash('error', implode(';', $model->firstErrors)
                                                            . implode(';', $model->attributes));
                    

                    //print_r($csv_userData);
                }
                else if($model->charge != $total_charge) {
                     //       Yii::$app->session->addFlash('error', "既存の振替依頼が請求金額と一致しません"
                     //                                           . implode(';', $model->attributes));
                     $model->charge = $total_charge;

                    if(! $model->save())
                        Yii::$app->session->addFlash('error', implode(';', $model->firstErrors)
                                                            . implode(';', $model->attributes));
//                    $done++;
                }

                foreach($purchase_array as $purchase) {
                    if($purchase->customer_id == $customer_id) {
                        $csv_userData .= TransferRequestPackager::renderPurchaseItem($model, $purchase);
                        $record_count++;
                    }
                }

                // 未落ち分を検索
                if(isset($unsettled_purchases)) {
                    foreach($unsettled_purchases as $purchase) {
                        // 当時の請求締め日を設定する
                        $old_cdate = date('Y-m-t', strtotime($purchase->create_date));
                        $model->cdate = $old_cdate;
                        $model->save();
                        $csv_userData .= TransferRequestPackager::renderPurchaseItem($model, $purchase);
                        $record_count++;
                    }
                }
                // 処理が終わったら請求締め日を本来の日に戻す
                $cdate = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $year, $month)));
                $model->cdate = $cdate;
                $model->save();
                
            }

            $trq_query = TransferRequest::find()->andWhere(['cdate' => $end_date]);

            $widget = new TransferRequestPackager([
                    'dataProvider' => new \yii\data\ActiveDataProvider([
                        'query'      => $trq_query,
                        'pagination' => false,
                    ])
                ]);


            $widget->renderCsv($csv_userData, $record_count);

            Yii::$app->response->sendFile($widget->output, $basename, ['mimeType'=>'text/csv','inline'=>false]);
        } else{
            // 失敗
            Yii::$app->session->addFlash('error',"発行エラー：振替依頼を作成できませんでした");
            return $this->redirect(['index','year'=>$year,'month'=>$month]);
        }
    }
    
    private function validate($year, $month)
    {
        $model = new \common\models\DateForm([
            'year' => $year,
            'month'=> $month,
        ]);
        return ($year && $month && $model->validate());
    }

    /**
     * CSVファイルをアップロードして、処理する
     *
     **/
    public function actionCsvUpload()
    {
        $model = new CsvUploadForm();

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->file && $model->validate()) {                
                $model->file->saveAs('runtime/' . $model->file->baseName . '.' . $model->file->extension);
            }
        }

        return $this->render('upload', ['model' => $model]);
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
