<?php
namespace frontend\widgets;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/SearchMenuForSmartPhone.php $
 * $Id: SearchMenuForSmartPhone.php 2911 2016-10-02 04:16:03Z mori $
 */

use Yii;
use \yii\helpers\ArrayHelper;
use \yii\helpers\Html;
use \yii\helpers\Url;

use \common\models\ProductMaster;
use \common\models\ProductSubcategory;
use \common\models\Subcategory;
use \common\models\Category;
use \common\models\Company;

class SearchMenuForSmartPhone extends \yii\base\Widget
{
    public $searchModel; // SearchProduct
    public $company;     // Company model
    public $categories;  // array of Category model
    public $submenu = [];

    private $category_ids = [
        1,  // 自然食品
        3,  // 書籍
        4,  // 自然化粧品
        6,  // レメディー・ハーブ酒
        10, // 雑貨
        13  // イベント・各種サービス
    ];

    public $action;
    public $method = 'get';
    public $id     = 'product-search-global-sm';
    public $useSubCategory = true;

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
        return $this->renderHeader()
             . $this->renderContent();
    }

    private function beginWrapper()
    {
        // return'<div class="product-search" id="main-for-smart">';
        return '<div id="main-for-smart">';
    }

    private function endWrapper()
    {
        return '</div>';
    }

    private function renderHeader()
    {
        $class = ($this->useSubCategory) ? 'col-md-2' : 'col-md-6';
        // var_dump($class);exit;

        $items = [
                    [
                        'label'  => "カテゴリー",
                        'options' => ['id' => 'category', 'class' => $class],
                    ],
                    [
                        'label'  => "検索",
                        'options' => ['id' => 'search', 'class' => $class],
                    ]
                ];

        // ウィジェットの呼び出し元での指定に応じてサブカテゴリーの表示/非表示を決める
        if ($this->useSubCategory) {
            $items[] = [
                'label'  => "サブカテゴリー",  
                'options' => ['id' => 'subCategory', 'class' => $class],
            ];
        }

        // 各タブ（見出しリンク）の出力
        return '<div class="row">'. \yii\bootstrap\Nav::begin([
                    'id'      => 'search-menu-for-smart',
                    'options' => ['class' => 'nav nav-tabs search-widget'],
                    'items'   => $items,
                ])->renderItems(). '</div>';
    }

    private function renderContent()
    {
        $SearchCategoryMenu = $this->renderCategoryMenu();
        $SearchMenu         = $this->renderSearchMenu();
        $SearchSubMenu      = $this->renderSubSearchMenu();
        $SearchSubCategory  = $this->renderSubCategoryMenu();

        return $this->beginWrapper(). 
            $SearchCategoryMenu . 
            $SearchMenu.
            $SearchSubMenu.
            $SearchSubCategory. 
            $this->endWrapper();
    }

    private function renderCategoryMenu()
    {
        $categories = $this->getCategories();

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $categories
        ]);

        return \yii\widgets\ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView'     => function ($model, $key, $index, $widget)
            {
                $text  = $model->name;
                $route = sprintf('/category/%s', Html::encode($model->category_id));

                return sprintf('<div class="cat0%d"><p>', $index + 1)
                     . Html::a($text, [ $route ], [
                         // 'style' => $model->active ? '' : 'opacity:0.4',
                     ])
                     . '</p></div>';
            },
            // 'id'           => 'category-main',
            'options'      => ['class' => 'form-group field-searchform-category search-sm-main category'],
            // 'options'      => ['class' => 'category'],   
            'layout'       => '{items}',
        ]);
    }

    private function renderSearchMenu(){
        $form = new \yii\bootstrap\ActiveForm([
                    'id'     => $this->id,
                    'action' => $this->action,
                    'method' => $this->method,
                ]);
        return
              '<div class="form-group field-searchform-category search-sm-main search">'
              . $form->field($this->searchModel, 'category_id')->dropDownList(self::getDropdownItems(),['name'=>'category'])
              . $form->field($this->searchModel, 'keywords')->textInput(['name'=>'keywords'])
              
              . '<div class="form-group btn-search">'
              . Html::submitButton("検索", ['class' => 'btn btn-success', 'id' => 'search-button-sm'])
              . '</div>'
              . '</div>'
        ;
    }

    private function getDropdownItems()
    {
        if(! $this->categories)
        {
            $restrict_id = ArrayHelper::getValue(Yii::$app->user, 'identity.grade_id', 0);

            $query = Category::find()
                ->andWhere([
                    'category_id' => ProductMaster::find()
                                  ->andWhere(['<=','restrict_id',$restrict_id])
                                  ->distinct()
                                  ->select('category_id')
                ]);
            if(is_integer($this->company))
                $query->andWhere(['seller_id' => $this->company]);
            elseif($this->company)
                $query->andWhere(['seller_id' => $this->company->company_id]);

            $query->select(['category_id','name'])
                  ->andWhere(['not',['category_id'=>[5/*喫食*/,7/*講演*/,8/*健康相談*/]]]);

            $this->categories = $query->all();
        }

        $items = ['' => 0];
        foreach(ArrayHelper::map($this->categories, 'category_id', 'name') as $k => $v)
            $items[$v] = $k;

        return array_flip($items);
    }

    private function renderSubSearchMenu()
    {
        $html = '';

        if(in_array('remedy', $this->submenu))
            $html .= $this->renderRemedyLinks();

        elseif($this->company)
            $html .= \frontend\widgets\SubcategoryMenuForSmartPhone::widget([
                'company' => $this->company
            ]);

        return $html;
    }

    private function renderRemedyLinks()
    {
        $alphabet = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

        $html =
	           '<div class="form-group field-searchform-initials search-sm-main search">'
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

        $html .= \frontend\widgets\SubcategoryMenuForSmartPhone::widget([
            'company' => Company::PKEY_HJ
        ]);

        return $html;
    }

    private function renderSubCategoryMenu()
    {

        $sub_id = Yii::$app->request->get('subcategory_id', null);
        $cat_id = ArrayHelper::getColumn($this->categories, 'category_id');

        $q1 = ProductMaster::find()->where(['category_id' => $cat_id]);
        $q2 = ProductSubcategory::find()->where(['ean13'  => $q1->select('ean13')]);
        $q3 = Subcategory::find()->andWhere(['subcategory_id' => $q2->select('subcategory_id')]);
        $q4 = Subcategory::find()
                ->orWhere(['subcategory_id' => $q2->select('subcategory_id')])
                ->orWhere(['subcategory_id' => $q3->select('parent_id')])
                ->andWhere(['restrict_id'   => 0])
                ->orderBy([
                    'company_id' => SORT_ASC,
                    'parent_id'  => SORT_ASC,
                    'weight'     => SORT_DESC
                ]);
        $contents = [];

        foreach($this->categories as $category)
        {
            $query = clone($q4);
            $query->andWhere(['company_id'=>$category->seller_id]);

            if(0 == $query->count())
                continue;

            $contents[] =  \frontend\widgets\SubcategoryMenuForSmartPhone::widget([
                'title'   => $category->seller->name,
                'company' => $category->seller,
                'sub_id'  => $sub_id,
                'seeds'   => ArrayHelper::getColumn($query->all(), 'subcategory_id')
            ]);
        }

        return implode('',$contents);
    }

    private function getCategories()
    {
        return Category::find()
                    ->where(['category_id' => $this->category_ids])
                    ->andWhere(['not','category_id' => 24])
                    ->all();
    }
    
}
