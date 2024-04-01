<?php 
namespace backend\modules\casher\widgets;
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/widgets/CartContentGrid.php $
 * $Id: CartContentGrid.php 3784 2017-12-06 02:39:53Z kawai $
 */

use Yii;
use \yii\helpers\Html;
use \yii\helpers\Url;

class CartContentGrid extends \yii\base\Widget
{
    public $items; // array of PurchaseItem

    public function run()
    {
        // アイテムリストの最終Index
        $last_idx = count($this->items)-1;

        echo \yii\grid\GridView::widget([
            'dataProvider' => new \yii\data\ArrayDataProvider([
                'allModels'  => $this->items,
                'pagination' => false,
            ]),
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'layout'  => '{items}{pager}',
            'columns' => [
                [
                    'attribute' => 'name',
                    'label'     => '商品',
                    'format'    => 'html',
                    'value'     => function($data,$key,$idx,$col)use($last_idx){
                        return Html::a('',['apply','target'=>'quantity','seq'=>$last_idx-$idx,'vol'=>(0 - $data->quantity)],['class'=>'btn btn-xs glyphicon glyphicon-remove item','style'=>'color:#ccc'])
                                   . ($data instanceof \common\models\PurchaseItem ? $data->shortName : $data->name);
                    },
                ],
                [
                    'attribute' => 'quantity',
                    'label'     => '数量',
                    'format'    => 'html',
                    'value'     => function($data,$key,$idx,$col)use($last_idx){
                        return Html::a('',['apply','target'=>'quantity','seq'=>$last_idx-$idx,'vol'=>-1],['class'=>'btn btn-xs glyphicon glyphicon-minus item'])
                                . ' ' 
                                . Html::tag('strong',$data->quantity) 
                                . ' '
                                . Html::a('',['apply','target'=>'quantity','seq'=>$last_idx-$idx,'vol'=>1],['class'=>'btn btn-xs glyphicon glyphicon-plus item']);
                    },
                    'headerOptions'   => ['class' => 'col-md-4'],
                ],
            ],
        ])
        . Html::a('',Url::to(['search','target'=>Yii::$app->request->get('target')]),['class'=>'glyphicon glyphicon-refresh']);

    }
}

 ?>

