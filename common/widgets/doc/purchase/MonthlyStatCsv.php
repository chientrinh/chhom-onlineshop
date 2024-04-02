<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/MonthlyStatCsv.php $
 * $Id: StatCsv.php  2017-07-21 15:51:23Z kawai $
 */

use Yii;
use \common\models\Company;
use \common\models\Payment;

class MonthlyStatCsv extends \yii\base\Widget
{
    /* @var Purchase model */
    public $model;


    public $eol = "\r\n";

    public $header = [
            '会社ID',
            '会社',
            '拠点ID',
            '拠点',
            'カテゴリーID',
            'カテゴリー',
            '商品計',
            '返品計',
            '値引計',
            '売上合計',
            '消費税',
            '値引き',
            'ポイント値引き',
            '純売上（税込）',
            '送料・手数料',
            '数量',
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
        $items = [
            $model['company_id'],
            $this->getCompanyName($model),
            $model['branch_id'],
            $this->getBranchName($model),
            $this->getCategoryId($model),
            $this->getCategoryName($model),
            isset($model['basePrice']) ? $model['basePrice'] : 0,
            isset($model['returnCharge']) ? $model['returnCharge'] : 0,
            isset($model['discountTotal']) ? $model['discountTotal'] : 0,
            isset($model['basePrice']) ? $model['basePrice'] - $model['returnCharge'] - $model['discountTotal'] : 0,
            isset($model['taxTotal']) ? $model['taxTotal'] : 0,
            $model['discount'],
            $model['point_consume'],
            isset($model['basePrice']) ? $model['basePrice'] + $model['taxTotal'] - $model['discount'] - $model['point_consume'] - $model['discountTotal'] : 0,
            $model['postage'] + $model['handling'],
            isset($model['quantity']) ? $model['quantity'] : 1
        ];

        return '"' . implode('","', $items) . '"' . $this->eol;
    }

    private function getCompanyName($data) {
        $company = Company::findOne($data['company_id']);
        return $company->name;
    }

    private function getBranchName($data) {
        $branch = \common\models\Branch::findOne($data['branch_id']);
        return $branch->name;
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
        }  else {
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
        } else {
            $summary_category = \common\models\SummaryCategory::find()->where(['summary_category_id' => $company_id . '99'])->one();
        }
        if (!$summary_category) {
            return 'その他';
        }
        return $summary_category->name;
    }
}
