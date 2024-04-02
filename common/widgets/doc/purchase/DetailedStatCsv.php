<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/DetailedStatCsv.php $
 * $Id: DetailedStatCsv.php  2017-07-21 15:51:23Z kawai $
 */

use Yii;
use \common\models\Company;
use \common\models\Payment;

class DetailedStatCsv extends \yii\base\Widget
{
    /* @var Purchase model */
    public $model;


    public $eol = "\r\n";

    public $header = [
             0 => 'カテゴリ',
             1 => '商品名',
             2 => '定価',
             3 => '数量',
             4 => 'キャンペーン適用',
             5 => '割引率',
             6 => '割引額',
             7 => 'ポイント率',
             8 => '伝票番号',
             9 => '定価小計',
             10 => '値下げ小計',
             11 => '小計',
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

        $model		= $this->model;
	$category	= $this->getCategoryName($model); // カテゴリー
//	$code		= $this->getBarCode($model['code']); // コード
	$name		= $model['name']; // 商品名
	$price		= $this->getPrice($model); // 定価
	$quantity	= $model['quantity']; // 数量
	$campaign	= $model['campaign_id']; // キャンペーンID
	$basePrice	= $model['basePrice']; // 定価＊数量
//	$pointTotal	= $model['pointTotal']; // 
//	$pointTotal	= $model['pointTotal']; // 
//	$pointTotal	= $model['pointTotal']; // 
	$discountTotal	= $model['discountTotal']; // 割引額＊数量
	$subTotal	= ($basePrice - $discountTotal) * $quantity; // （定価ー割引額）＊数量

        $items = [
		$company, // 0	    販社ID
        	$category, // 1	    カテゴリ
        	$code, // 2 	    コード
		$name, // 3	    商品名
		$price, // 4 	    定価
		$quantity, // 5     数量
		$basePrice, // 6    定価小計
		$discountTotal, // 7 値下げ小計
		$pointTotal // 8    ポイント小計
        ];

        return '"' . implode('","', $items) . '"' . $this->eol;
    }


    /**
     * 商品の属するカテゴリの名前を取得する
     * @param array $data
     * @return $name|""
     **/
    private function getCategoryName($data) {
	$product = \common\models\Product::findOne(['product_id' => $data['product_id']]);
        if($product)
            return $product->category->name;

        $remedy = \common\models\RemedyStock::findOne([
           'remedy_id' => $data['remedy_id'],
        ]);
        if($remedy)
            return $remedy->category->name;

        return "";

    }

    /**
     * 商品が「生野菜」でない限り、価格を出力させる
     * @param array $data
     * @return string $price|null
     **/
    private function getPrice($data) {
        // 生野菜という商品と紐付く、野菜管理上の商品でない限り、価格を表示させる
        $model = \common\models\Product::findOne($data['product_id']);
        if($model && $model->name != \common\models\Vegetable::PRODUCT_NAME)
            return $data['price'];

        return null;
    }

    /**
     * 引き渡されたcode(ean13)が野菜のものなら、VEG_IDに変換して返す
     * @param string $code
     * @return string $code|$veg_id
     **/
    private function getBarCode($code) {

        // 野菜かどうかは、Vegetable：EAN13＿PREFIXに先頭が一致するかで判定できる
        $veg_prefix = \common\models\Vegetable::EAN13_PREFIX;

        if($veg_prefix == substr($code, 0, strlen($veg_prefix))) {
            $veg_id = substr($code, 2, 5);
            return $veg_id; 
        }

        return $code;

    }

}
