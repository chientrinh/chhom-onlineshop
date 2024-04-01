<?php

namespace backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\Company;
use common\models\Purchase;
use common\models\PurchaseItem;
/**
 * 拠点別売上集計
 */
class SummaryController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        return true;
    }

    public function actionAuth($target = 'index')
    {
        if(Yii::$app->session['invoice'])
            return $this->goBack();


        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new \backend\models\AuthForm();
        if ($model->load(Yii::$app->request->post()) && $model->login())
        {
            Yii::$app->session['invoice'] = true;
            if ($target === 'payoff') {
                return $this->redirect(['payoff']);
            }
            return $this->redirect(['index']);
        }
        return $this->render('auth', [
            'model' => $model,
        ]);
    }

    public function actionIndex()
    {
        if(!Yii::$app->session['invoice']) {
             return $this->redirect(['auth']);
        }

        return $this->render('index');
    }

    public function actionPayoff()
    {
        if(!Yii::$app->session['invoice']) {
             return $this->redirect(['auth', 'target' => 'payoff']);
        }

        $year = Yii::$app->request->get('year') && Yii::$app->request->get('year') != 99 ? Yii::$app->request->get('year') : date('Y');
        $month = Yii::$app->request->get('month') && Yii::$app->request->get('month') != 99 ? Yii::$app->request->get('month') : date('m');

        $start_date = date('Y-m-01 00:00:00', strtotime($year . '-' . $month));
        $end_date = date('Y-m-t 23:59:59', strtotime($year . '-' . $month));

        $payoff = \backend\models\stat\Payoff::find()->where(['year' => $year, 'month' => $month])->all();

        $stat = \backend\models\stat\MonthlySummary::find()
            ->select([
                'year',
                'month',
                'company_id',
                'branch_id',
                'category_id',
                'SUM(subtotal) AS subtotal',
                'SUM(return_total) AS return_total',
                'SUM(discount_total) AS discount_total',
                'SUM(total_charge) AS total_charge',
                'SUM(tax_total) AS tax_total',
                'SUM(discount) AS discount',
                'SUM(point_consume) AS point_consume',
                'SUM(point_given) AS point_given',
                'SUM(postage) AS postage',
                'SUM(handling) AS handling',
                'SUM(net_sales) AS net_sales',
                'SUM(quantity) AS quantity'
            ])
            ->where([
                'year'  => $year,
                'month' => $month,
            ])
            ->groupBy(['company_id', 'branch_id'])
            ->orderBy(['company_id' => SORT_ASC, 'branch_id' => SORT_ASC]);

        $pointing = \common\models\Pointing::find()
                ->select([
                    'company_id',
                    'SUM(point_given) AS point_given',
                    'SUM(point_consume) AS point_consume'
                ])
                ->andWhere(['between', 'create_date', $start_date, $end_date])
                ->groupBy(['company_id'])
                ->asArray()
                ->all();

        $dataProvider = new ActiveDataProvider([
            'query' => $stat,
            'pagination' => false,
        ]);
        $models = $dataProvider->getModels();
        $companies = [Company::PKEY_HJ, Company::PKEY_HE, Company::PKEY_HP];

        $data = [];
        foreach ($companies as $company) {
            $stat = [
                'year'       => $year,
                'month'      => $month,
                'company_id' => $company,
                'sales'      => 0,
                'point_given'   => 0,
                'point_consume' => 0
            ];

            foreach ($models as $model) {
                if ($stat['company_id'] == $model['company_id']) {
                    $stat['point_given'] += $model['point_given'];

                    // 売上は通販のみ、使用ポイントは店舗のみで集計
                    if ($model['branch_id'] == \common\models\Branch::PKEY_ATAMI) {
                        $stat['sales'] += $model['net_sales'];
                    } else {
                        $stat['point_consume'] += $model['point_consume'];
                    }
                }
            }
            // 付与ポイントの計算
            foreach ($pointing as $point) {
                if ($stat['company_id'] == $point['company_id']) {
                    $stat['point_consume'] += $point['point_consume'];
                }
            }
            $data[] = $stat;
        }

        // 初検索かどうかで処理を分岐させる（初回：データ作成・PDF出力、２回目以降：データ更新・画面表示）
        if (!$payoff) {
            foreach ($data as $value) {
                $row = new \backend\models\stat\Payoff([
                    'year'          => $value['year'],
                    'month'         => $value['month'],
                    'company_id'    => $value['company_id'],
                    'sales'         => $value['sales'],
                    'point_given'   => $value['point_given'],
                    'point_consume' => $value['point_consume']
                ]);
                $row->save();
            }
            $this->layout = '/none';
            $mpdf = new \mPDF('ja', 'A4', 0, '', 5, 5, 5, 5, 0, 0, '');
            foreach ($companies as $key => $company_id) {
                $model = \backend\models\stat\Payoff::find()->where(['year' => $year, 'month' => $month, 'company_id' => $company_id])->one();
                $html = $this->render('print-payoff', ['company_id' => $model->company_id, 'year' => $model->year, 'month' => $model->month, 'sales' => $model->sales, 'point_given' => $model->point_given, 'point_consume' => $model->point_consume]);
                $mpdf->WriteHTML($html);
                if ($key != (count($companies) - 1)) {
                    $mpdf->AddPage();
                }
            }
            $mpdf->output();
            return;
        } else {
            // 精算書を作成し直す
            \backend\models\stat\Payoff::deleteAll(['year' => $year, 'month' => $month]);
            foreach ($data as $value) {
                $row = new \backend\models\stat\Payoff([
                    'year'          => $value['year'],
                    'month'         => $value['month'],
                    'company_id'    => $value['company_id'],
                    'sales'         => $value['sales'],
                    'point_given'   => $value['point_given'],
                    'point_consume' => $value['point_consume']
                ]);
                $row->save();
            }
            $query = \backend\models\stat\Payoff::find()->where(['year' => $year, 'month' => $month]);
            return $this->render('payoff', [
                'query' => $query,
                'year'  => $year,
                'month' => $month,
            ]);
        }
    }

    public function actionPrintPayoff($year, $month, $company=[])
    {
        if (!$company) {
            return $this->redirect("payoff?year={$year}&month={$month}");
        }

        $this->layout = '/none';
        $mpdf = new \mPDF('ja', 'A4', 0, '', 5, 5, 5, 5, 0, 0, '');
        $company_list = explode(',', $company);
        foreach ($company_list as $key => $company_id) {
            $model = \backend\models\stat\Payoff::find()->where(['year' => $year, 'month' => $month, 'company_id' => $company_id])->one();
            $html = $this->render('print-payoff', ['company_id' => $model->company_id, 'year' => $model->year, 'month' => $model->month, 'sales' => $model->sales, 'point_given' => $model->point_given, 'point_consume' => $model->point_consume]);
            $mpdf->WriteHTML($html);
            if ($key != (count($company_list) - 1)) {
                $mpdf->AddPage();
            }
        }
        $mpdf->output();
        return;
    }

    /**
     * 日次集計
     */
    public function actionDaily()
    {
        $stat = new \backend\models\stat\DailySummary([
            'payment_id'  => Yii::$app->request->get('payment'),
            'company_id'  => Yii::$app->request->get('company'),
            'branch_id'   => Yii::$app->request->get('branch'),
            'start_date'  => Yii::$app->request->get('start_date'),
            'end_date'    => Yii::$app->request->get('end_date'),
            'class'       => Yii::$app->request->get('class'),
        ]);

        return $this->render('daily', [
                'model'  => $stat,
            ]);
    }

    /**
     * 売上集計データをCSV出力
     * @param null
     *
     */
    public function actionPrintStat($mode='daily')
    {
        $branch_id = Yii::$app->getRequest()->getQueryParam('branch');
        $company_id = Yii::$app->getRequest()->getQueryParam('company');
        $start_date = Yii::$app->getRequest()->getQueryParam('start_date');
        $end_date =  Yii::$app->getRequest()->getQueryParam('end_date');
        $payment_id = Yii::$app->getRequest()->getQueryParam('payment');

        if(Yii::$app->getRequest()->getQueryParam('mode'))
            $mode = Yii::$app->getRequest()->getQueryParam('end_date');

        if($mode != 'daily') {

            $year = Yii::$app->request->get('year') ? Yii::$app->request->get('year') : date('Y');
            $month = Yii::$app->request->get('month') ? Yii::$app->request->get('month') : date('m');
            $end_date = "";

            // CSV出力
            $company_initial = "all";
            $summary = \backend\models\stat\MonthlySummary::find()
                    ->select([
                        'year',
                        'month',
                        'company_id',
                        'branch_id',
                        'category_id',
                        'SUM(subtotal) AS subtotal',
                        'SUM(return_total) AS return_total',
                        'SUM(discount_total) AS discount_total',
                        'SUM(total_charge) AS total_charge',
                        'SUM(tax_total) AS tax_total',
                        'SUM(discount) AS discount',
                        'SUM(point_consume) AS point_consume',
                        'SUM(point_given) AS point_given',
                        'SUM(postage) AS postage',
                        'SUM(handling) AS handling',
                        'SUM(net_sales) AS net_sales',
                        'SUM(quantity) AS quantity'
                    ])
                    ->where(['year' => $year]);

            if ($month != 99) {
                $summary->andWhere(['month' => $month]);
                $start_date = $year . sprintf('%02d', $month);
            } else {
                $start_date = date('Y', strtotime($year));
            }

            if($branch_id && $branch_id != 99)
                $summary->andWhere(['branch_id' => $branch_id]);

            if($company_id && $company_id != 99)
                $summary->andWhere(['company_id' => $company_id]);

            if($payment_id && $payment_id != 0)
                $summary->andWhere(['payment_id' => $payment_id]);

            $summary->groupBy(['company_id', 'branch_id', 'category_id'])
                    ->orderBy(['company_id' => SORT_ASC, 'branch_id' => SORT_ASC, 'category_id' => SORT_ASC]);

            $models = $summary->all();

        } else {
            $stat = new \backend\models\stat\DailySummary([
                'branch_id'   => $branch_id,
                'company_id'  => $company_id,
                'start_date'  => $start_date,
                'end_date'    => $end_date,
            ]);

            $models = $stat->itemProvider->getModels();
            $header_models = $stat->headerItemProvider->getModels();
            $return_models = $stat->returnItemProvider->getModels();
            $minus_models = $stat->minusItemProvider->getModels();
            $point_models = $stat->pointProvider->getModels();
            foreach ($models as $key => $value) {
                if ($return_models) {
                    foreach ($return_models as $return) {
                        if ($this->checkEqualValue($value, $return)) {
                            $models[$key]['returnCharge'] = $return['basePrice'];
                            $models[$key]['quantity'] += $return['quantity'];
                        }
                    }
                }
                if ($minus_models) {
                    foreach ($minus_models as $minus) {
                        if ($this->checkEqualValue($value, $minus)) {
                            $models[$key]['returnCharge'] = $minus['basePrice'];
                            $models[$key]['quantity'] += $minus['quantity'];
                        }
                    }
                }

                foreach ($header_models as $header) {
                    $check = false;
                    foreach ($models as $key => $value2) {
                        if ($value2['company_id'] === $header['summary_company_id'] && $value2['branch_id'] === $header['branch_id'] && !$value2['category_id']) {
                            $models[$key]['discount'] = $header['discount'];
                            $models[$key]['point_consume'] = $header['point_consume'];
                            $models[$key]['postage'] = $header['postage'];
                            $models[$key]['handling'] = $header['handling'];
                            $check = true;
                            break;
                        }
                    }
                    if (!$check) {
                        if (($header['discount'] + $header['point_consume'] + $header['postage'] + $header['handling']) > 0){
                            $models[] = [
                                'company_id'     => $header['summary_company_id'],
                                'branch_id'      => $header['branch_id'],
                                'category_id'    => null,
                                'discount'       => $header['discount'],
                                'point_consume'  => $header['point_consume'],
                                'basePrice'      => 0,
                                'discountTotal'  => 0,
                                'taxTotal'       => 0,
                                'returnCharge'   => 0,
                                'postage'        => $header['postage'],
                                'handling'       => $header['handling'],
                                'point_given'    => 0
                            ];
                        }
                    }
                }
            }
            foreach ($point_models as $point_model) {
                array_push($models, array(
                    'company_id'     => $point_model['company_id'],
                    'branch_id'      => 99,
                    'category_id'    => null,
                    'discount'       => 0,
                    'point_consume'  => $point_model['point_consume'],
                    'basePrice'      => 0,
                    'discountTotal'  => 0,
                    'taxTotal'       => 0,
                    'returnCharge'   => 0,
                    'postage'        => 0,
                    'handling'       => 0,
                    'point_given'    => $point_model['point_given'],
                    'quantity'       => 0
                ));
            }
        }

        foreach ($models as $key => $value) {
            $sort_company[$key] = $value['company_id'];
            $sort_branch[$key] = $value['branch_id'];
            // 「その他」は一旦カテゴリID最大にする
            $sort_category[$key] = ($value['category_id']) ? $value['category_id'] : 100;
        }
        array_multisort($sort_company, $sort_branch, $sort_category, $models);

        $company_model = \common\models\Company::find()->where(['company_id' => $company_id])->one();

        if($company_model) {
            $company_initial = $company_model->key;
        } else {
            $company_initial = "all";
        }
        $basename = ($end_date) ? $company_initial."_".date('Ymd',strtotime($start_date))."-".date('Ymd', strtotime($end_date)) : $company_initial."_".$start_date;
        return $this->printStatCsv($models, $basename);
    }

    /**
    * 配列マージのためのチェック関数
    * company_id, branch_id, category_idが同じであれば返品商品、値引き商品を加算させる
    * @param type $model
    * @param type $value
    * @return boolean
    */
   private function checkEqualValue ($model, $value) {
       if ($model['company_id'] === $value['company_id'] && $model['branch_id'] === $value['branch_id'] && $model['category_id'] === $value['category_id']) {
           return true;
       }
       return false;
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
        $widget = new \common\widgets\doc\purchase\DailyStatCsv();
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
     * 月次集計
     */
    public function actionMonthly()
    {
        $year = Yii::$app->request->get('year') ? Yii::$app->request->get('year') : date('Y');
        $month = Yii::$app->request->get('month') ? Yii::$app->request->get('month') : date('m');
        $payment_id = Yii::$app->request->get('payment');
        $company_id = Yii::$app->request->get('company');
        $branch_id  = Yii::$app->request->get('branch');

        $start_date = date($year.'-'.$month.'-01 00:00:00');
        $end_date = date($year.'-'.$month.'-t 23:59:59');

        $stat = \backend\models\stat\MonthlySummary::find()
            ->select([
                'year',
                'month',
                'company_id',
                'branch_id',
                'category_id',
                'SUM(subtotal) AS subtotal',
                'SUM(return_total) AS return_total',
                'SUM(discount_total) AS discount_total',
                'SUM(total_charge) AS total_charge',
                'SUM(tax_total) AS tax_total',
                'SUM(discount) AS discount',
                'SUM(point_consume) AS point_consume',
                'SUM(point_given) AS point_given',
                'SUM(postage) AS postage',
                'SUM(handling) AS handling',
                'SUM(net_sales) AS net_sales',
                'SUM(quantity) AS quantity'
            ])
            ->where([
                'year'  => $year
            ])
            ->groupBy(['company_id', 'branch_id', 'category_id'])
            ->orderBy(['company_id' => SORT_ASC, 'branch_id' => SORT_ASC, 'category_id' => SORT_ASC]);

        if ($month != 99) {
            $stat->andWhere(['month' => $month]);
        }

        if($payment_id && $payment_id != 99)
            $stat->andWhere(['payment_id' => $payment_id]);

        if($company_id)
            $stat->andWhere(['company_id' => $company_id]);

        if($branch_id && $branch_id != 99)
            $stat->andWhere(['branch_id' => $branch_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $stat,
            'pagination' => false,
        ]);

        return $this->render('monthly', [
                'dataProvider'  => $dataProvider,
                'year'       => $year,
                'month'      => $month,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'payment' => $payment_id,
                'company' => $company_id,
                'branch' => $branch_id,
            ]);
    }

    /**
     * 年次集計
     */
    public function actionYearly()
    {

    }

    public function actionFetchBranch()
    {
        $company_id = Yii::$app->request->post('company_id');
        $query = \common\models\Branch::find();
        if ($company_id) {
            $query->where(['company_id' => $company_id]);
        }
        $query->orderBy(['branch_id' => SORT_ASC]);
        $branch = \yii\helpers\ArrayHelper::map($query->all(), 'branch_id', 'name');
        return \yii\helpers\Json::encode($branch);
    }


    /**
     * 売上明細CSV
     **/
    public function actionPurchaseItemCsv()
    {
        $year = Yii::$app->request->get('year') ? Yii::$app->request->get('year') : date('Y');
        $month = Yii::$app->request->get('month') ? Yii::$app->request->get('month') : date('m');

        $start_date = date($year.'-'.$month.'-01 00:00:00');
        $end_date = date($year.'-'.$month.'-t 23:59:59');

        $stat = new \backend\models\stat\PurchaseItemCsvSummary([
            'start_date'  => $start_date,
            'end_date'    => $end_date,
        ]);

        return $this->render('purchase_item_csv', [
                'year'       => $year,
                'month'      => $month,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'model'      => $stat
            ]);

    }

    /**
     * 代理店売上集計CSV
     **/
    public function actionAgencySummaryCsv()
    {
        $post = Yii::$app->request->post();
        if($post) {
            $this->exportAgencySummaryCsv($post);
            return;         
        }

        $year = Yii::$app->request->get('year') ? Yii::$app->request->get('year') : date('Y');
        $month = Yii::$app->request->get('month') ? Yii::$app->request->get('month') : date('m');

        $start_date = date($year.'-'.$month.'-01 00:00:00');
        $end_date = date($year.'-'.$month.'-t 23:59:59');
 
        $agency =  1;
        
        $stat = new \backend\models\stat\AgencyCsvSummary([
            'start_date'  => $start_date,
            'end_date'    => $end_date,
            'agency'      => $agency,
        ]);


        return $this->render('agency_summary_csv', [
                'year'       => $year,
                'month'      => $month,
                'start_date' => $start_date,
                'end_date'   => $end_date,
                'agency'     => $agency,
                'model'      => $stat
            ]);

    }

    /**
     * 代理店売上集計CSVをExport
     *
     **/
    private function exportAgencySummaryCsv($params)
    {
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $agency = $params['AgencyCsvSummary']['agency'];
        $base = date('Ymd',strtotime($start_date))."-".date('Ymd', strtotime($end_date));
        $agency_base = "";
        if($agency == 1) {
            $agency_base = 'hj';
        } else if($agency == 2) {
            $agency_base = 'he';
        } else {
            $agency_base = "";
        }
        $basename = $agency_base."_agency_summary_".$base;
        $basename .= '.csv';
        $csv = "";
        $widget = new \common\widgets\doc\purchase\AgencySummaryCsv();
        $csv  .= implode(',', $agency == 1 ? $widget->hj_header : $widget->he_header) . $widget->eol;
        $query = new \backend\models\stat\AgencyCsvSummary([
            'start_date'  => $start_date,
            'end_date'    => $end_date,
            'agency'      => $agency,
        ]);

        try {
            foreach($query->query->queryAll() as $model) {

                $csv_line = $widget::widget([
                        'model' => $model,
                ]);
                $csv .= $csv_line;
            }
            //$csv = implode('', $csv);

            if(0 === strlen($csv))
                $csv = "指定された期間に売上データがありませんでした: \n"
                        . $start_date ."〜".$end_date;

            $csv = mb_convert_encoding($csv, 'SJIS-WIN', 'UTF-8');

        } catch (ErrorException $e) {
                throw new \yii\web\NotFoundHttpException(
                    "売上集計時にエラーが発生しました [".$e->__toString()."]"
                );

             $error .= $e->__toString()."\n";
        }
        Yii::$app->response->charset = 'shift_jis';
        Yii::$app->response->sendContentAsFile($csv, $basename, ['inline'=>false, 'mimeType'=>'application/csv']);
        Yii::$app->response->send();

    }



    public function actionExportCsv()
    {
        $start_date = Yii::$app->request->get('start_date');
        $end_date = Yii::$app->request->get('end_date');
        $base = date('Ymd',strtotime($start_date))."-".date('Ymd', strtotime($end_date));
        $basename = "purchase_item_".$base;
        $basename .= '.csv';
        $csv = "";
        $widget = new \common\widgets\doc\purchase\PurchaseItemCsv();
        $csv  .= implode(',', $widget->header) . $widget->eol;
        $query = PurchaseItem::find()->select(PurchaseItem::tableName().".*")
                    ->innerJoin(['p' => Purchase::tableName()], 'dtb_purchase_item.purchase_id=p.purchase_id')->andWhere(['>=', 'p.create_date', $start_date])->andWhere(['<=', 'p.create_date', $end_date])->orderBy(['p.purchase_id' => SORT_ASC]);
        try {
            foreach($query->batch() as $models) {
                foreach($models as $model) {

                    $csv_line = $widget::widget([
                            'model' => $model,
                    ]);
                    $csv .= $csv_line;
                }
                
            }
            //$csv = implode('', $csv);

            if(0 === strlen($csv))
                $csv = "指定された期間に売上データがありませんでした: \n"
                        . $start_date ."〜".$end_date;

            $csv = mb_convert_encoding($csv, 'SJIS-WIN', 'UTF-8');
 
        } catch (ErrorException $e) {
                throw new \yii\web\NotFoundHttpException(
                    "売上集計時にエラーが発生しました [".$e->__toString()."]"
                );

             $error .= $e->__toString()."\n";
        }       
        Yii::$app->response->charset = 'shift_jis';
        Yii::$app->response->sendContentAsFile($csv, $basename, ['inline'=>false, 'mimeType'=>'application/csv']);
        Yii::$app->response->send();

    }
}
