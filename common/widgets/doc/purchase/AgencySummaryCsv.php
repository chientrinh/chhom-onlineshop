<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/AgencySummaryCsv.php $
 * $Id: AgencySummaryCsv.php  2020-03-11 14:58:23Z kawai $
 */

use Yii;
use \common\models\Company;
use \common\models\Payment;
use \common\models\Purchase;

class AgencySummaryCsv extends \yii\base\Widget
{
    /* @var AgencySummary model */
    public $model;


    public $eol = "\r\n";

    public $hj_header = [
            '顧客ID',
            '顧客名',
            '数量',
            '定価合計（税別）',
            '卸価格合計（税別）',
            '税額',
            '卸価格合計（税込）',
            'HJ代理店ランク',
        ];

    public $he_header = [
            '顧客ID',
            '顧客名',
            '数量',
            '定価合計（税別）',
            '卸価格合計（税別）',
            '税額',
            '卸価格合計（税込）',
            'HE代理店割引率',
        ];


    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $text = $this->renderCsv();

        return $text;
    }

    private function renderCsv()
    {
        $model = $this->model;
        
//        $agency =  Yii::$app->request->post('AgencyCsvSummary')['agency'];


/*
        $items = [
            $model['customer_id'],
            $model['name'],
            $model['quantity'],
            $model['price_total'],
            $model['wholesale_total_excluding_tax'],
            $model['tax'],
            $model['wholesale_total'],
        ];

        if($agency == 1) {
            $items[] = $model['hj_agency_rank'];
        } else if($agency == 2) {
            $items[] = $model['he_agency_rate'];
        }
*/
        return '"' . implode('","', $model) . '"' . $this->eol;
    }
}
