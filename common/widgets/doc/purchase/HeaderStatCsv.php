<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/HeaderStatCsv.php $
 * $Id: HeaderStatCsv.php  2017-07-21 15:51:23Z kawai $
 */

use Yii;
use \common\models\Company;
use \common\models\Payment;

class HeaderStatCsv extends \yii\base\Widget
{
    /* @var Purchase model */
    public $model;


    public $eol = "\r\n";

    public $header = [
	     0 => '拠点名',
             1 => '伝票番号',
             2 => '購入日',
             3 => '発送日',
             4 => 'キャンペーン適用',
             5 => '顧客ID',
             6 => '顧客名',
             7 => '合計',
             8 => '税込（軽減）',
             9 => '税込（標準）',
             10 => '税込（出版）',
             11 => '商品計',
             12 => '消費税',
             13 => '使用ポイント',
             14 => '値引',
             15 => '付与ポイント',
             16 => '支払区分',
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
        $formatter = Yii::$app->formatter;

        $model		= $this->model;
	$branch	        = $this->getBranchName($model['branch_id']); 
	$purchase_id	= $model['purchase_id'];
	$campaign	= $this->getCampaign($model['campaign_id']);
        $customer_id    = $model['customer_id'];
	$customer	= $this->getCustomerName($model['customer_id']);
	$total_charge	= $model['total_charge'];
        $tax8_price   = $model['tax8_price'];
        $tax10_price   = $model['tax10_price'] + $model['postage'] + $model['handling']; // 送料・代引き手数料も加算する
        $taxHP_price   = $model['taxHP_price'];
	$subtotal	= $model['subtotal'];
	$tax	        = $model['tax'];
	$point_consume	= $model['point_consume'];
	$discount	= $model['discount'];
	$point_given	= $model['point_given'];
	$payment_id	= $this->getPayment($model['payment_id']);
        $create_date    = $formatter->asDate($model['create_date'], 'yyyy-MM-dd');
        $shipping_date  = $formatter->asDate($model['shipping_date'], 'yyyy-MM-dd');
        
        $items = [
		$branch, // 0	    拠点名
        	$purchase_id, // 1	    伝票番号
                $create_date, // 2  購入日
                $shipping_date, // 3 発送日
        	$campaign, // 4 	    キャンペーン適用
		$customer_id, // 5	    顧客ID
		$customer, // 6	    顧客名
		$total_charge, // 7 	    合計
                $tax8_price, // 8         税込（軽減）
                $tax10_price, // 9         税込（標準）
                $taxHP_price, // 10         税込（出版）
		$subtotal, // 11     商品計
		$tax, // 12    消費税
		$point_consume, // 13 使用ポイント
		$discount, // 14    値引
		$point_given, // 15    付与ポイント
		$payment_id // 16    支払区分
        ];

        return '"' . implode('","', $items) . '"' . $this->eol;
    }


    /**
     * 拠点名を取得する
     * @param string $data
     * @return string $name|""
     **/
    private function getBranchName($branch_id) {
	$branch = \common\models\Branch::findOne(['branch_id' => $branch_id]);
        if($branch)
            return $branch->name;

        return "";

    }

    /**
     * キャンペーン適用の有無をIDで判定
     * @param array $campaign_id
     * @return string 
     **/
    private function getCampaign($campaign_id) {
        $model = \common\models\Campaign::findOne(['campaign_id' => $campaign_id]);
        if($model)
            return "適用";

        return "なし";
    }

    /**
     * 引き渡されたPayment_idから支払い区分を返す
     * @param string $payment_id
     * @return string 支払い区分|""
     **/
    private function getPayment($payment_id) {

        $model = \common\models\Payment::findOne(['payment_id' => $payment_id]);
        if($model)
            return $model->name;

        return "";
    }

    /**
     * 引き渡されたcustomer_idから顧客名を返す
     * @param string $customer_id
     * @return string "性　名"|""
     **/
    private function getCustomerName($customer_id) {

        $model = \common\models\Customer::findOne(['customer_id' => $customer_id]);
        if($model)
            return sprintf('%s　%s', $model->name01, $model->name02);

        return "";
    }
}
