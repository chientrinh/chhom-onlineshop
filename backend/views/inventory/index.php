<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/inventory/index.php $
 * $Id: index.php 2321 2016-03-27 09:06:17Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$branches = ArrayHelper::map(\common\models\Branch::find()->all(),'branch_id','name');
?>
<div class="inventory-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'inventory_id',
                'format'    => 'html',
                'value'     => function($data){
                    $id = sprintf('%06d',$data->inventory_id);
                    return Html::a($id,['view','id'=>$id]);
                },
            ],
            [
                'attribute' => 'create_date',
                'format'    => ['date', 'php:Y-m-d'],
            ],
            [
                'attribute' => 'branch_id',
                'value'     => function($data){ return ($b = $data->branch) ? $b->name : null; },
            ],
            [
                'attribute' => 'istatus_id',
                'format'    => 'html',
                'value'     => function($data){
                    $name = ($s = $data->status) ? $s->name : null;
                    $link = $data->isApproved() ? Html::a('<i class="glyphicon glyphicon-download-alt"></i>',['print','id'=>$data->inventory_id,'format'=>'csv'],['class'=>'pull-right btn btn-xs btn-warning','title'=>'原価を集計します']) : null;

                    return $name . $link;
                },
            ],
            [
                'attribute' => 'updated_by',
                'value'     => function($data){ return ($u = $data->updator) ? $u->name : null; },
            ],
            [
                'attribute' => 'update_date',
                'format'    => ['date', 'php:Y-m-d H:i'],
            ],
        ],
    ]); ?>

</div>
