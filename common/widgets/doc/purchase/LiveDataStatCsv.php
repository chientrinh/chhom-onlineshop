<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/LiveDataStatCsv.php $
 * $Id: HeaderStatCsv.php  2020-04-27 09:51:23Z kawai $
 */

use Yii;
use \common\models\Company;
use \common\models\Payment;

class LiveDataStatCsv extends \yii\base\Widget
{
    /* @var Purchase model */
    public $model;


    public $eol = "\r\n";

    public $header = [
             0 => '注文番号',
             1 => '拠点ID',
             2 => '購入日',
             3 => '顧客ID',
             4 => '会員ランク',
             5 => '顧客名',
             6 => 'かな',
             7 => 'メールアドレス',
             8 => '電話番号',
             9 => '商品名',
             10 => '数量',
             11 => '税込金額',
             12 => '支払方法',
             13 => '状態',
             14 => '備考（お客様）',
             15 => '備考',
             16 => 'サポート申込ID',
             17 => 'サポート申込者',
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
        $model  		= $this->model;
        $branch_id      = isset($model['branch_id']) ? $model['branch_id'] : 16;
        $branch	        = $this->getBranchName($branch_id);
	$purchase_id	= $model['purchase_id'];
        $customer_id    = $model['customer_id'] ? $model['customer_id'] : "";
        $customer       = $model['customer_id'] ? $model['customer_name'] : $model['delivery_customer_name'];
        $kana           = $model['customer_id'] ? $model['customer_kana'] : "";
        $grade          = isset($model['customer_grade']) ? $model['customer_grade'] : $this->getCustomerGrade($customer_id);
	$payment_id	    = $this->getPayment($model['payment_id']);
        $create_date    = $model['create_date'];
        $email          = $model['email'];
        $tel            = $model['tel'];
        $product        = $model['product_name'];
        $price          = $model['basePrice'];
        $qty            = $model['quantity'];
        $status         = $model['status'];
        $note           = in_array((int)$model['product_id'], [1763,1764,1774]) ? "" : $model['note'];

        $customer_msg   = in_array((int)$model['product_id'], [1763,1764,1774]) ? str_replace("\n".$model['note'], "",$model['customer_msg']) : $model['customer_msg']; 
        $agent_id         = isset($model['agent_id']) ? $model['agent_id'] : "";
        $agent_name         = isset($model['agent_id']) ? $this->getCustomerName($model['agent_id']) : ""; 
        
        $items = [
            $purchase_id, // 0	    注文番号
            $branch_id,   // 1      拠点ID
            $create_date, // 2      購入日
            $customer_id, // 3      顧客ID
            $grade,       // 4      会員ランク
            $customer,    // 5      顧客名
            $kana,       // 6       かな
            $email,       // 7      メールアドレス
            $tel,         // 8      電話番号
            $product,     // 9      商品名
            $qty,          // 10      数量
            $price,       // 11      税込金額
            $payment_id,   // 12      支払方法
            $status,       // 13     ステータス（運用時は出さない
            $customer_msg,   // 14    備考（お客様）
            $note,          // 15    備考
            $agent_id,          // 16    サポート申込ID
            $agent_name,          // 17    サポート申込者

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
     * 引き渡されたmodelから会員ランクを返す
     * @param Purchase $model
     * @return string "grade"|""
     **/
    private function getCustomerGrade($customer_id) {
        if($customer_id != "")
           $model = \common\models\Customer::findOne(['customer_id' => $customer_id]);
           if(isset($model))
              return sprintf('%s', $model->grade->name);

        return "";
    }

    /**
     * 引き渡されたmodelから顧客名を返す
     * @param customer_id $id
     * @return string "性　名"|""
     **/
    private function getCustomerName($id) {
        $model = \common\models\Customer::findOne(['customer_id' => $id]);
        if($model)
            return sprintf('%s　%s', $model->name01, $model->name02);

        return "";
    }
}
