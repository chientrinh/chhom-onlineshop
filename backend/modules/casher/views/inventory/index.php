<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/inventory/index.php $
 * $Id: index.php 2049 2016-02-07 02:58:54Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$branches = ArrayHelper::map(\common\models\Branch::find()->all(),'branch_id','name');
?>
<div class="casher-inventory-index">

    <p class="pull-right">
        <?= Html::a('新規作成',['create'],['class'=>'btn btn-success']) ?>
    </p>

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
                'value'     => function($data){ return ($s = $data->status) ? $s->name : null; },
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

    <p class="pull-right">
        <?= Html::a('全店表示',['/inventory/index'],['class'=>'btn btn-default','title'=>'すべての棚卸を表示します']) ?>
    </p>

</div>
