<?php
namespace frontend\widgets;

use Yii;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/ProductListView.php $
 * $Id: ProductListView.php 4251 2020-04-24 17:19:55Z mori $
 *
 * $dataProvider 
 * $searchModel
 * $grid          bool
 */

class ProductListView extends \yii\base\Widget
{
    public $dataProvider;
    public $searchModel;
    public $grid;

    /* @var bool : show price label for every Remedy item */
    public $remedyStock = false;

    public function getViewPath()
    {
        return '@frontend/widgets/views/';
    }

    public function init()
    {
        parent::init();

        $this->dataProvider->pagination->pagesize = 42;
        $this->dataProvider->setSort([
            'attributes' => [
                'name' => [
                    'asc'   => ['kana' => SORT_ASC ],
                    'desc'  => ['kana' => SORT_DESC],
                    'label' => '名前',
                    'default' => SORT_ASC,
                ],
                'price' => [
                    'asc'   => ['price' => SORT_ASC ],
                    'desc'  => ['price' => SORT_DESC],
                    'label' => '価格',
                    'default' => SORT_ASC,
                ],
                'dsp_priority',
            ],
            'defaultOrder'    => ['dsp_priority' => SORT_DESC],

            'enableMultiSort' => false,
        ]);

        $this->searchModel = new \frontend\models\SearchProductMaster(['customer'=>Yii::$app->user->identity]);

    }

    public function run()
    {
        $echom = '';
        if('echom-frontend' == Yii::$app->id) {
            $echom = 'echom/';
        }
        if($this->grid)
            return $this->render($echom.'product-grid-view',[
                'dataProvider' => $this->dataProvider,
                'searchModel'  => $this->searchModel,
                'remedyStock'  => $this->remedyStock,
            ]);
        else
            return $this->render($echom.'product-list-view',[
                'dataProvider' => $this->dataProvider,
                'searchModel'  => $this->searchModel,
                'remedyStock'  => $this->remedyStock,
            ]);
    }

}
