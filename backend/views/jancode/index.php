<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/jancode/index.php $
 * $Id: index.php 2547 2016-05-27 08:54:48Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider of RemedyStock
 */

use \yii\helpers\Html;
?>

<h1>JANコード</h1>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'caption' => Html::a('全件表示', ['index','pagination'=>'false']),
    'columns' => [
        ['class'=>\yii\grid\SerialColumn::className()],
        [
            'attribute' => 'jan',
            'format'    => 'html',
            'value'     => function($data){
                return Html::a($data->jan, ['view','id'=>$data->jan]);
            },
        ],
        'sku_id',
        [
            'label' => 'code',
            'value' => function($data){
                if($p = $data->product){ return $p->code; }
                if($s = $data->stock  ){ return $s->code; }
            },
        ],
        [
            'label' => 'pick',
            'value' => function($data){
                if($p = $data->product){ return $p->pickcode; }
                if($s = $data->stock)  { return $s->pickcode; }
            },
        ],
        [
            'label' => 'name',
            'value' => function($data){
                if($p = $data->product){ return $p->name; }
                if($s = $data->stock)  { return ($r = $s->remedy) ? $r->abbr : null; }
            },
        ],
        [
            'attribute' => 'stock.potency_id',
            'value'     => function($data){
                if($s = $data->stock)  { return ($p = $s->potency) ? $p->name : null; }
            },
        ],
        [
            'attribute' => 'stock.vial_id',
            'value'     => function($data){
                if($s = $data->stock)  { return ($v = $s->vial) ? $v->name : null; }
            },
        ],
    ],
]) ?>
