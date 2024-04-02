<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/StatCsv.php $
 * $Id: StatCsv.php  2017-07-21 15:51:23Z kawai $
 */

use Yii;
use \common\models\Company;
use \common\models\Payment;

class StatCsv extends \yii\base\Widget
{
    /* @var Purchase model */
    public $model;


    public $eol = "\r\n";

    public $header = [
	     0 => '販社ID',
             1 => 'カテゴリ',
             2 => 'コード',
             3 => '商品名',
             4 => '定価',
             5 => '数量',
             6 => '定価小計',
             7 => '値下げ小計',
             8 => 'ポイント小計',
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
	$company	= $model['company_id']; 
	$category	= $this->getCategoryName($model);
	$code		= $model['code'];
	$name		= $model['name'];
	$price		= $model['price'];
	$quantity	= $model['quantity'];
	$basePrice	= $model['basePrice'];
	$pointTotal	= $model['pointTotal'];
	$discountTotal	= $model['discountTotal'];

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
     *
     *
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

}
