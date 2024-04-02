<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/views/customer-view-detail.php $
 * $Id: customer-view-detail.php 3013 2016-10-23 03:17:48Z mori $
 *
 * @var $this  yii/web/View
 * @var $model common/models/Customer
 * @var $backend bool
 */

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

?>

<h1><?= $model->name ?></h1>

<div class="col-md-12">

<?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'attribute' => 'kana',
            'visible'   => ! $backend,
        ],
        [
            'attribute' => 'kana',
            'format'    => 'html',
            'value'     => Html::a($model->kana,['/customer/view','id'=>$model->customer_id]),
            'visible'   => $backend,
        ],
        [
            'attribute' => 'code',
            'format'    => 'raw',
            'value'     => $model->code .'&nbsp;'. Html::a('更新',['attach-membercode','id'=>$model->customer_id],['class'=>'btn btn-xs btn-default']),
        ],
        [
            'attribute' => 'grade',
            'value'     => ArrayHelper::getValue($model,'grade.name'),
        ],
        'point:integer',
        [
            'label'    => '親会員',
            'attribute'=> 'parent',
            'value'    => $model->parent ? $model->parent->name : null,
            'visible'  => $model->getParent()->exists(),
        ],
        [
            'label'    => '家族会員',
            'attribute'=> 'children',
            'format'   => 'html',
            'value'    => implode('<i class="text-muted"> , </i>',ArrayHelper::getColumn($model->children,'name')),
            'visible'  => $model->getChildren()->exists(),
        ],
    ],
]) ?>

</div>
