<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy/_stock.php $
 * $Id: _stock.php 2324 2016-03-30 01:31:29Z mori $
 *
 *
 * $allModels array of RemedyStock
 * $caption   string
 */

use \yii\helpers\Html;
use \common\models\RemedyVial;

$restrictions = \common\models\ProductRestriction::find()->all();
$restrictions = \yii\helpers\ArrayHelper::map($restrictions, 'restrict_id', 'name');

?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider([
        'allModels'  => $allModels,
        'sort'       => [
            'attributes' => [
                'code',
                'price', 'in_stock','prange.name',
                'vial.name' => [
                    'asc'  => ['vial.vial_id' => SORT_ASC],
                    'desc' => ['vial.vial_id' => SORT_DESC],
                ],
                'potency.name' => [
                    'asc'  => ['potency.weight' => SORT_ASC],
                    'desc' => ['potency.weight' => SORT_DESC],
                ],
                'restrict_id',
            ],
        ],
        'pagination' => false,
        'sort'       => false,
    ]),
    'layout' => '{items}',
    'caption' => $caption,
    'rowOptions' => function($data, $key, $index, $grid){ if($data->isNewRecord || $data->in_stock) return null; return ['class'=>'danger']; },
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'barcode',
        [
            'attribute' => 'potency.name',
            'format'    => 'html',
        ],
        [
            'attribute' => 'prange.name',
            'format'    => 'html',
        ],
        [
            'attribute' => 'vial.name',
            'format'    => 'html',
            'value'     => function($data){
                $name = $data->vial->name;

                if($data->isNewRecord || (\common\models\RemedyVial::DROP == $data->vial_id))
                    return $name;

                return $name . Html::tag('span','既製品',['class'=>'btn btn-xs btn-default pull-right']);
            },
        ],
        [
            'attribute' => 'price',
            'format'    => 'html',
            'value'     => function($data){ return '&yen;' . number_format($data->price); },
            'contentOptions' => ['class' => 'text-right'],
        ],
        [
            'attribute' => 'in_stock',
            'filter'    => [1 => "OK", 0 => "NG"],
            'format'    => 'html',
            'value'     => function($data){
                if($data->in_stock)    return "OK";
                if($data->isNewRecord) return "NG";

                return Html::tag('span','NG',['class'=>'btn btn-xs btn-danger']);
            },
        ],
        [
            'attribute' => 'restrict_id',
            'filter'    => $restrictions,
            'value'     => function($data){
                if($data->restriction)         return $data->restriction->name;
                if($data->remedy->restriction) return $data->remedy->restriction->name;
            },
        ],
        [
            'label' => '',
            'format'=> 'raw',
            'value' => function($data)
            {
                $link = [];

                if(RemedyVial::DROP != $data->vial_id)
                    $link[] = Html::a(Html::tag('i','',['class'=>'glyphicon glyphicon-shopping-cart btn btn-xs btn-success']),
                               ['/casher/default/apply',
                                'target'   => 'barcode',
                                'barcode'  => $data->barcode,
                               ],[
                                   'title' => 'レジに追加します'
                               ]).'&nbsp;';

                if($data->isNewRecord)
                    return implode('', $link);

                $link[] = Html::a(Html::tag('i','&nbsp;',['class'=>'glyphicon glyphicon-eye-open']),
                               ['/remedy-stock/view',
                                'remedy_id'  => $data->remedy_id,
                                'potency_id' => $data->potency_id,
                                'vial_id'    => $data->vial_id,
                               ]);

                $link[] = Html::a(Html::tag('i','&nbsp;',['class'=>'glyphicon glyphicon-pencil']),
                               ['/remedy-stock/update',
                                'remedy_id'  => $data->remedy_id,
                                'potency_id' => $data->potency_id,
                                'vial_id'    => $data->vial_id,
                               ]);

                return implode('', $link);
            },
        ],
    ],
]); ?>
