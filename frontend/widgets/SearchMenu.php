<?php
namespace frontend\widgets;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/SearchMenu.php $
 * $Id: SearchMenu.php 4248 2020-04-24 16:29:45Z mori $
 */

use Yii;
use \yii\helpers\Html;

class SearchMenu extends \yii\base\Widget
{
    public $searchModel; // SearchProduct
    public $company;     // Company model
    public $categories;  // array of Category model
    public $submenu = [];

    public $action;
    public $method = 'get';
    public $id     = 'product-search-global';

    public function init()
    {
        parent::init();

        if(! $this->action)
             $this->action = [Yii::$app->controller->route];

        if(is_array($this->searchModel->category_id))
            foreach(self::getDropdownItems() as $k => $item)
                if(in_array($k, $this->searchModel->category_id))
                {
                    $this->searchModel->category_id = $k;
                    break;
                }

    }

    public function run()
    {
        return $this->beginWrapper()
             . $this->renderContent()
             . $this->endWrapper();
    }

    private function beginWrapper()
    {
        return '<div class="product-search">
			    <div class="inner">
			    <h3>商品検索</h3>';
    }

    private function endWrapper()
    {
        return '</div>
			    </div>';
    }

    private function renderContent()
    {
        $form = new \yii\bootstrap\ActiveForm([
                    'id'     => $this->id,
                    'action' => $this->action,
                    'method' => $this->method,
                ]);
		$menu =
              '<div class="form-group field-searchform-category">'
              . $form->field($this->searchModel, 'category_id')->dropDownList(self::getDropdownItems(),['name'=>'category'])
              . $form->field($this->searchModel, 'keywords')->textInput(['name'=>'keywords'])
              . '</div>'
              . '<div class="form-group btn-search">'
              . Html::submitButton("検索", ['class' => 'btn btn-success', 'id' => 'search-button'])
	          . '</div>'
        ;
        $submenu = '';

        $submenu = $this->renderSubmenu();

        return $menu . $submenu;
    }

    private function getDropdownItems()
    {
        if(! $this->categories)
        {
            $restrict_id = \yii\helpers\ArrayHelper::getValue(Yii::$app->user, 'identity.grade_id', 0);

            $query = \common\models\Category::find()
                ->andWhere([
                    'category_id' => \common\models\ProductMaster::find()
                                  ->andWhere(['<=','restrict_id',$restrict_id])
                                  ->distinct()
                                  ->select('category_id')
                ]);
            if(is_integer($this->company))
                $query->andWhere(['seller_id' => $this->company]);
            elseif($this->company)
                $query->andWhere(['seller_id' => $this->company->company_id]);

            $query->select(['category_id','name'])
                  ->andWhere(['not',['category_id'=>[5/*喫食*/,7/*講演*/,8/*健康相談*/,24/*ライブ配信チケット*/]]]);

            $this->categories = $query->all();
        }

        $items = ['' => 0];
        foreach(\yii\helpers\ArrayHelper::map($this->categories, 'category_id', 'name') as $k => $v)
            $items[$v] = $k;

        return array_flip($items);
    }

    private function renderSubMenu()
    {
        $html = '';

        if(in_array('remedy', $this->submenu))
            $html .= $this->renderRemedyLinks();

        elseif($this->company)
            $html .= \frontend\widgets\SubcategoryMenu::widget([
                'company' => $this->company
            ]);

        return $html;
    }

    private function renderRemedyLinks()
    {
        $alphabet = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

        $html =
	           '<div class="form-group field-searchform-initials">'
		     . '<label class="control-label line-dot" for="searchform-subcategory">レメディー検索</label>'
	          . '<ul class="initial">';

        foreach($alphabet as $chr)
            $html .= Html::tag('li',Html::a($chr, ['/remedy/indexof', 'firstLetter'=>$chr]));
        $html .= '</ul>';

        $html .= '<ul>'
              . Html::tag('li','&nbsp;',['style'=>'list-style-type:none'])
//              . Html::tag('li',Html::a('定番レメディー',['remedy/popular']))
              . Html::tag('li',Html::a('単品レメディー',['remedy/search']))
              . Html::tag('li',Html::a('適用書レメディーの購入', ['/recipe/review/index']))
              .'</ul>'
              .'</div>';

        $html .= \frontend\widgets\SubcategoryMenu::widget([
            'company' => \common\models\Company::PKEY_HJ,
        ]);

        return $html;
    }
    
}
