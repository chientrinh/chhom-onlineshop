<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/PurchaseItemCsv.php $
 * $Id: PurchaseItemCsv.php  2020-02-13 11:58:23Z kawai $
 */

use Yii;
use \common\models\Company;
use \common\models\Payment;
use \common\models\Purchase;

class PurchaseItemCsv extends \yii\base\Widget
{
    /* @var PurchaseItem model */
    public $model;


    public $eol = "\r\n";

    public $header = [
            '伝票ID',
            'ステータス',
            '売上日',
            '顧客ID',
            '顧客名',
            '代理店',
            'キャンペーンID',
            'キャンペーン名称',
            '商品ID',
            'レメディーID',
            'コード',
            '商品名',
            '販社ID',
            '数量',
            '定価',
            '単価',
            '消費税',
            '税別価格',
            '税込価格',
            '値引額',
            '値引率',
            'ポイント付与額',
            'ポイント率',
            '消費税率',
            '拠点ID',
            '製造元',
            '大分類',
            '中分類',
            '小分類',
            'sku_id',
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
        $model = $this->model->attributes;

        if (Yii::$app->getRequest()->getQueryParam('start_date') && Yii::$app->getRequest()->getQueryParam('end_date')) {
            $start_date = date('Ymd', strtotime(Yii::$app->getRequest()->getQueryParam('start_date')));
            $end_date = date('Ymd', strtotime(Yii::$app->getRequest()->getQueryParam('end_date')));
        } else {
            $start_date = (Yii::$app->request->get('month') != 99) ? date('Ym01', strtotime(Yii::$app->request->get('year') . '-' . Yii::$app->request->get('month'))) : date('Y0101', strtotime(Yii::$app->request->get('year')));
            $end_date = (Yii::$app->request->get('month') != 99) ? date('Ymt', strtotime(Yii::$app->request->get('year') . '-' . Yii::$app->request->get('month'))) : date('Y1231', strtotime(Yii::$app->request->get('year')));
        }
        $sales = \common\models\SalesCategory::find()->where(['sku_id' => $model['sku_id']])->one();
        $salesCategory = $sales ? $sales->attributes : null;
        $purchase = \common\models\Purchase::find()->where(['purchase_id' => $model['purchase_id']])->one()->attributes;
        $items = [
            $model['purchase_id'],
            $purchase['status'],
            $purchase['shipping_date'] ? $purchase['shipping_date'] : "",
            $purchase['customer_id'] ? $purchase['customer_id'] : "",
            $purchase['customer_id'] ? $this->getCustomerName($purchase['customer_id']) : "",
            $model['is_wholesale'] ? $model['is_wholesale'] : "",
            $model['campaign_id'] ? $model['campaign_id'] : "",
            $model['campaign_id'] ? $this->getCampaignName($model['campaign_id']) : "",
            $model['product_id'] ? $model['product_id'] : "",
            $model['remedy_id'] ? $model['remedy_id'] : "",
            $model['code'],
            $model['name'],
            $model['company_id'],
            $model['quantity'],
            $model['price'],
            $model['unit_price'],
            $model['unit_tax'],
            $model['unit_price']*$model['quantity'],
            $model['unit_tax']*$model['quantity'],
            $model['discount_amount'],
            $model['discount_rate'],
            $model['point_amount'],
            $model['point_rate'],
            $model['tax_rate'],
            $purchase['branch_id'],
            $salesCategory ? $salesCategory['vender_key'] : "",
            $salesCategory ? $salesCategory['bunrui_code1'] : "",
            $salesCategory ? $salesCategory['bunrui_code2'] : "",
            $salesCategory ? $salesCategory['bunrui_code3'] : "",
            $salesCategory ? $salesCategory['sku_id'] : "",
        ];
        return '"' . implode('","', $items) . '"' . $this->eol;
    }

    private function getCampaignName($campaign_id) {
        $campaign = \common\models\Campaign::findOne($campaign_id);
        return $campaign->campaign_name;
    }

    private function getCustomerName($customer_id) {
        $customer = \common\models\Customer::findOne($customer_id);
        return $customer->name;
    }

    private function getCategoryId($data) {
        $category_id = $data['category_id'];
        $company_id = $data['company_id'];

        // 生野菜、酒類は別カテゴリ化
        if ($category_id == '99') {
            $summary_category = \common\models\SummaryCategory::find()->where(['summary_category_id' => $company_id . '97'])->one();
        } else if ($category_id == '98') {
            $summary_category = \common\models\SummaryCategory::find()->where(['summary_category_id' => $company_id . $category_id])->one();
        } else if (\common\models\Category::findOne($category_id)) {
            $summary_category = \common\models\SummaryCategory::find()->where(['category_id' => $category_id])->one();
        } else {
            $summary_category = \common\models\SummaryCategory::find()->where(['summary_category_id' => $company_id . '99'])->one();
        }
        if (!$summary_category) {
            return null;
        }
        return $summary_category->summary_category_id;
    }

    /**
     * 商品の属するカテゴリの名前を取得する
     * @param array $data
     * @return $name|""
     **/
    private function getCategoryName($data) {
        $category_id = $data['category_id'];
        $company_id = $data['company_id'];

        // 生野菜、酒類は別カテゴリ化
        if ($category_id == '99') {
            $summary_category = \common\models\SummaryCategory::find()->where(['summary_category_id' => $company_id . '97'])->one();
        } else if ($category_id == '98') {
            $summary_category = \common\models\SummaryCategory::find()->where(['summary_category_id' => $company_id . $category_id])->one();
        } else if (\common\models\Category::findOne($category_id)) {
            $summary_category = \common\models\SummaryCategory::find()->where(['category_id' => $category_id])->one();
        }  else {
            $summary_category = \common\models\SummaryCategory::find()->where(['summary_category_id' => $company_id . '99'])->one();
        }
        if (!$summary_category) {
            return 'その他';
        }
        return $summary_category->name;
    }
}
