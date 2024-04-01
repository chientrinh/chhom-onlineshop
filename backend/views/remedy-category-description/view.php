<?php
/**
 * $URL: $
 * $Id: $
 *
 * @var $this yii\web\View
 * @var $customer common\models\Customer
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = 'レメディー共通補足説明: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => $model->title];

$prev_id = $model->prev;
$next_id = $model->next;
?>
<div class="customer-addrbook-view">

<p class="pull-right">
<?php if(isset($prev_id)): ?>
    <?= Html::a("", ['view', 'id' => $prev_id], ['title' => "前のレメディー共通補足説明",'class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-left']) ?>
<?php endif ?>

<?php if(isset($next_id)): ?>
    <?= Html::a("", ['view', 'id' => $next_id], ['title' => "次のレメディー共通補足説明",'class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-right']) ?>
<?php endif ?>
</p>

<h1><?= $model->title ?></h1>

<?= \yii\widgets\DetailView::widget([
    'model'  => $model,
//     'options'=> ['class' => 'table table-striped table-bordered'],
    'attributes' => [
        [
            'attribute' => 'body',
            'value'     => nl2br($model->body),
            'format'    => 'raw'
        ],
        'seq:html',
        [
            'attribute' => 'is_display',
            'label'     => '表示/非表示',
            'value'     => $model->displayName,
        ],
        [
            'attribute' => 'create_by',
            'value'     => $model->creator->name01
        ],
        'create_date:datetime',
        [
            'attribute' => 'update_by',
            'value'     => $model->updator->name01
        ],
        'update_date:datetime'
    ],
    'template' => "<tr><th class=\"col-md-2\">{label}</th><td class=\"col-md-8\" style=\"word-break:break-all;\">{value}</td></tr>" // template形式
]);?>
    <div class="pull-left">
        <?= Html::a("編集", ['update', 'id' => $model->remedy_category_desc_id], ['class' => 'btn btn-primary']) ?>
    </div>
    <div class="pull-right">
        <?= Html::a("削除", ['delete', 'id' => $model->remedy_category_desc_id], ['class' => 'btn btn-danger', 'data-confirm'=>'補足説明を削除します。よろしいですか。']) ?>
    </div>


</div>
