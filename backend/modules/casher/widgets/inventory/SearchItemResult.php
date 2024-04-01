<?php
namespace backend\modules\casher\widgets\inventory;
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/widgets/inventory/SearchItemResult.php $
 * $Id: SearchItemResult.php 2201 2016-03-05 09:18:56Z mori $
 */

use \yii\helpers\ArrayHelper;
use \yii\helpers\Html;
use \common\models\InventoryItem;
use \common\models\Product;
use \common\models\ProductMaster;
use \common\models\RemedyVial;

class SearchItemResult extends \yii\base\Widget
{
    /*
     * @var string
     */
    public $keyword;

    /*
     * @var Branch model
     */
    public $branch;

    /*
     * @var Inventory model
     */
    public $inventory;

    public function init()
    {
        parent::init();
    }

    public function getQuery()
    {
        $keyword = $this->keyword;

        if(! $keyword)
            return ProductMaster::find()->andWhere('1 = 0');

        $query = ProductMaster::find();
        $query->andWhere(['or',
                       ['like','kana',$keyword],
                       ['like','ean13',$keyword], ])
              ->andWhere(['not',['vial_id'=>RemedyVial::DROP]])
              ->andWhere(['not',['ean13'=>
                  InventoryItem::find()->where(['inventory_id' => $this->inventory->inventory_id])
                                       ->select('ean13')
                                       ->column()
              ]]);

        if(0 == $query->count())
            $query = Product::find()
                   ->andWhere(['or',
                       ['like','code',$keyword],
                       ['like','name',$keyword],
                       ['like','kana',$keyword], ]);

        if(0 == $query->count())
            $query->andWhere(['or',
                             ['like','summary',$keyword],
                             ['like','description',$keyword]]);

        return $query;
    }

    public function run()
    {
        $id = $this->inventory->inventory_id;

        echo \yii\grid\GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query'  => $this->query,
                'pagination' => [
                    'pageSize' => 50,
                    'pageParam' => 'sip', // stands for Search Item Page
                ],
            ]),
            'tableOptions' => ['class'=>'table-condensed'],
            'showHeader'   => false,
            'showOnEmpty'  => false,
            'emptyText'    => '',
            'layout'       => '{items}<span class="hint-block">{summary}</span>',
            'columns'      => [
                [
                    'attribute' => 'kana',
                    'format'    => 'html',
                    'value'     => function($data)
                    {
                        if(strlen($data->kana) < strlen($data->name))
                            return $data->name;
                        else
                            return $data->kana;
                    },
                ],
                'price:currency',
                [
                    'label' => '',
                    'format' => 'html',
                    'value'  => function($data)use($id)
                    {
                        if($data instanceof ProductMaster)
                            $ean13 = $data->ean13;
                        else
                            $ean13 = $data->barcode;

                        return Html::a('',['create-item','id'=>$id,'ean13'=>$ean13],[
                            'class' => 'glyphicon glyphicon-open btn btn-success',
                            'title' => '棚卸商品として追加する',
                        ]);
                    }
                ],
            ]
        ]);
    }
}
