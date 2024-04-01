<?php

namespace backend\modules\casher\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/controllers/BaseController.php $
 * $Id: BaseController.php 4257 2020-04-27 05:09:53Z mori $
 */

use Yii;
use \yii\helpers\ArrayHelper;
use \common\models\Branch;
use \common\models\Membership;
use \common\models\Payment;
use \common\models\Product;
use \common\models\ProductMaster;
use \common\models\PurchaseDelivery;
use \common\models\Vegetable;
use \common\models\Recipe;
use \common\models\SearchRecipe;
use \common\models\Campaign;
use \backend\modules\casher\models\Command;
use \common\components\cart\ComplexRemedyForm;
use \common\widgets\doc\purchase\ChainstoreDocument;
use \common\widgets\doc\purchase\PurchaseDocument;
use \common\models\Stock;
use backend\models\Staff;
use common\models\RemedyVial;

abstract class BaseController extends \yii\web\Controller
{
    public $nav;
    public $nav2;
    public $campaigns;
    public $stock;

    const SESSION_KEY_RECIPE = 'recipe-in-casher';
    const SESSION_KEY_RECIPE_DEL = 'recipe-del-in-casher';
    const CSV_NAME_YUPACK    = '\common\widgets\doc\purchase\YuPackPrintCsv';
    const CSV_NAME_YAMATO    = '\common\widgets\doc\purchase\YamatoCsv';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['worker'], // allow worker to do everything
                    ],
                    [
                        'allow' => true,
                        'roles' => ['tenant'],
                        'controllers' => ['casher/trose'],
                    ],
                ],
            ],
        ];
    }

    public function init()
    {
        parent::init();

        $this->stock = new \common\models\Stock();
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->initBreadcrumbs($action);
        $this->initNav($action);
        if(!$this->campaigns)
            $this->campaigns = $this->setCampaignsPulldown();


        if(in_array($action->id, ['view','print']))
            return true;

        if(! $this->module->branch && ('setup' != $action->id)) {
            // ログインした従業員の「役割」中最初の拠点を取り出す
            return $this->redirect(['default/setup', 'id' => Yii::$app->user->identity->roles[0]->branch_id]);
        }

        return true;
    }

    public function afterAction($action, $result)
    {
        if(! parent::afterAction($action, $result))
            return false;

        if($this->module->purchase)
        {
            $this->module->purchase->mergeItems();
            $this->module->purchase->compute(false);
        }

        return $result;
    }

    abstract public function actionIndex();

    public function actionPrint($id = null, $format='html', $target='auto')
    {
        if(! $id && ! $selection = Yii::$app->request->get('selection'))
            throw new \yii\web\BadRequestHttpException('注文IDが指定されていません');

        if($id) // print single model
        {
            $model  = $this->findModel($id);
            $widget = new PurchaseDocument(['model'=>$model,'target'=>$target]);

            if('chainstore'==$target)
            $widget = new ChainstoreDocument(['model'=>$model]);

            $html   = $widget->run();
            $pdf    = $widget->pdffile;

            if(('pdf' == $format) && is_file($pdf))
                return Yii::$app->response->sendFile($pdf, basename($pdf), ['inline'=>true]);

            $this->layout = '/none';
            return $this->renderContent($html);
        }

        $models   = \common\models\Purchase::findAll(['purchase_id'=>$selection]);
                foreach($models as $model)
        {
            if($model->shipped)
                continue;
            // 未発送のレコードについて、発送日を登録する
            $model->shipping_date = date('Y-m-d H:i:s');
            $model->save();
        }

        $basename = Yii::$app->request->get('basename', $this->module->getPrintBasename());

        $this->batchPrint($models, $basename);

        // update Purchase::shipped to '1'
        foreach($models as $model)
        {
            if($model->shipped)
                continue;

            $model->shipped = 1;
            $model->status  = \common\models\PurchaseStatus::PKEY_PAYING;
            $model->save();
        }
    }

    /**
     * 注文一覧画面のCSV出力機能（ヤマト用）
     *
     * @param  array  $id　注文ID
     * @return 出力CSV
     */
    public function actionPrintCsv($id = null)
    {
        if(! $id && ! $selection = Yii::$app->request->get('selection'))
            throw new \yii\web\BadRequestHttpException('注文IDが指定されていません');
        if($id)
            $selection = [ $id, ];

        $basename = Yii::$app->request->get('basename', $this->module->getPrintBasename());

        $query    = \common\models\Purchase::find()->where(['purchase_id'=>$selection]);

        return $this->printYamatoCsv($query->all(), $basename);
    }

    /**
     * 注文一覧画面のCSV出力機能（ゆうプリ用）
     *
     * @param  array  $id　注文ID
     * @return 出力CSV
     */
    public function actionPrintCsvForYuPrint($id = null)
    {
        if(! $id && ! $selection = Yii::$app->request->get('selection'))
            throw new \yii\web\BadRequestHttpException('注文IDが指定されていません');
        if($id)
            $selection = [ $id, ];

        $models   = \common\models\Purchase::findAll(['purchase_id'=>$selection]);
        $basename = Yii::$app->request->get('basename', $this->module->getPrintBasename());

        return $this->printYuPrintCsv($models, $basename);
    }


    /*
     * 一覧画面でラベルボタンからリクエストされるアクション。レメディーラベルをプリントする
     */
    public function actionPrintRemedyLabel($id = null)
    {
        if(! $id && ! $selection = Yii::$app->request->get('selection'))
            throw new \yii\web\BadRequestHttpException('注文IDが指定されていません');
        if($id)
            $selection = [ $id, ];

        $models   = \common\models\Purchase::findAll(['purchase_id'=>$selection]);
        $basename = Yii::$app->request->get('basename', $this->module->getPrintBasename());
//        return $this->printRemedyLabel($models, $basename);
//                $query    = \common\models\Purchase::find()->where(['purchase_id'=>$selection]);
        return $this->printRemedyCsv($models, $basename);
    }

    public function actionPrintLabel($id = null, $target = 'remedy')
    {
        if(! $id && ! $selection = Yii::$app->request->get('selection'))
            throw new \yii\web\BadRequestHttpException('注文IDが指定されていません');
        if($id)
            $selection = [ $id, ];

        $models   = \common\models\Purchase::findAll(['purchase_id'=>$selection]);
        $basename = Yii::$app->request->get('basename', $this->module->getPrintBasename());

        if('remedy' == $target)
//            return $this->printRemedyLabel($models, $basename);
            return $this->printRemedyCsv($models, $basename);

        elseif('price' == $target)
            return $this->printProductPrices($models, $basename);

        //elseif('sticker' == $target)
        return $this->printProductStickers($models, $basename);
    }

    private function batchPrint($models, $basename)
    {
        if(! $pdffile = PurchaseDocument::getMergedPdf($models))
            throw new \yii\web\ServerErrorHttpException('application failed to generate united pdf');
        if(! is_file($pdffile))
            echo 'was not generated';

        $basename .= '.pdf';

        Yii::$app->response->sendFile($pdffile, $basename, ['inline'=>false]);
        Yii::$app->response->send();
    }

    protected function printRemedyLabel($models, $basename)
    {
        $basename .= '.html';
        $html      = [];

        foreach($models as $model)
            $html[] = \common\widgets\doc\purchase\RemedyLabels::widget([
                'model' => $model,
            ]);

        $html = implode('', $html);

        if(0 === strlen($html))
            $html = "指定された注文IDに滴下レメディーはありませんでした: \n"
                  . implode(';', \yii\helpers\ArrayHelper::getColumn($models,'purchase_id'));

        Yii::$app->response->sendContentAsFile($html, $basename, ['inline'=>false]);
        Yii::$app->response->send();
    }

    /**
     * レメディーラベル用CSVを出力
     */
    protected function printRemedyCsv($models, $basename)
    {
        $basename = "remedy_".$basename;
        $basename .= '.csv';
        $csv = [];

        $widget = new \common\widgets\doc\remedy\RemedyLabelCsv();

        foreach($models as $model) {
            $csv_record = \common\widgets\doc\purchase\RemedyLabelCsvs::widget([
                'model' => $model,
            ]);
            if($csv_record && count($csv_record) != 0) {
                $csv[] = $csv_record;
            }
        }

        $csv = implode('', $csv);

        if(0 === strlen($csv))
            $csv = "指定された注文IDのすべてに滴下レメディーの情報がありませんでした: \n"
                  . implode(';', \yii\helpers\ArrayHelper::getColumn($models,'purchase_id'));

        $csv .= "EjectCut";
        $csv = mb_convert_encoding($csv, 'SJIS-WIN', 'UTF-8');
        Yii::$app->response->charset = 'shift_jis';
        Yii::$app->response->sendContentAsFile($csv, $basename, ['inline'=>false, 'mimeType'=>'text/csv']);
        Yii::$app->response->send();
    }



    protected function printProductPrices($models, $basename)
    {
        $widget  = new \common\widgets\doc\purchase\ProductStickers([
            'models'      => $models,
            'fieldConfig' => ['layout' => '{name}{price}{barcode}'],
        ]);
        $output  = $widget->run();

        $inline  = true;
        $mime    = 'application/pdf';
        Yii::$app->response->setDownloadHeaders(basename($basename), $mime, $inline);
        return Yii::$app->response->sendFile($output, $inline);
    }

    protected function printProductStickers($models, $basename)
    {
        $widget  = new \common\widgets\doc\purchase\ProductStickers(['models'=>$models]);
        $output  = $widget->run();

        $inline  = true;
        $mime    = 'application/pdf';
        Yii::$app->response->setDownloadHeaders(basename($basename), $mime, $inline);
        return Yii::$app->response->sendFile($output, $inline);
    }

    private function printYamatoCsv($models, $basename)
    {
        $basename = "yamato_".$basename;
        $basename .= '.csv';
        $csv = [];

        $widget = new \common\widgets\doc\purchase\YamatoCsv();

        if(0 < strlen(Yii::$app->request->get('header'))) // e.g., index.php/casher/ropponmatsu/print-csv?id=14&header=on
            $csv[]  = implode(',', $widget->header) . $widget->eol;

        foreach($models as $model)
            $csv[] = \common\widgets\doc\purchase\YamatoCsv::widget([
                'model' => $model,
            ]);

        $csv = implode('', $csv);

        if(0 === strlen($csv))
            $csv = "指定された注文IDのすべてに配送先の指定がありませんでした: \n"
                  . implode(';', \yii\helpers\ArrayHelper::getColumn($models,'purchase_id'));

        $csv = mb_convert_encoding($csv, 'SJIS-WIN', 'UTF-8');

        Yii::$app->response->charset = 'shift_jis';
        Yii::$app->response->sendContentAsFile($csv, $basename, ['inline'=>false, 'mimeType'=>'text/csv']);
        Yii::$app->response->send();
    }

    private function printYuPrintCsv($models, $basename)
    {
        $this->setCsv(self::CSV_NAME_YUPACK, $models, $basename);
    }

    private function setCsv($widgetName, $models, $basename)
    {
        // 内部的な受け渡しにもかかわらず想定外の指定が来た場合はシステムエラーとする
        if (! in_array($widgetName, [self::CSV_NAME_YAMATO, self::CSV_NAME_YUPACK]))
            throw new \yii\web\ServerErrorHttpException('システムエラーが発生しました。システム管理者にお問い合わせください。');

        $basename = "yupri_".$basename;
        $basename .= '.csv';
        $csv = [];

        $widget = new $widgetName();

        if(0 < strlen(Yii::$app->request->get('header')))
            $csv[]  = implode(',', $widget->header) . $widget->eol;

        foreach($models as $model)
            $csv[] = $widget::widget([
                'model' => $model,
            ]);

        $csv = implode('', $csv);

        if(0 === strlen($csv))
            $csv = "指定された注文IDのすべてに配送先の指定がありませんでした: \n"
                  . implode(';', \yii\helpers\ArrayHelper::getColumn($models,'purchase_id'));

        $csv = mb_convert_encoding($csv, 'SJIS-WIN', 'UTF-8');

        Yii::$app->response->charset = 'shift_jis';
        Yii::$app->response->sendContentAsFile($csv, $basename, ['inline'=>false, 'mimeType'=>'text/csv']);
        Yii::$app->response->send();
    }

    public function actionStat()
    {
        $stat = new \backend\models\stat\PurchaseStatistics([
            'branch_id'   => $this->module->branch->branch_id,
            'company_id'  => Yii::$app->request->get('company'),
            'start_date'  => Yii::$app->request->get('start_date'),
            'end_date'    => Yii::$app->request->get('end_date'),
            'class'       => Yii::$app->request->get('class'),
        ]);

    return $this->render('stat', [
            'branch' => $this->module->branch,
            'model'  => $stat,
        ]);
    }

    /**
     * 売上集計データをCSV出力
     * @param null
     *
     */
    public function actionPrintStat()
    {
        $branch_id = $this->module->branch->branch_id;
        $company_id = Yii::$app->getRequest()->getQueryParam('company');
        $start_date = Yii::$app->getRequest()->getQueryParam('start_date');
        $end_date =  Yii::$app->getRequest()->getQueryParam('end_date');

        $stat = new \backend\models\stat\PurchaseStatistics([
            'branch_id'   => $branch_id,
            'company_id'  => $company_id,
            'start_date'  => $start_date,
            'end_date'    => $end_date,
        ]);

        $models = $stat->itemProvider->models;

        //$models   = \common\models\Purchase::findAll(['purchase_id'=>$selection]);
            //$basename = Yii::$app->request->get('basename', $this->module->getPrintBasename());
        $company_model = \common\models\Company::find()->where(['company_id' => $company_id])->one();

        if($company_model) {
            $company_initial = $company_model->key;
        } else {
            $company_initial = "all";
        }
        $basename = $this->module->branch->name."_".$company_initial."_".date('Ymd',strtotime($start_date))."-".date('Ymd', strtotime($end_date));
            return $this->printStatCsv($models, $basename);

    }

    /**
     * ライブ配信チケット売上データをCSV出力
     * @param null
     * 注文番号	拠点id	購入日	顧客id	顧客名	メールアドレス	電話番号	商品名	税込金額	支払方法
     *　（注）branch_id = 16 CHhom 東京 教育推進部だけが使用する想定
     */
    public function actionLiveDataPrintStat()
    {
        //$start_date = "2021-04-01 12:00:00";
         $start_date = "2023-11-01 00:00:00";
        $end_date = date('Y-m-d 23:59:59');

        $stat = new \backend\models\stat\PurchaseStatistics([
            // 'branch_id'   => $branch_id,
            // 'company_id'  => $company_id,
            'start_date'  => $start_date,
            'end_date'    => $end_date,
        ]);

        $models = $stat->liveDataItemProvider->models;

        $basename = date('Ymd',strtotime($start_date))."-".date('Ymd', strtotime($end_date));
            return $this->printLiveDataStatCsv($models, $basename);

    }


    /**
     * 売上データ（ヘッダー）をCSV出力
     * @param null
     *
     */
    public function actionHeaderPrintStat()
    {
        $branch_id = $this->module->branch->branch_id;
        $company_id = Yii::$app->getRequest()->getQueryParam('company');
        $start_date = Yii::$app->getRequest()->getQueryParam('start_date');
        $end_date =  Yii::$app->getRequest()->getQueryParam('end_date');

        $stat = new \backend\models\stat\PurchaseStatistics([
            'branch_id'   => $branch_id,
            'company_id'  => $company_id,
            'start_date'  => $start_date,
            'end_date'    => $end_date,
        ]);

        $models = $stat->headerItemProvider->models;

        //$models   = \common\models\Purchase::findAll(['purchase_id'=>$selection]);
            //$basename = Yii::$app->request->get('basename', $this->module->getPrintBasename());
        $company_model = \common\models\Company::find()->where(['company_id' => $company_id])->one();

        if($company_model) {
            $company_initial = $company_model->key;
        } else {
            $company_initial = "all";
        }
        $basename = $this->module->branch->name."_".$company_initial."_".date('Ymd',strtotime($start_date))."-".date('Ymd', strtotime($end_date));
            return $this->printHeaderStatCsv($models, $basename);

    }


    /**
     * 売上集計データをCSV出力
     * @param null
     *
     */
    public function actionDetailedPrintStat()
    {
        $branch_id = $this->module->branch->branch_id;
    $company_id = Yii::$app->getRequest()->getQueryParam('company');
        $start_date = Yii::$app->getRequest()->getQueryParam('start_date');
        $end_date =  Yii::$app->getRequest()->getQueryParam('end_date');

    $stat = new \backend\models\stat\PurchaseStatistics([
            'branch_id'   => $branch_id,
            'company_id'  => $company_id,
            'start_date'  => $start_date,
            'end_date'    => $end_date,
        ]);

        $models = $stat->itemProvider->models;

        //$models   = \common\models\Purchase::findAll(['purchase_id'=>$selection]);
            //$basename = Yii::$app->request->get('basename', $this->module->getPrintBasename());
        $company_model = \common\models\Company::find()->where(['company_id' => $company_id])->one();

        if($company_model) {
            $company_initial = $company_model->key;
        } else {
            $company_initial = "all";
        }
        $basename = $this->module->branch->name."_".$company_initial."_detailed_".date('Ymd',strtotime($start_date))."-".date('Ymd', strtotime($end_date));
            return $this->printDetailedStatCsv($models, $basename);

    }

    /**
     * actionStat で取得した売上集計データをCSVとして出力する
     * @param unknown $models
     * @param unknown $basename
     */
    public function printStatCsv($models, $basename)
    {

    $basename = "stat_".$basename;
        $basename .= '.csv';
        $csv = [];
        $widget = new \common\widgets\doc\purchase\StatCsv();

//      if(0 < strlen(Yii::$app->request->get('header'))) // e.g., index.php/casher/ropponmatsu/print-csv?id=14&header=on
            $csv[]  = implode(',', $widget->header) . $widget->eol;

            foreach($models as $model)
                $csv[] = $widget::widget([
                        'model' => $model,
                ]);

                $csv = implode('', $csv);

                if(0 === strlen($csv))
                    $csv = "指定された期間に売上データがありませんでした: \n"
                            . implode(';', \yii\helpers\ArrayHelper::getColumn($models,'purchase_id'));

                            $csv = mb_convert_encoding($csv, 'SJIS-WIN', 'UTF-8');

    Yii::$app->response->charset = 'shift_jis';
        Yii::$app->response->sendContentAsFile($csv, $basename, ['inline'=>false, 'mimeType'=>'application/csv']);
        Yii::$app->response->send();

    }

    /**
     * actionHeaderPrintStat で取得した売上ヘッダーデータをCSVとして出力する
     * @param unknown $models
     * @param unknown $basename
     */
    public function printHeaderStatCsv($models, $basename)
    {

        $basename = "header_".$basename;
        $basename .= '.csv';
        $csv = [];
        $widget = new \common\widgets\doc\purchase\HeaderStatCsv();

            $csv[]  = implode(',', $widget->header) . $widget->eol;

            foreach($models as $model)
                $csv[] = $widget::widget([
                        'model' => $model,
                ]);

                $csv = implode('', $csv);

                if(0 === strlen($csv))
                    $csv = "指定された期間に売上データがありませんでした: \n"
                            . implode(';', \yii\helpers\ArrayHelper::getColumn($models,'purchase_id'));

                            $csv = mb_convert_encoding($csv, 'SJIS-WIN', 'UTF-8');

        Yii::$app->response->charset = 'shift_jis';
        Yii::$app->response->sendContentAsFile($csv, $basename, ['inline'=>false, 'mimeType'=>'application/csv']);
        Yii::$app->response->send();
    }

    /**
     * actionLiveDataPrintStat で取得した売上ヘッダーデータをCSVとして出力する
     * @param unknown $models
     * @param unknown $basename
     */
    public function printLiveDataStatCsv($models, $basename)
    {

        $basename = "liveData_".$basename;
        $basename .= '.csv';
        $csv = [];
        $widget = new \common\widgets\doc\purchase\LiveDataStatCsv();

            $csv[]  = implode(',', $widget->header) . $widget->eol;

            foreach($models as $model)
                $csv[] = $widget::widget([
                        'model' => $model,
                ]);

                $csv = implode('', $csv);

                if(0 === strlen($csv))
                    $csv = "指定された期間に売上データがありませんでした: \n"
                            . implode(';', \yii\helpers\ArrayHelper::getColumn($models,'purchase_id'));

                            $csv = mb_convert_encoding($csv, 'SJIS-WIN', 'UTF-8');

        Yii::$app->response->charset = 'shift_jis';
        Yii::$app->response->sendContentAsFile($csv, $basename, ['inline'=>false, 'mimeType'=>'application/csv']);
        Yii::$app->response->send();

    }

    /**
     * actionDetailediPrintStat で取得した売上明細データをCSVとして出力する
     * @param unknown $models
     * @param unknown $basename
     */
    public function printDetailedStatCsv($models, $basename)
    {

        $basename = "detail_".$basename;
        $basename .= '.csv';
        $csv = [];
        $widget = new \common\widgets\doc\purchase\DetailStatCsv();

//      if(0 < strlen(Yii::$app->request->get('header'))) // e.g., index.php/casher/ropponmatsu/print-csv?id=14&header=on
            $csv[]  = implode(',', $widget->header) . $widget->eol;

            foreach($models as $model)
                $csv[] = $widget::widget([
                        'model' => $model,
                ]);

                $csv = implode('', $csv);

                if(0 === strlen($csv))
                    $csv = "指定された期間に売上データがありませんでした: \n"
                            . implode(';', \yii\helpers\ArrayHelper::getColumn($models,'purchase_id'));

                            $csv = mb_convert_encoding($csv, 'SJIS-WIN', 'UTF-8');

        Yii::$app->response->charset = 'shift_jis';
        Yii::$app->response->sendContentAsFile($csv, $basename, ['inline'=>false, 'mimeType'=>'application/csv']);
        Yii::$app->response->send();

    }


    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('//purchase/view', ['model' => $model]);
    }

    public function actionReceipt($id, $print_name_flg = 0)
    {
        $html  = \common\widgets\doc\purchase\Receipt::widget([
            'model' => $this->findModel($id),
            'print_name_flg' => $print_name_flg,
        ]);

        $this->layout = '/none';
        return $this->renderContent($html);
    }

    public function actionAllReceipt($id = null, $format='html', $target='auto')
    {
        if(! $id && ! $selection = Yii::$app->request->get('selection'))
            throw new \yii\web\BadRequestHttpException('注文IDが指定されていません');

            if($id) // print single model
            {
            $html  = \common\widgets\doc\purchase\Receipt::widget([
                'model' => $this->findModel($id),
                'print_name_flg' => 1,
            ]);

            $this->layout = '/none';
            return $this->renderContent($html);
        }
        $merged_html = "";

        $models   = \common\models\Purchase::findAll(['purchase_id'=>$selection]);
        foreach ($models as $model) {
                        $html  = \common\widgets\doc\purchase\Receipt::widget([
                'model' => $model,
                'print_name_flg' => 1,
            ]);

            $this->layout = '/none';
            $merged_html .= $html;
        }

        return $this->renderContent($merged_html);
    }

    public function actionCreate()
    {
        if(isset($this->module->purchase->purchase_id) && 0 < $this->module->purchase->purchase_id) // now editing exsisting record
            return $this->redirect(['update', 'id'=>$this->module->purchase->purchase_id]);

        // キャンペーンのデフォルトセットはこちらで
        $campaign = Campaign::getCampaignOneWithBranch($this->module->branch->branch_id);
        if($campaign && !$this->module->purchase->campaign_id && 0 == count($this->module->purchase->items))
            $this->module->purchase->setCampaign($campaign);

        $this->module->purchase->validate();
        $this->module->purchase->compute(false);
        return $this->render('create', ['model' => $this->module->purchase]);
    }

    public function actionUpdate($id,$target=null)
    {
        if('delivery' == $target)
            return $this->updateDelivery();

        $model = $this->findModel($id);

        if($id != $this->module->purchase->purchase_id)
        {

            $form           = \common\models\PurchaseForm::findOne($id);
            $form->items    = $model->items;
            // オリジナルレメディー、特別レメディーが含まれているかチェックし変換する
            $form->items = $this->convertToComplexRemedyItem($id, $form->items);
            $form->delivery = $model->delivery;

            $this->module->purchase = $form;
            $this->module->reloadBuffer();
        }

        $this->module->purchase->validate();
        $this->module->purchase->compute(false);

        // 紐づく適用書の確認
        $rids = ArrayHelper::getcolumn(\common\models\LtbPurchaseRecipe::find()->where(['purchase_id' => $id])->select('recipe_id')->all(), 'recipe_id');
        $rids_session = Yii::$app->session->get(self::SESSION_KEY_RECIPE, []);
        $rids_del_session = Yii::$app->session->get(self::SESSION_KEY_RECIPE_DEL, []);

        if(count($rids_session) == 0 && count($rids_del_session) == 0) {
            $rids = ArrayHelper::getcolumn(\common\models\LtbPurchaseRecipe::find()->where(['purchase_id' => $id])->select('recipe_id')->all(), 'recipe_id');
            Yii::$app->session->set(self::SESSION_KEY_RECIPE, $rids);
        }
        return $this->render('update', ['model'=> $this->module->purchase]);
    }

    private function updateDelivery()
    {
        if(! Yii::$app->request->isPost)
            return $this->render('update-delivery',['model' => $this->module->purchase->delivery]);

        $model = $this->module->purchase->delivery;
        $model->load(Yii::$app->request->post());
        if(! $model->expect_time)
             $model->expect_time = null;

        if(! $model->expect_date)
             $model->expect_date = null;

        if(('zip2addr' == Yii::$app->request->post('scenario')) &&
           ($param = \common\models\Zip::zip2addr($model->zip01, $model->zip02)))
        {
            $model->pref_id = $param->pref_id;
            $model->addr01  = array_shift($param->addr01);
        }
        $model->validate();
        $model->clearErrors('purchase_id');

        if($model->hasErrors())
            return $this->render('update-delivery',['model' => $model]);

        $this->module->purchase->delivery = $model;
        return $this->redirect(['create']);
    }

    /*
     * 共通メソッド 顧客・商品の適用・レジへの追加を行なう
     * string $target 適用種類( barcode | customer | product | recipe | remedy | veg | quantity | reduce | summary | reset )
     *      barcode     バーコード（顧客番号・商品バーコード）
     *      customer    顧客情報
     *      product     通常商品
     *      recipe      適用書レメディー
     *      remedy      レメディー商品
     *      veg         野菜
     *      quantity    数量増量
     *      reduce      数量減量
     *      summary　    一覧
     *      reset       初期化
     * string $auto_customer_create 顧客自動作成（1：ON | 0：OFF）
     */
    public function actionApply($target, $auto_customer_create = 0)
    {
        if('multi-apply' == $target)
        {
            $data = Yii::$app->request->post();
            foreach($data as $item) {
                if(isset($item['rid']))
                    $this->addRemedy($item['rid'], $item['pid'], $item['vid'], false, $item['qty']);
                if(isset($item['id']) && $item['target'] != "recipe")
                    $this->applyProduct(\common\models\Product::findOne($item['id']), $item['qty'], $item['target']);
                if($item["target"] == "veg")
                    $this->applyVegetable($item['vid'],$item['qty'], $item['price']);
            }
            return "ok"; 
            //return $this->redirect(Yii::$app->request->referrer);
        }
        if('barcode'  == $target)
        {
            $barcode = Yii::$app->request->get('barcode');
            // 顧客自動作成について確認
            $auto_customer_create = Yii::$app->request->get('auto_customer_create');

            if((0 == strlen($barcode)) && $this->module->purchase->validate())
                return $this->actionFinish();

            return $this->applyBarcode($barcode, $auto_customer_create);
        }

        elseif('customer' == $target)
            $this->applyCustomer(Yii::$app->request->get('id'));

        elseif(in_array($target, ['product', 'products', 'popular', 'cosme_food', 'book_dvd', 'restaurant', 'trose', 'other', 'agent']))
            return $this->applyProduct(
                \common\models\Product::findOne(Yii::$app->request->get('id')),
                Yii::$app->request->get('qty'),
                $target
            );

        elseif('recipe' == $target)
            $this->applyRecipe(Yii::$app->request->get('id'));

        elseif('recipe_del' == $target)
            $this->applyRecipeDel(Yii::$app->request->get('id'));


        elseif(in_array($target, ['tincture', 'flower', 'flower2', 'remedy', 'all_remedy']))
            return $this->applyRemedy(
                Yii::$app->request->get('rid'),
                Yii::$app->request->get('pid'),
                Yii::$app->request->get('vid'),
                Yii::$app->request->get('stock'),
                Yii::$app->request->get('qty'),
                $target
            );

        elseif(in_array($target, ['kit', 'modular']))
            return $this->applyKitSet($target);

        elseif('veg'  == $target)
            return $this->applyVegetable(
                Yii::$app->request->get('vid'),
                Yii::$app->request->get('qty'),
                Yii::$app->request->get('price')
            );

        elseif('quantity' == $target)
        {
            if($json = $this->applyQuantity(Yii::$app->request->get()))
                return $json;
        }

        elseif('price' == $target)
        {
            if($json = $this->applyPrice(Yii::$app->request->get()))
                return $json;
        }

        elseif('reduce'   == $target)
            $this->module->purchase->applyReduce(Yii::$app->request->get());

        elseif('delivery' == $target)
            return $this->applyDelivery(Yii::$app->request->get('id'));

        elseif('summary'  == $target)
            $this->applySummary(Yii::$app->request->get());

        elseif('reset'  == $target)
            $this->module->clearBuffer();

        else
            throw new \yii\web\NotFoundHttpException("$target unknown");

        $this->module->purchase->compute(false);


        $print_name_flg = Yii::$app->request->get('print_name_flg');

        if($prev = \yii\helpers\Url::previous($this->module->id))
            return $this->redirect($prev);

        return $this->redirect(['index']);
    }

    public function actionCompose()
    {

        $params = Yii::$app->request->get();
        $model = new ComplexRemedyForm([
            'scenario'     => 'prescribe',
            'maxDropLimit' => 6,
        ]);

        $model->load($params);

        if('extend' == Yii::$app->request->get('command', null))
            $model->extend();

        if('shrink' == Yii::$app->request->get('command', null))
            $model->shrink();

        $model->validate();

        if('finish' == Yii::$app->request->get('command', null))
            if($this->applyComplexRemedy($model))
                return $this->redirect(['compose']);

        return $this->render('/default/compose',['model' => $model]);
    }

    /**
     * @brief 特別レメディー（レメディーマシンで作る）１点を組み立て、カートに追加することができる
     * @see /recipe/create/machine
     */
    public function actionMachine()
    {
        $model = new \common\models\MachineRemedyForm();
        $model->load(Yii::$app->request->post()); 

        if($model->load(Yii::$app->request->post()) &&
           $model->validate() && $this->addMachine($model))
        {
            Yii::$app->session->addFlash('success', sprintf('%s が追加されました', $model->name));
            return $this->redirect('machine');
        }

        return $this->render('/default/machine', ['model' => $model]);
    }

    public function addMachine($model, $qty = 1)
    {
        $product = Product::findOne($model->product_id);
        if(! $product || ! $product->company)
            return false;

        $product->name = $model->name;
        $this->applyProduct($product);
        return true;
    }

    public function actionFinish()
    {
        // set required static values
        if($this->module->purchase->isNewRecord)
        {
            if($this->module->branch->isWarehouse())
            {
                $paid    = (int)false;
                $shipped = (int)false;
            }
            else
            {
                $paid    = $this->module->purchase->isPaymentDeferred() ? (int)false : (int)true;
                $shipped = 9; // no need to ship
            }

            $this->module->purchase->branch_id = $this->module->branch->branch_id;
            $this->module->purchase->paid      = $paid;
            $this->module->purchase->shipped   = $shipped;
        }
        $transaction = Yii::$app->db->beginTransaction();
        $rollback    = false;

/* 在庫処理 */
        $outOfStock = false;
        $msg = "注文を確定できませんでした。";
        $items = $this->module->purchase->items;
        if(isset($this->module->purchase->campaign))
            $this->module->purchase->setCampaignForItems();

        try {

        if($this->module->branch->branch_id == \common\models\Branch::PKEY_ROPPONMATSU) {

            foreach($items as $item)
            {

                if(($m = $item->model) && $m instanceof \yii\db\ActiveRecord)
                {
                    // Product_idを持つ場合、dtb_stock上の在庫確認を行う(併せてバージョン取得)
                    if (!isset($m->product_id))
                        continue;

                    if (isset($m->product_id)) {// && $m->product_id == Stock::VEGETABLE_SETM) {

                        // Stockテーブルの在庫数の取得、更新
                        $stock = $this->stock->getStock($m->product_id);

                        if (! $stock) continue;

                        if ($stock->actual_qty < 1 || ($stock->actual_qty < $item->qty)) {
                            if ($stock->actual_qty != 0 && $stock->actual_qty < $item->qty) {
                                $msg .= sprintf("<br> %s の在庫数を上回る数量が指定されています。(現在の在庫数：%s)", $m->name, $stock->actual_qty);
                            }

                            $m->in_stock = 0;
                        } else {
                            // 在庫引当
                            if($this->module->purchase->isNewRecord) {
                                $stock->actual_qty = ($stock->actual_qty - $item->qty);
                            } else {
                                // 新規レコードで無い＝伝票修正の場合は、単純に引いてしまうと在庫に矛盾が発生するa
                                $origin_purchase = \common\models\PurchaseItem::find()
                                              ->andWhere(['purchase_id' => $item->purchase_id,
                                                          'product_id' => $item->product_id,
                                                          'code' => $item->code])
                                              ->select('quantity')->one();

                                if($origin_purchase && $item->qty != $origin_purchase->qty)
                                    $stock->actual_qty = $stock->actual_qty - ($item->qty - $origin_purchase->qty);
                            }

                            $stock->updated_by = Staff::STAFF_SYSTEM; // 暫定でフロント側で在庫が更新される場合は「システム」にする

                            // mvtb_product_masterの更新日時を更新( キャッシュ対応 )
                            $q = Yii::$app->db
                            ->createCommand('update mvtb_product_master set update_date = NOW() where product_id = :product_id')
                            ->bindValues([':product_id'=> $m->product_id]);

                            if (! $stock->save(false) || ! $q->execute()) {
                                Yii::error(
                                    sprintf('dtb_stock と mvtb_product_master の更新処理に失敗しました。(product_id: %s)', $m->product_id)
                                    ,self::className().'::'.__FUNCTION__);
                                $m->in_stock = 0;
                            }
                        }
                    }

                    if (($m->hasAttribute('in_stock') && ! $m->in_stock))
                        $outOfStock = true;
                }
            }
        }
/* 在庫処理ここまで */
            if(!$outOfStock && $this->module->purchase->validate() && $this->module->purchase->save())
            {
                $purchase_id = $this->module->purchase->purchase_id;

                // 適用書と伝票を紐付ける処理を呼び出す（適用書が追加されてなければスルー）
                if (! $this->recipeCookieSave($purchase_id))
                    $rollback = true;

            } else {
                $purchase_id = null;
                Yii::$app->session->addFlash('error', implode(';',$this->module->purchase->firstErrors));
            }

            if(! $rollback)
            {
                // success
                $transaction->commit();
                Yii::$app->session->remove(self::SESSION_KEY_RECIPE);
                Yii::$app->session->remove(self::SESSION_KEY_RECIPE_DEL);
                $this->module->clearBuffer(); // forget everything here

                $print_name_flg = Yii::$app->request->get('print_name_flg', 0); // print customer name for receipt

                if($this->module->branch->isWarehouse()){
                    return $this->redirect(['view',   'id'=>$purchase_id]);
                } else {
                    return $this->redirect(['receipt','id'=>$purchase_id, 'print_name_flg' => $print_name_flg]);
                }
            }
            // failure
            $transaction->rollback();
        } catch (StaleObjectException $e) {
            // 衝突を解決するロジック
            $transaction->rollback();
            Yii::$app->getSession()->addFlash('error', $msg);

        }
        return $this->redirect(['create']);
    }

    /**
     * init or update cookie
     */
    private function recipeCookieUpdate($recipe_id)
    {
        $key   = self::SESSION_KEY_RECIPE;
        $rid   = Yii::$app->session->get($key, []);

        $del_key   = self::SESSION_KEY_RECIPE_DEL;
        $rid_dels   = Yii::$app->session->get($del_key, []);

        // 対象の適用書IDがセッションに格納されていない場合は格納する
        if (! in_array($recipe_id, $rid)) {
            $rid[] = $recipe_id;
            Yii::$app->session->set($key, $rid);
        }

        // 対象の適用書IDが削除予定セッションに格納されている場合は削除する
        foreach($rid_dels as $rkey => $val) {
            if($val == $recipe_id) {
                unset($rid_dels[$rkey]);
                array_values($rid_dels);
            }
        }

        if(count($rid_dels) > 0 ) {
            Yii::$app->session->set($del_key, $rid_dels);
        } else {
            Yii::$app->session->remove($del_key);
        }

        return;
    }

    /**
     * bind purchase_id & recipe_id
     */
    private function recipeCookieSave($purchase_id)
    {
        $key    = self::SESSION_KEY_RECIPE;
        $del_key    = self::SESSION_KEY_RECIPE_DEL;
        $rid    = Yii::$app->session->get($key, []);
        $rid_dels    = Yii::$app->session->get($del_key, []);

        // セッション上に適用書IDが格納されていない場合は正常終了で返す
        if(!$rid && !$rid_dels)
            return true;

        // セッション上に適用書IDが存在している場合は、管理テーブルへの追加とステータス変更を行なう。
        foreach($rid as $id)
        {
            $recipe = \common\models\Recipe::findOne($id);

            if(!$recipe){
                Yii::$app->session->addFlash('error', sprintf('適用書ID： %s は存在しません。', $id ));
                return false;
            }

            $model = \common\models\LtbPurchaseRecipe::find()->where([
                'purchase_id' => $purchase_id,
                'recipe_id'   => $id,
            ])->one();
            if(!$model && $recipe->status != $recipe::STATUS_SOLD) {
                $model = new \common\models\LtbPurchaseRecipe([
                    'purchase_id' => $purchase_id,
                    'recipe_id'   => $id,
                ]);

                // 適用書購入テーブルへのレコード追加に失敗した場合は処理中断
                if (! $model->save() ) {
                    Yii::error($model->errors);
                    return false;
                }
                // 適用書ステータス変更に失敗した場合は処理中断 
                if (!$recipe->sold()) {
                    Yii::$app->session->addFlash('error', sprintf('適用書ID： %s は既に購入済になっています。', $id ));
                    return false;
                }

            }

        }

        // セッション上に削除予定の適用書IDが存在している場合は管理テーブルから削除しステータス変更を行なう。
        foreach($rid_dels as $id)
        {
            $recipe = \common\models\Recipe::findOne($id);

            $model = \common\models\LtbPurchaseRecipe::find()->where([
                'purchase_id' => $purchase_id,
                'recipe_id'   => $id,
            ])->one();

            // 適用書購入テーブルのレコード削除に失敗した場合は処理中断
            if ($model) {
                if(!$model->delete() ) {
                    Yii::error($model->errors);
                    return false;
                }

                // 適用書が存在しない、もしくはステータス変更に失敗した場合は処理中断
                if (!$recipe || !$recipe->unSold()) {
                    Yii::$app->session->addFlash('error', sprintf('適用書ID： %s は存在しない、もしくは既に購入済ではありません。', $id ));
                    return false;
                }
            }

        }
        return true;
    }

    /**
     * clear cookie
     */
    private function recipeCookieDelete($rid = null)
    {
        $key = self::SESSION_KEY_RECIPE;
        $del_key = self::SESSION_KEY_RECIPE_DEL;

        $rids    = Yii::$app->session->get($key, []);
        $rid_dels = Yii::$app->session->get($del_key, []);

        if(!$rid) {
            Yii::$app->session->remove($key);
            return true;
        }

        // セッション上に適用書IDが格納されていない場合は正常終了で返す
        if(!in_array($rid, $rids)) {
            Yii::$app->session->addFlash('error', sprintf('適用書ID： %s は存在しない、もしくは既に伝票から削除されています'));
            return true;
        }


        // 適用書が存在しない場合は処理中断
        $recipe = \common\models\Recipe::findOne($rid);
        if (!$recipe) {
            Yii::$app->session->addFlash('error', sprintf('適用書ID： %s は存在しません。', $rid ));
            return false;
        }

        // 中間管理テーブルにレコードがある場合に、削除予定のIDとしてセッションに記録する
        $model = \common\models\LtbPurchaseRecipe::find()->where([
            'purchase_id' => $this->module->purchase->purchase_id,
            'recipe_id'   => $rid,
        ])->one();
        if($model) {

            // 削除予定ID
            if (! in_array($rid, $rid_dels)) {
                $rid_dels[] = $rid;
                Yii::$app->session->set($del_key, $rid_dels);
            }
        }
        
        // 登録予定IDから削除
        foreach($rids as $rkey => $val) {
            if($val == $rid) {
                unset($rids[$rkey]);
                array_values($rids);
            }
        }

        if(count($rids) > 0 ) {
            Yii::$app->session->set($key, $rids);
        } else {
            Yii::$app->session->remove($key);
        }
        return true;
    }

    public function actionSearch($target)
    {
        if('customer' == $target)
            return $this->actionSearchCustomer();

        elseif('delivery' == $target)
            return $this->actionSearchDelivery();

        elseif('flower'  == $target)
            return $this->actionSearchFlower(); // フラワーエッセンス

        elseif('flower2'  == $target)
            return $this->actionSearchFlower2(); // フラワーエッセンス2

        elseif('modular'  == $target)
            return $this->actionSearchModular(); // セット補充

        elseif('popular'  == $target)
            return $this->actionSearchPopular(); // キットセット補充

        elseif('kit' == $target)
            return $this->actionSearchKit(); // キット単品

        elseif('product'  == $target)
            return $this->actionSearchProduct(Yii::$app->request->get('category'));

        elseif('products' == $target)
            return $this->searchProducts();

        elseif('cosme_food' == $target)
            return $this->searchCosmeAndFood();

        elseif('book_dvd' == $target)
            return $this->searchBookAndDvd();

        elseif('restaurant' == $target)
            return $this->searchRestaurant();

        elseif('other' == $target)
            return $this->searhOther();
        
        elseif('agent' == $target)
            return $this->searchAgent();

        elseif('remedy'  == $target)
            return $this->actionSearchRemedy(Yii::$app->request->get('startwith'));
        elseif('rxt' == $target)
            return $this->actionSearchRxt(Yii::$app->request->get('startwith'));

        elseif('all_remedy' == $target)
            return $this->actionSearchAllRemedy(Yii::$app->request->get('startwith'));

        elseif('tincture'  == $target)
            return $this->actionSearchTincture(); // Mother Tincture

        elseif('veg'  == $target)
            return $this->actionSearchVegetable(); // 野菜


        elseif('recipe'  == $target)
            return $this->actionSearchRecipe(); // 適用書

        elseif('trose' == $target)
            return $this->searchTrose(); // トミーローズ

        else
            throw new \yii\web\NotFoundHttpException('該当するデータがありません。');
    }

    private function actionSearchCustomer()
    {
        $searchModel  = new \backend\models\SearchCustomer();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
//        $dataProvider->query->child(false);

        if(! Yii::$app->user->can('viewCustomer'))
            throw new \yii\web\ForbiddenHttpException("許可がありません");

        return $this->render('search',[
            'title'        => 'お客様を検索',
            'viewFile'     => '_customer',
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    private function actionSearchDelivery()
    {
        $model  = new \common\models\CustomerAddrbook(['customer_id' => $this->module->purchase->customer_id]);
        $model->load(Yii::$app->request->queryParams);
        $model->validate(['kana01']); // filter by Romaji2Kana
        $model->clearErrors();        // ignore error
        $model->tel01 = preg_replace('/-/','',$model->tel01);
        $query  = $model->find()->where(['customer_id'=> $model->customer_id])
                        ->andFilterWhere(['pref_id'   => $model->pref_id    ])
                        ->andFilterWhere(['like', 'CONCAT(name01,name02)',    $model->name01])
                        ->andFilterWhere(['like', 'CONCAT(kana01,kana02)',    $model->kana01])
                        ->andFilterWhere(['like', 'CONCAT(addr01,addr02)',    $model->addr01])
                        ->andFilterWhere(['like', 'CONCAT(zip01,zip02)',      $model->zip01 ])
                        ->andFilterWhere(['like', 'CONCAT(tel01,tel02,tel03)',$model->tel01 ]);

        $dataProvider = new \yii\data\ActiveDataProvider(['query'=>$query]);

        return $this->render('search',[
            'title'        => 'お届け先を検索',
            'viewFile'     => '_delivery',
            'searchModel'  => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    private function actionSearchProduct($category_id=null)
    {
        $searchModel  = new \backend\models\SearchProduct([
            'category_id' => $category_id,
            'company'     => $this->module->branch->company_id
        ]);
        $provider = $searchModel->search(Yii::$app->request->get());
        $provider->query->active();

        if(! $provider->query->exists() && ! $searchModel->category_id)
        {
            // Product に一致しなければ Remedy の検索結果を表示する
            $k = trim($searchModel->keywords).'%';
            $q = \common\models\Remedy::find()->where(['like','abbr', $k, false]);
            if($q->exists())
                return $this->redirect(['search','target'=>'remedy','startwith'=>$k]);
        }

        return $this->render('search',[
            'title'        => '商品を検索',
            'viewFile'     => '_product',
            'searchModel'  => $searchModel,
            'dataProvider' => $provider,
        ]);
    }

    private function searchProducts()
    {
        $searchModel  = new \backend\models\SearchProduct([
            'subcategory' => \yii\helpers\ArrayHelper::getColumn(
                \common\models\Subcategory::find()->products()->all(), 'subcategory_id'),
        ]);
        $provider = $searchModel->search(Yii::$app->request->queryParams);
        $provider->query->active();

        return $this->render('search',[
            'title'        => '商品を検索',
            'viewFile'     => '_products',
            'searchModel'  => $searchModel,
            'dataProvider' => $provider,
        ]);
    }

    private function searchCosmeAndFood()
    {
        $searchModel  = new \backend\models\SearchProductMaster([
            'subcategories' => [4,201,202,203,5,6,2,204,161,162,205,208],
            'branch_id'     => $this->module->branch->branch_id
        ]);
        $dataProvider = $searchModel->searchCosmeAndFood(Yii::$app->request->queryParams);

        return $this->render('search',[
            'title'        => '商品を検索',
            'viewFile'     => '_cosme_food',
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    private function searchBookAndDvd()
    {
        $searchModel  = new \backend\models\SearchProduct([
            'categogies' => [\common\models\Category::BOOK],
        ]);
        $provider = $searchModel->search(Yii::$app->request->get());
        $provider->query->active();
        return $this->render('search',[
            'title'        => '商品を検索',
            'viewFile'     => '_book_dvd',
            'searchModel'  => $searchModel,
            'dataProvider' => $provider,
        ]);
    }

    private function searchRestaurant()
    {
        $searchModel  = new \backend\models\SearchProduct([
            'categogies' => [\common\models\Category::RESTAURANT],
        ]);
        $provider = $searchModel->search(Yii::$app->request->get());
        $provider->query->active();

        return $this->render('search',[
            'title'        => '商品を検索',
            'viewFile'     => '_restaurant',
            'searchModel'  => $searchModel,
            'dataProvider' => $provider,
        ]);
    }

    private function searchTrose()
    {
        $searchModel  = new \backend\models\SearchProduct([
            'categogies' => [\common\models\Category::TROSE],
        ]);
        $provider = $searchModel->search(Yii::$app->request->get());
        $provider->query->active();

        return $this->render('search',[
            'title'        => '商品を検索',
            'viewFile'     => '_trose',
            'searchModel'  => $searchModel,
            'dataProvider' => $provider,
        ]);
    }


    private function searhOther()
    {
        $searchModel  = new \backend\models\SearchProduct([
            'categogies' => [\common\models\Category::OTHER],
        ]);
        $provider = $searchModel->search(Yii::$app->request->get());
        $provider->query->active();

        return $this->render('search',[
            'title'        => '商品を検索',
            'viewFile'     => '_other',
            'searchModel'  => $searchModel,
            'dataProvider' => $provider,
        ]);

    }
    
    private function searchAgent()
    {
        $searchModel  = new \backend\models\SearchProduct([
            'subcategory' => [\common\models\Subcategory::PKEY_ONLY_HE],
        ]);
        $provider = $searchModel->search(Yii::$app->request->get());
        $provider->query->active();

        return $this->render('search',[
            'title'        => '商品を検索',
            'viewFile'     => '_agent',
            'searchModel'  => $searchModel,
            'dataProvider' => $provider,
        ]);

    }

    /**
     * レジの検索タブ「キットセット」
     */
    private function actionSearchPopular()
    {
        $searchModel  = new \backend\models\SearchProductMaster([
            'subcategories' => [5, 7, 8, 130, 220, 225, 226],
        ]);
        $dataProvider = $searchModel->searchSeparately(Yii::$app->request->queryParams, false);

        return $this->render('search',[
            'title'        => 'キットセットを検索',
            'viewFile'     => '_popular',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * レジの検索タブ「キット単品」
     */
    private function actionSearchKit()
    {
        $searchModel  = new \backend\models\SearchProductMaster(['parent_id' => 7]);
        $dataProvider = $searchModel->searchSeparately(Yii::$app->request->queryParams);

        return $this->render('search',[
            'title'         => 'キット単品を検索',
            'viewFile'      => '_kit',
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
        ]);

    }

    /**
     * レジの検索タブ「セット単品」
     */
    private function actionSearchModular()
    {
        $searchModel  = new \backend\models\SearchProductMaster(['parent_id' => 8]);
        $dataProvider = $searchModel->searchSeparately(Yii::$app->request->queryParams);

        return $this->render('search',[
            'title'        => 'セット単品を検索',
            'viewFile'     => '_modular',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * レジの検索タブ「レメディー単品」
     */
    private function actionSearchRemedy($startwith)
    {
        $searchModel  = new \backend\models\SearchProductMaster([
            'vials'     => \common\models\RemedyVial::isRemedy(),
            'parent_id' => \common\models\Subcategory::PKEY_REMEDY_SEPARATE,
        ]);

        $dataProvider = $searchModel->searchRemedy(Yii::$app->request->queryParams);

        return $this->render('search',[
            'title'        => 'レメディー単品を検索',
            'viewFile'     => '_remedy',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    private function actionSearchAllRemedy()
    {
        $searchModel  = new \backend\models\SearchProductMaster([]);

        $dataProvider = $searchModel->searchAllRemedy(Yii::$app->request->queryParams);

        return $this->render('search',[
            'title'        => 'レメディー全品を検索',
            'viewFile'     => '_rxt',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    private function actionSearchRxt()
    {
        $searchModel  = new \backend\models\SearchProductMaster([]);

        $dataProvider = $searchModel->searchRemedy(Yii::$app->request->queryParams);

        return $this->render('search',[
            'title'        => 'RXTを検索',
            'viewFile'     => '_rxt',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * レジの検索タブ「マザーチンクチャー」
     */
    private function actionSearchTincture()
    {
        $company = $this->module->branch->company;

        $conditions = [
            'potencies' => \common\models\RemedyPotency::MT,
            'vials'    => \yii\helpers\ArrayHelper::getColumn(
                \common\models\RemedyVial::find()->tincture()->all(),'vial_id'
            )
        ];

        // 企業がHE（3）の場合は、サブカテゴリーを指定する
        if ($company->isHE())
            $conditions['subcategories'] = \common\models\Subcategory::PKEY_TOUYA;

        $searchModel  = new \backend\models\SearchProductMaster($conditions);

        $dataProvider = $searchModel->searchRemedy(Yii::$app->request->queryParams);

        return $this->render('search',[
            'title'        => 'マザーチンクチャーを検索',
            'viewFile'     => '_tincture',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * レジの検索タブ「フラワーエッセンス」
     */
    private function actionSearchFlower()
    {
        $searchModel  = new \backend\models\SearchProductMaster([
            'potencies' => \common\models\RemedyPotency::FE,
            'vials'    => \yii\helpers\ArrayHelper::getColumn(
                \common\models\RemedyVial::find()->flower()->all(),'vial_id'
            )
        ]);

        $dataProvider = $searchModel->searchRemedy(Yii::$app->request->queryParams);

        return $this->render('search',[
            'title'        => 'フラワーエッセンスを検索',
            'viewFile'     => '_flower',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * レジの検索タブ「フラワーエッセンス2」
     */
    private function actionSearchFlower2()
    {
        $searchModel  = new \backend\models\SearchProductMaster([
            'potencies' => \common\models\RemedyPotency::FE2,
            'vials'    => \yii\helpers\ArrayHelper::getColumn(
                \common\models\RemedyVial::find()->flower()->all(),'vial_id'
            )
        ]);

        $dataProvider = $searchModel->searchRemedy(Yii::$app->request->queryParams);

        return $this->render('search',[
            'title'        => 'フラワーエッセンス2を検索',
            'viewFile'     => '_flower2',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * レジの検索タブ「野菜」
     */
    private function actionSearchVegetable()
    {
        $searchModel = new \backend\models\SearchVegetable();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $searchModel->clearErrors();

        return $this->render('search',[
            'title'        => '野菜を検索',
            'viewFile'     => '_veg',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * レジの検索タブ「適用書」
     */
    private function actionSearchRecipe()
    {

        $searchModel = new SearchRecipe();
        // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        $dataProvider->query->andWhere(['status' => [Recipe::STATUS_INIT, Recipe::STATUS_PREINIT]]);

        return $this->render('search', [
            'title'        => '適用書を検索',
            'viewFile'     => '_recipe',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    private function applyCommand(Command $model)
    {
        $action = $model->action;
        if($action == $model::ACTION_FINISH)
        {
            return $this->actionFinish();
        }

        if($action == $model::ACTION_RESET)
        {
            $this->recipeCookieDelete();
            $this->module->clearBuffer();
        }
        elseif($action == $model::ACTION_DISCOUNT)
            $this->module->purchase->discount = $model->volume;

        elseif($action == $model::ACTION_REDUCE)
        {
            if(is_float($model->volume))
            {
                $attr = 'discount_rate';
                $vol  = $model->volume * 100;
            }
            else
            {
                $attr = 'discount_amount';
                $vol  = $model->volume;
            }
            if(true == $model->seq) {
                $item = end($this->module->purchase->items);
                // レジにアイテムがあるときだけ処理
                if(!$item)
                    return $this->redirect(['create']);

                if($attr == 'discount_amount') {
                   $item->setDiscountAmount($vol);
                   $item->setDiscountRate(round($item->discount_amount / $item->price * 100));
                }

                if($attr == 'discount_rate') {
                    $item->setDiscountRate($vol);
                    $item->setDiscountAmount(floor($item->price * $item->discount_rate / 100));
                }

                $item->setPointAmount(floor(($item->price - $item->discount_amount) * $item->point_rate / 100));


            } else {
                foreach($this->module->purchase->items as $k => $item) {
                    $item->$attr = $vol;
                    if($attr == 'discount_amount') {
                        $item->setDiscountAmount($vol);
                        $item->setDiscountRate(round($item->discount_amount / $item->price * 100));
                    }

                    if($attr == 'discount_rate') {
                        $item->setDiscountRate($vol);

                        $item->setDiscountAmount(floor($item->price * $item->discount_rate / 100));
                    }

                    $item->setPointAmount(floor(($item->price - $item->discount_amount) * $item->point_rate / 100));

                }
            }
            $this->module->purchase->compute(false);
        }

        return $this->redirect(['create']);
    }

    private function applyBarcode($barcode, $auto_customer_create = '0')
    {
        $barcode = mb_convert_kana($barcode, 'as'); // 全角 -> 半角
        $barcode = trim($barcode);
        $command = new Command(['code' => $barcode]);
        if($command->validate())
            return $this->applyCommand($command);

        if(preg_match('/^25/',$barcode) && (12 == strlen($barcode)))
            $barcode .= 0; // add bogus checkdigit


        // 顧客自動作成が1:ONの場合、自動作成を行なう
        if('1' == $auto_customer_create) {
            $model = \backend\models\Customer::findByBarcode($barcode,false,$auto_customer_create);
            $this->applyCustomer($model->customer_id);
            return $this->redirect(['create']);
        }

        $finder = new \common\components\ean13\ModelFinder(['barcode' => $barcode]);

        $model = $finder->getOne();


        if(! $model)
            // check if input was a membercode
            $model = \common\models\Membercode::findOne(['code'=>$barcode]);

        if(! $model)
            // check if input was a product code
            $model = \common\models\Product::findOne(['code'=>$barcode]);

        if(! $model)
        {
            // check if input was a pickcode
            $pick = \common\models\ProductPickcode::findOne(['pickcode' => $barcode]);

            if(! $pick)
                 $pick = \common\models\ProductPickcode::findOne(['product_code'=>$barcode]);
            if(  $pick)
                $model = $pick->model;
        }


        if($model instanceof \common\models\Product) {
            // 野菜かどうかは、Vegetable：EAN13＿PREFIXに先頭が一致するかで判定できる
            if(Vegetable::EAN13_PREFIX == substr($model->code, 0, strlen(Vegetable::EAN13_PREFIX))) {
                $veg_id = (int) substr($model->code, 2, 5);
                $price  = (int) substr($model->code, 7, 5);
                $this->applyVegetable($veg_id, 1, $price);
            } else {
                $this->applyProduct($model);
            }
        }

        else if($model instanceof \common\models\Customer) {
            $this->applyCustomer($model->customer_id);
        }
        else if($model instanceof \common\models\Membercode) {

            self::addFlash('warning', "この会員証はまだ登録されていません。このまま会員登録を行ないますか？<br>"
                                  . sprintf("(会員証No:%s)", $model->code)
                                  . '  <button class="btn btn-default" data-dismiss="alert" aria-hidden="true" style="margin-left:15px;">キャンセル</button>'
                                 // . '        <a disabled="disabled" style="pointer-events:none; float:right; margin-right:10px;" class="disable btn btn-info" href="'.\Yii::$app->getUrlManager()->createUrl(['customer/create']).'">顧客を登録しますか？</a>'
                                  . '        <a margin-left:10px;" class="btn btn-info" href="'.\Yii::$app->getUrlManager()->createUrl(['/casher/default/apply', 'target' => 'barcode', 'barcode' => $barcode, 'auto_customer_create' => '1']).'">顧客登録を続行</a>'

            );

        }
        else if($model && isset($model->remedy_id) && isset($model->potency_id) && isset($model->vial_id)) {
            $this->applyRemedy($model->remedy_id, $model->potency_id, $model->vial_id, $model->in_stock);
        }
        else if(! $finder->validate()) {
            self::addFlash('error', "バーコードにエラーがあるようです。もう一度スキャンしてください。<br>"
                                  . sprintf("(barcode:%s)", $finder->barcode));
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /* @return bool */
    private function applyComplexRemedy($model)
    {
        if($model->hasErrors())
        {
            self::addFlash('error', $model->firstErrors);
            return false;
        }
        $this->module->purchase->addItem($model);
        return true;
    }

    /* @return void */
    public function applyCustomer($id)
    {
        if($customer = \common\models\Customer::findOne($id))
            self::addFlash('success', sprintf("<strong>%s</strong>さんを設定しました", $customer->name));

        $this->module->purchase->setCustomer($customer);

        // 熱海発想所かつ特定顧客のみ銀行振込に固定する
        if($this->module->branch->branch_id == Branch::PKEY_ATAMI) {
            if($customer && in_array($customer->customer_id, [30500, 30501, 30513]))
            {
                $this->module->purchase->payment_id = Payment::PKEY_BANK_TRANSFER;
            } else {
                $this->module->purchase->payment_id = Payment::PKEY_YAMATO_COD;
            }
        }

        // flush point & discount
        $this->module->purchase->compute(true);

        $this->module->purchase->delivery = null;

        return;
    }

    /* @return mixed */
    private function applyProduct($product, $qty=1, $target='product')
    {
        if($product instanceof \common\models\Product)
        {
        $matrix = [Product::PKEY_TORANOKO_G_ADMISSION => Membership::PKEY_TORANOKO_GENERIC,
                   Product::PKEY_TORANOKO_N_ADMISSION => Membership::PKEY_TORANOKO_NETWORK];
        $pid    = $product->product_id;
            if(in_array($pid,array_keys($matrix)))
            {
                if($cid = $this->module->purchase->customer_id)
                {
                    echo $this->redirect(['/member/toranoko/update',
                                          'id'  => $cid,
                                          'mid' => $matrix[$pid],
                                          'pid' => Payment::PKEY_CASH,
                    ]);
                    exit; // とにかくここで HTTP response を終わる
                }

                self::addFlash('error',"年会費のご購入にはお客様の指定が必要です");
                return $this->redirect(['create']);
            }
        }

        $item = new \common\models\PurchaseItem(['company_id' => $product->category->seller_id]);
        foreach($item->attributes as $name => $value)
        {
            if($product->hasAttribute($name)) {
                if($name === 'code') { // 品番を入れるな！　JAN > 内部コードの優先度で返す
                    $item->$name = $product->getBarcode();
                } else {
                    $item->$name = $product->$name;
                }
            }
        }
        $item->quantity = $qty;
        $this->module->purchase->addItem($item);

        if($this->module->purchase->campaign_id && $this->module->purchase->campaign_id > 0) {
            $this->module->purchase->setCampaignForItems();
        }

        if(Yii::$app->request->isAjax)
            return 'ok';

        // self::addFlash('success', sprintf("<strong>%s</strong>を追加しました", $item->name));
        return $this->redirect(Yii::$app->request->referrer);

        // return $this->redirect(['search', 'target'=>$target]);
    }

    /* @return void */
    private function applyQuantity($params)
    {

        $seq = ArrayHelper::getValue($params, 'seq', null);
        $vol = ArrayHelper::getValue($params, 'vol', null);
        $opt = ArrayHelper::getValue($params, 'operator');
        if((null === $seq) || (null === $vol) || ! isset($this->module->purchase->items[$seq]))
            return null;

        $vol = (int) mb_convert_kana($vol, 'n');

        if('=' === $opt)
            $this->module->purchase->items[$seq]->quantity  = $vol;
        else
            $this->module->purchase->items[$seq]->quantity += $vol;

        $item = $this->module->purchase->items[$seq];
        if($item->quantity <= 0)
            unset($this->module->purchase->items[$seq]);

        if(Yii::$app->request->isAjax)
        {
            $this->module->purchase->compute(false); // keep manually added discounts
            $param = $this->module->purchase->attributes;
            $param['item'] = [
                'seq'      => $seq,
                'quantity' => $item->quantity,
                'charge'   => $item->charge,
            ];

            if($item->quantity <= 0)
                // `seq` has been changed. need to refresh entire <table>
                $param['widget'] = \backend\modules\casher\widgets\CartContentGrid::widget([
                                       'items' => $this->module->purchase->items
                                   ]);
                $param['itemCount'] = $this->module->purchase->itemCount;

            return \yii\helpers\Json::encode($param);
        }

        // self::addFlash('success', sprintf("<strong>%s</strong> が <strong>%s 点</strong> になりました", $item->name, $item->quantity));

        return null;
    }

    /* @return void */
    private function applyPrice($params)
    {

        $seq = ArrayHelper::getValue($params, 'seq', null);
        $price = ArrayHelper::getValue($params, 'price', null);

        if((null === $seq) || (null === $price) || ! isset($this->module->purchase->items[$seq]))
            return null;

        $price = (int) mb_convert_kana($price, 'n');
        $this->module->purchase->items[$seq]->price  = $price;

        $item = $this->module->purchase->items[$seq];

        if(Yii::$app->request->isAjax)
        {
            $param = $this->module->purchase->attributes;
            $param['item'] = [
                'seq'    => $seq,
                'price'  => $item->price,
                'charge' => $item->charge,
            ];
            return \yii\helpers\Json::encode($param);
        }
        return null;
    }

    public function setCampaignReset()
    {
        $this->module->purchase->unsetCampaign();
        $this->module->purchase->campaign_id = 0;
    }

    public function setCampaignForItems()
    {
        $campaign_id = $this->module->purchase->campaign_id;

        // キャンペーンIDに紐づくキャンペーン詳細を全て取得する
        $products = Yii::$app->db->createCommand('
            SELECT
                `m`.`ean13`,
                `dtb_campaign_detail`.`discount_rate`
            FROM
              `dtb_campaign_detail`
              INNER JOIN `mvtb_product_master` `m`
                ON dtb_campaign_detail.ean13 = m.ean13
                AND (dtb_campaign_detail.ean13 IS NOT NULL)
                AND (`campaign_id` = :campaign_id)
        ', [':campaign_id' => $campaign_id])->queryAll();

        $products = ArrayHelper::map($products, 'ean13', 'discount_rate');

        $subcategories = Yii::$app->db->createCommand('
            SELECT
                `m`.`ean13`,
                `dtb_campaign_detail`.`discount_rate`
            FROM
              `dtb_campaign_detail`
              INNER JOIN `mtb_subcategory` `c`
                ON dtb_campaign_detail.subcategory_id = c.subcategory_id
                AND (`campaign_id` = :campaign_id)
                AND (dtb_campaign_detail.subcategory_id IS NOT NULL)
              INNER JOIN `dtb_product_subcategory` `sp`
                ON dtb_campaign_detail.subcategory_id = sp.subcategory_id
              INNER JOIN `mvtb_product_master` `m`
                ON sp.ean13 = m.ean13
        ', [':campaign_id' => $campaign_id])->queryAll();

        $subcategories = ArrayHelper::map($subcategories, 'ean13', 'discount_rate');


        $categories = Yii::$app->db->createCommand('
            SELECT
                `m`.`ean13`,
                `dtb_campaign_detail`.`discount_rate`
            FROM
              `dtb_campaign_detail`
              INNER JOIN `mtb_category` `c`
                ON dtb_campaign_detail.category_id = c.category_id
                AND (dtb_campaign_detail.category_id IS NOT NULL)
              INNER JOIN `mvtb_product_master` `m`
                ON dtb_campaign_detail.category_id = m.category_id
                AND (`campaign_id` = :campaign_id)
        ', [':campaign_id' => $campaign_id])->queryAll();

        $categories = ArrayHelper::map($categories, 'ean13', 'discount_rate');

        // 商品とサブカテゴリー、カテゴリーで取得した商品情報をマージする
        // target_products [[product_id => discount_rate], [product_id => discount_rate]・・・・]
        $target_products = ArrayHelper::merge($products, ArrayHelper::merge($subcategories, $categories));
        // product_idの昇順
        ksort($target_products);

        foreach ($this->module->purchase->items as $seq => $item) {

            foreach($target_products as $ean13 => $discount_rate) {

                if ($item->code == (int)$ean13) {
                    $this->module->purchase->items[$seq]->campaign_id = $campaign_id;
                    $this->module->purchase->applyReduce(['seq' => $seq, 'per' => $discount_rate, 'discount' => 1]);
                }

            }

        }
    }

    public function applyReduce($params)
    {
//        $seq = (int) ArrayHelper::getValue($params, 'seq', null);
//        $per = (int) ArrayHelper::getValue($params, 'per', null);
//        $yen = (int) ArrayHelper::getValue($params, 'yen', null);
        $seq = ArrayHelper::getValue($params, 'seq', null);
        $per = ArrayHelper::getValue($params, 'per', null);
        $yen = ArrayHelper::getValue($params, 'yen', null);
        if(! $item = $this->module->purchase->items[$seq])
            $msg = "$seq 番目の商品がみつかりません";
        elseif(100 < $per)
            $msg = "割引率 $per は大き過ぎます";
        elseif($item->price < $yen)
            $msg = "値引き $yen は大き過ぎます";

        if(isset($msg))
        {
            self::addFlash('error', $msg);
            return;
        }

        if(!isset($yen) && isset($per) && $per != $item->discountRate)
            $item->setDiscountAmount(floor($item->price * $per / 100));
            $item->discountRate = $per; // TBD: not working!!

        if(!isset($per) && isset($yen) && $yen != $item->discountAmount)
        $item->setDiscountRate(round($yen / $item->price * 100));
            $item->discountAmount = $yen;

    }

    /* @return void */
    public function applyRecipe($recipe_id)
    {
        if(! $recipe = $this->findRecipe($recipe_id)) {
            Yii::$app->session->addFlash('error',"適用書のインポートに失敗しました。<br>手動で追加して下さい。");
            return $this->redirect(['search', 'target'=>'recipe']);
        }

        foreach($recipe->parentItems as $item)
            $this->applyRecipeItem($item);

        if(($client = $recipe->client) && ($client = $client->parent ? $client->parent : $client))
            $this->applyCustomer($client->customer_id);

        // セッションに適用書IDを格納（最終的な伝票との紐付けはPurchase->Save時）
        $this->recipeCookieUpdate($recipe_id);

        Yii::$app->session->addFlash('success', sprintf('適用書ID： %s を追加しました。', $recipe_id ));
        if(Yii::$app->request->isAjax)
            return 'ok';

        return $this->redirect(Yii::$app->request->referrer);
        // return $this->redirect(['search', 'target'=>'recipe']);
    }

    private function applyRecipeItem(\common\models\RecipeItem $item)
    {
        $cnt = $this->module->purchase->itemCount;

        if($item->children && ($model = \common\components\cart\ComplexRemedyForm::convertFromRecipeItem($item)))
            return $this->applyComplexRemedy($model);

        elseif($item->remedy_id)
            foreach(range(1, $item->quantity) as $i)
                $this->addRemedy($item->remedy_id,
                                   $item->potency_id,
                                   $item->vial_id,
                                   false);
        else
        {
            $p = \common\models\Product::findOne($item->product_id);
            if($p->name != $item->name) // it is MachineRemedy
                $p->name = $item->name; // keep remedy names

            foreach(range(1, $item->quantity) as $i)
                $this->applyProduct($p);
        }

        if($cnt == $this->module->purchase->itemCount)
            self::addFlash('error', "{$item->name}の追加に失敗しました。手動で追加してください。");

        return;
    }

    /* @return void */
    public function applyRecipeDel($recipe_id)
    {
        if(! $recipe = $this->findRecipe($recipe_id)) {
            Yii::$app->session->addFlash('error',"指定の適用書IDはこの伝票に追加されていません");
            return $this->redirect(Yii::$app->request->referrer);
        }

        // セッションから適用書IDを削除
        $this->recipeCookieDelete($recipe_id);
        Yii::$app->session->addFlash('success', sprintf('適用書ID： %s を解除しました。', $recipe_id ));

        if(Yii::$app->request->isAjax)
            return 'ok';

        return $this->redirect(Yii::$app->request->referrer);
    }

    private function addRemedy($rid,$pid,$vid,$stock,$qty=1)
    {
        $stock = $this->findRemedy($rid,$pid,$vid,$stock);
        $name  = ProductMaster::find()->where(['remedy_id'=>$rid,
                                               'potency_id'=>$pid,
                                               'vial_id'   =>$vid])
                                      ->select('name')
                                      ->scalar();

        $item = new \common\models\PurchaseItem([
            'company_id' => \common\models\Company::PKEY_HJ,
            'remedy_id'  => $rid,
            'code'       => $stock->barcode,
            'price'      => $stock->price,
            'name'       => $name ? $name : $stock->name,
        ]);
        if($stock->errors) {
            self::addFlash('error', sprintf("この商品（%s : %s）の価格が取得できません", $item->name, $item->code));
            return;
        }

        $item->quantity = $qty;
        $this->module->purchase->addItem($item);

        if($this->module->purchase->campaign_id && $this->module->purchase->campaign_id > 0) {
            $this->module->purchase->setCampaignForItems();

        }
    }

    private function applyKitSet($target)
    {
        $qty        = Yii::$app->request->get('qty'); // 数量

        if ($product_id = Yii::$app->request->get('id'))
            return $this->applyProduct(\common\models\Product::findOne($product_id), $qty, $target);

        $remedy_id  = Yii::$app->request->get('rid');
        $potency_id = Yii::$app->request->get('pid');
        $vial_id    = Yii::$app->request->get('vid');

        if ($remedy_id && $potency_id && $vial_id)
            return $this->applyRemedy($remedy_id, $potency_id, $vial_id, false, $qty, $target);

        return;
    }

    /* @return mixed */
    private function applyRemedy($rid,$pid,$vid,$stock,$qty=1,$target='remedy')
    {
        $this->addRemedy($rid,$pid,$vid,$stock,$qty);
        if(Yii::$app->request->isAjax)
            return 'ok';

        return $this->redirect(Yii::$app->request->referrer);
    }

    private function applyVegetable($vid, $qty, $price)
    {
        $model = Vegetable::findOne($vid);
        if(! $model)
            self::addFlash('error', sprintf("veg_idが見つかりません(%s)", $vid));

        $price = trim(mb_convert_kana($price, 'a'));
        $price = min(99999, (int)$price); // 上限 5 桁
        if($price <= 0)
            self::addFlash('error', sprintf("価格が不正です(%s)", $price));

        if(Yii::$app->session->hasFlash('error'))
            return $this->redirect(['search','target'=>'veg']);

        $model->price = $price;

            $product = Vegetable::findByBarcode($model->ean13);

        $item = new \common\models\PurchaseItem([
            'company_id' => $this->module->branch->company_id,
            'product_id' => $product->product_id,
            //'code'       => sprintf('%05d', $model->veg_id),
            'code'       => $model->ean13,
            'price'      => $model->price,
            'name'       => sprintf('%s', $model->print_name),
        ]);

        $item->quantity = $qty;
        $this->module->purchase->addItem($item);


        // TODO:従来処理。改修すると、PurchaseFormに集約される
        if($this->module->purchase->campaign_id && $this->module->purchase->campaign_id > 0) {
            $this->module->purchase->setCampaignForItems();
        }


        if(Yii::$app->request->isAjax)
            return \backend\modules\casher\widgets\CartContentGrid::widget([
                'items' => $this->module->purchase->items
            ]);

        return $this->redirect(Yii::$app->request->referrer);

        // self::addFlash('success', sprintf("<strong>%s</strong>を追加しました", $item->name));

        // return $this->redirect(['search', 'target'=>'veg']);
    }

        /* @return void */
    public function applyDelivery($id)
    {
        if(! $addrbook = $this->findAddrBook($id)) {
            Yii::$app->session->addFlash('error',"指定の登録住所データは存在しません。<br>手動で追加して下さい。");
            return $this->redirect(['search', 'target'=>'delivery']);
        }

        $delivery = new PurchaseDelivery();
        $delivery->load($addrbook->attributes, '');
        $this->module->purchase->delivery = $delivery;

        self::addFlash('success', sprintf("「<strong>%s%s%s</strong>」に設定しました", $delivery->pref->name,$delivery->addr01,$delivery->addr02));

                if($prev = \yii\helpers\Url::previous($this->module->id))
            return $this->redirect($prev);

        return $this->redirect(Yii::$app->request->referrer);
        // return $this->redirect(['search', 'target'=>'recipe']);
    }

    /* @return common/models/Recipe | null */
    private function findRecipe($id)
    {
        if($model = \common\models\Recipe::findOne($id))
            return $model;

        if($row = \common\models\webdb\Syoho::findOne($id))
        {
            $finder = new \common\models\webdb\RecipeFinder(['id'=>$id,'pw'=>$row->passwd]);
            $model  = $finder->get();
        }

        return $model;
    }

    /* @return common/models/CustomerAddrBook | null */
    private function findAddrBook($id)
    {
        if($model = \common\models\CustomerAddrbook::findOne($id))
            return $model;


        return $model;
    }


    /* @return RemedyStock */
    protected function findRemedy($rid,$pid,$vid,$stock)
    {
        $model = \common\models\RemedyStock::find()->where([
            'remedy_id'  => $rid,
            'potency_id' => $pid,
            'vial_id'    => $vid,
        ])->one();
        if(! $model)
            if($stock)
                throw new \yii\web\NotFoundHttpException('当該レメディーが見つかりません');
            else
                $model = new \common\models\RemedyStock([
                    'remedy_id'  => $rid,
                    'potency_id' => $pid,
                    'vial_id'    => $vid,
                ]);

        return $model;
    }

    /* @return void */
    private function applySummary($params)
    {
        foreach($params as $k => $v)
            $params[$k] = trim(mb_convert_kana($v, 'ns'));

        $payment_id    = ArrayHelper::getValue($params, 'payment_id',    null);
        $point_consume = ArrayHelper::getValue($params, 'point_consume', null);
        $discount      = ArrayHelper::getValue($params, 'discount',      null);
        $receive       = ArrayHelper::getValue($params, 'receive',       null);
        $customer_msg  = ArrayHelper::getValue($params, 'customer_msg',  null);
        $campaign_id   = ArrayHelper::getValue($params, 'campaign_id',   null);

        $updateItems = false;

        $this->module->purchase->receive = (int) $receive; // always zero if not paid by cash

        if(null !== $payment_id)
            if($this->module->purchase->payment_id != $payment_id)
                $updateItems = true;
            $this->module->purchase->payment_id = $payment_id;

        if(null !== $point_consume)
            $this->module->purchase->point_consume = $point_consume;

        if(null !== $discount)
            $this->module->purchase->discount = $discount;

        if(null !== $customer_msg)
            $this->module->purchase->customer_msg = $customer_msg;

        if(null !== $campaign_id) {
            if ($campaign_id == 0) {
                $this->setCampaignReset();
            } else {
                $this->module->purchase->campaign_id = $campaign_id;
                $this->module->purchase->setCampaignForItems($updateItems);
            }
        }

       $this->module->purchase->compute($updateItems);
    }

    protected function setCampaignsPulldown()
    {
        $branch = $this->module->branch;
        if(!$branch){
            return null;
        }
        $campaigns = \common\models\Campaign::getCampaignWithBranch($branch->branch_id);

        if (! $campaigns)
            return null;

        return ArrayHelper::merge(['未適用'],
                                  ArrayHelper::map($campaigns, 'campaign_id', 'campaign_name'));
    }


    /**
     * 価格一括更新処理(試作中なのでまだ使えない）
     */
/*
    public function actionUpdateAll()
    {
        try{
            Yii::info("受注情報の一括更新を開始します。", $this->className().'::'.__FUNCTION__);

            $data = \common\models\Purchase::find();
            // 対象データが1件もない場合
            if ($data->count() < 1) {
                Yii::$app->session->addFlash('success','一括更新が終了しました。<br />実行対象件数はありませんでした。');

                return $this->redirect('index');
            }

            // 正常更新件数
            $suc_total = 0;
            $fatal_total = 0;

            foreach ($data->each(1000) as $purchase) {
                try {
                    $purchase_id = $purchase->purchase_id;
	            $model = $this->findModel($purchase_id);
                }
                catch (\console\Exception $e) {
                    // エラーログ出力をする
                    Yii::error(
                        sprintf(
                            "受注情報を取得できませんでした。"
                            ),
                        $this->className().'::'.__FUNCTION__
                        );
                    $fatal_total++;
                    // エラーを検知したデータは後続処理は行わずに次のデータ処理へ
                    continue;
                }
                // データ更新を行う
                if($purchase_id)
                {

                    $form           = \common\models\PurchaseForm::findOne($purchase_id);
                    $form->items    = $model->items;
                    // オリジナルレメディー、特別レメディーが含まれているかチェックし変換する
                    $form->items = $this->convertToComplexRemedyItem($purchase_id, $form->items);
                    $form->delivery = $model->delivery;

                    $this->module->purchase = $form;
                    $this->module->reloadBuffer();
                }
                $this->module->purchase->validate();
                $this->module->purchase->compute(false);
                // save　このタイミングで卸売判定を行なう
                if($this->module->purchase->validate() && $this->module->purchase->save(false)) {
                    $this->module->clearBuffer();
                    $suc_total++;
                } else {
                    $fatal_total++;
                }

            }

            $complete_msg = sprintf(
                '一括更新終了。<br /><br />実行対象件数：%s件<br /><br />正常完了件数：%s件, 処理失敗件数:%s件',
                $data->count(),
                $suc_total,
                $fatal_total
                );

            Yii::info($complete_msg, $this->className().'::'.__FUNCTION__);

            Yii::$app->session->addFlash('success', $complete_msg);

        }
        catch (\yii\db\Exception $e)
        {
            Yii::error($e->__toString(), $this->className().'::'.__FUNCTION__);
            Yii::$app->session->addFlash('error','一括更新にてシステムエラーが発生しました。システム管理者にご連絡ください。');
        }

        return $this->redirect('index');

    }
*/



    protected static function findModel($id)
    {
        $model = \common\models\Purchase::findOne($id);

        if(! $model)
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');

        return $model;
    }

    private function initBreadcrumbs($action)
    {
        $name = ($b = $this->module->branch) ? $b->name : '(拠点不明)';

        $this->view->params['breadcrumbs'][] = [
            'label'=> $name,
            'url'  => [sprintf('/%s/%s/%s',
                               $this->module->id,
                               $this->id,
                               $this->defaultAction)],
        ];

        return true;
    }

    private function initNav($action)
    {
        $this->nav = \yii\bootstrap\Nav::begin([
            'items'   => [
                ['label' => "履歴",    'url' => [ 'index'], 'active' => ('index'  == $action->id) ],
                ['label' => "起票",    'url' => [ 'create'],'active' => in_array($action->id, ['create','search']) ],
            ],
            'options' => ['class' =>'nav-tabs alert-info'],
        ]);

        $target = Yii::$app->request->get('target');
        $this->nav2 = \yii\bootstrap\Nav::begin([
            'items'   => [
                ['label' => "レジ",    'url' => [ 'create'], 'active' => ('create'  == $action->id) ],
                ['label' => "商品を検索",  'url' => ['search','target'=>'product'], 'active' => ('product' == $target)],
                ['label' => "お客様を検索", 'url' => ['search','target'=>'customer'], 'active' => ('customer' == $target)],
            ],
              'options' => ['class' =>'nav-tabs alert-success'],
        ]);
    }

    /**
     * PurchaseItem群から特別レメディー、オリジナルレメディー（ComplexRemedy）に該当するItemを逆変換させる
     */
    private function convertToComplexRemedyItem($purchase_id, $items) {
    	$convert_items = array();
    	foreach($items as $item) {
    		if($item->parent === null) {
    			if(0 == $item->getChildren()->count()) {
    				$convert_items[] = $item;
    			} else {
    				$children = $item->getChildren()->all();
    				$complex = new ComplexRemedyForm(['scenario'=>ComplexRemedyForm::SCENARIO_PRESCRIBE, 'maxDropLimit' => 6]);
    				$recipe_flg = \common\models\LtbPurchaseRecipe::find()->where(['purchase_id' => $item->purchase_id])->one();
    				if($recipe_flg) {
    				}
    				$complex->qty = $item->quantity;
    				$vial = $item->model;
    				$vial->prange_id = 8; // 母体となるRemedyStockのprange_idを８に設定

    				$params = array();
    				$params['Vial'] = array('vial_id' => $vial->vial_id, 'barcode' => $vial->barcode);
    				//$params['Vial'] = array('vial_id' => $vial->vial_id, 'barcode' => $vial->barcode, 'remedy_id' => $vial->remedy_id, 'potency_id' => $vial->potency_id, 'prange_id' => $vial->prange_id);
    				$complex->load($params);
    				$complex->vial = $vial;
                                $discount_amount = $item->discount_amount;
                                $point_amoumt = $item->point_amount;
                                $price = $item->price;
    				$drops = [];
    				foreach($children as $child)
    				{
    					$drop = $child->model;
                                        $discount_amount += $child->discount_amount;
                                        $item->point_amount += $child->point_amount;
                                        $price += $child->price;
    					$drops[] = $drop;
    				}
    				$complex->drops = $drops;
    				$complex->discount = new \common\components\cart\ItemDiscount(['amount' => $discount_amount, 'rate' => $item->discount_rate]);
    				$complex->discount_amount = $discount_amount;
    				$complex->discount_rate = $item->discount_rate;

    				$complex->point = new \common\components\cart\ItemPoint(['amount' => $item->getPointAmount(), 'rate' => $item->point_rate]);
    				$complex->point_amount = floor(($price - $discount_amount) * $item->point_rate / 100);//$item->point_amount;
    				$complex->point_rate = $item->point_rate;
                                $complex->is_wholesale = $item->is_wholesale;
                                $complex->campaign_id = $item->campaign_id;
    				$complex->validate();
    				$convert_items[] = $complex;
    			}
    		}
    	}
    	return $convert_items;
    }

    private static function addFlash($key, $value)
    {
        Yii::$app->session->addFlash($key, $value);
    }

}
