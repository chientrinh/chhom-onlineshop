<?php
namespace common\widgets\doc\invoice;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/invoice/InvoiceCsv.php $
 * $Id: StatCsv.php  2017-07-21 15:51:23Z kawai $
 */

use Yii;
use yii\helpers\ArrayHelper;

class InvoiceCsv extends \yii\base\Widget
{
    /* @var Purchase model */
    public $model;


    public $eol = "\r\n";

    public $header = [
        '請求書番号',
        '名前',
        'メールアドレス',
        '状態',
        'ご請求額',
        '今回お買い上げ額',
        'ポイント付与代金',
        '発行日',
        '更新日',
        '代理店',
        'お支払方法',
        '発行者',
        '更新者'
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
        $status = ArrayHelper::map(\common\models\InvoiceStatus::find()->all(),'istatus_id','name');
        $agency = ($model->customer->isAgency()) ? '代理店' : '個人';
        $items = [
            $model->invoice_id,
            $model->customer->name,
            $model->customer->email,
            ArrayHelper::getValue($status, $model->status),
            $model->due_total,
            $model->due_purchase,
            $model->due_pointing,
            $model->create_date,
            $model->update_date,
            $agency,
            $model->payment->name,
            $model->creator->name01,
            $model->updator->name01
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
