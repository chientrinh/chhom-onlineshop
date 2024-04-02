<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/PickingList.php $
 * $Id: PickingList.php 4247 2020-03-25 12:34:54Z sakai $
 */

use Yii;
use \yii\helpers\ArrayHelper;
use \common\models\PurchaseItem;
use \common\models\Product;
use \common\models\LtbPurchaseRecipe;
use \common\models\Recipe;
use \common\models\Company;

class PickingList extends \yii\base\Widget
{
    /* @var Purchase model */
    public  $model;

    /* @var string */        
    public  $title = '仕訳伝票';
    
    public function init()
    {
        parent::init();

        if($this->model->shipped)
            $this->title = '仕訳伝票(再出力）';
    }

    public function run()
    {
        echo $this->beginWrapper();
        echo $this->renderHeader();
        foreach($this->model->companies as $company)
            echo $this->renderItemsOf($company);

        echo $this->endWrapper();

        echo $this->renderRecipe();

        return;
    }

    private function beginWrapper()
    {
        return '<page>';
    }

    private function endWrapper()
    {
        return '</page>';
    }

    private function renderHeader()
    {
        return $this->render('picking-header', [
            'model' => $this->model,
            'title' => $this->title,
        ]);
    }

    private function renderItemsOf($company)
    {
        $items = $this->model->getItemsOfCompany($company->company_id);

        if(Company::PKEY_HJ != $company->company_id)
        {
            ArrayHelper::multisort($items, ['pickcode','name']);

            return $this->render('picking-grid', [
                'company' => $company,
                'items'   => $items,
            ]);
        }

        /* Company::PKEY_HJ */
        $picks  = [];
        $family = [];
        $html = "";

        foreach($items as $k => $item)
        {
            if($children = $item->children)
                ArrayHelper::multisort($children, function($item) {
                    return ['code', 'name'];

                });

            if(null !== $item->parent) // skip if the row has parent
                continue;

            if(! $children && $item->pickcode)
            {
                $picks[] = $item;
                continue;
            }

            // ２次元配列を生成
            $label    = implode(';', ArrayHelper::getColumn($children, 'name'));
            $family[] = ['parent' => $item, 'children' => $children , 'label' => $label ];
        }

        // Pickコード順で並び替え
        ArrayHelper::multisort($picks, ['pickcode','name']);

        if($family) {
            $buf = [];
            foreach($family as $item)
            {
                // 一次元配列に戻す
                $buf[] = ArrayHelper::getValue($item, 'parent');
                foreach($item['children'] as $child) {
                    $buf[] = $child;
                }
            }

            $company->key .= 'ピックコードなし';
            $html = $this->render('picking-grid', [
                'company' => $company,
                'items'   => $buf,
            ]);	    

        }

        $company->key = str_replace('ピックコードなし', '', $company->key);
        
        $pick_html = $this->render('picking-grid', [
             'company' => $company,
             'items'   => $picks,
        ]);

        return $html.$pick_html;

    }

    /**
     * もし由井会長の適用書が紐付いていたら一緒に印刷する（DBで表現しきれない指示が含まれている場合があるため）
     */
    private function renderRecipe()
    {
        if($this->model instanceof \common\models\Transfer)
            return null;

        $html  = [];
        $query = Recipe::find()->where(['homoeopath_id' => 12 /* 由井寅子 */])
                               ->andWhere(['recipe_id' =>
                                   LtbPurchaseRecipe::find()->where(['purchase_id'=>$this->model->purchase_id])
                                                            ->select('recipe_id')
                               ]);
        if($query->exists()){
            $html[] = 'この注文には由井会長の適用書が紐付いています。備考欄などに特別な指示がないか、確認してください';
				}
				else{
	        $query = Recipe::find()->where(['homoeopath_id' => 6029 /* 菊田雄介 */])
                               ->andWhere(['recipe_id' =>
                                   LtbPurchaseRecipe::find()->where(['purchase_id'=>$this->model->purchase_id])
                                                            ->select('recipe_id')
                               ]);
          if($query->exists())
            $html[] = 'この注文には菊田ホメオパスの適用書が紐付いています。備考欄などに特別な指示がないか、確認してください';
        }

        foreach($query->each() as $recipe)
        {
            $html[] = $this->beginWrapper();
            $html[] = \common\widgets\doc\recipe\RecipeDocument::widget(['model'=>$recipe]);
            $html[] = $this->endWrapper();
            $html[] = '<pagebreak />';
        }
        // 白紙ページができてしまうため最後のpagebreakは削除
        unset($html[count($html) - 1]);
        return implode('', $html);
    }

}
