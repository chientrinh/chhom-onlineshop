<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use common\models\RemedyCategory;
use common\models\RemedyDescription;

/* @var $this yii\web\View */
/* @var $model common\models\RemedyDescription */
/* @var $form yii\widgets\ActiveForm */

$prev_id = $model->prev;
$next_id = $model->next;
$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => $model->remedy->abbr, 'url' => ['remedy/view','id'=> $model->remedy_id]];
$this->params['breadcrumbs'][] = ['label' => '補足', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title];

?>
<div class="customer-addrbook-view">

<?= Html::a("補足を追加",['remedy-description/create','remedy_id'=>$model->remedy_id],['class'=>'btn btn-success']) ?>

<p class="pull-right">

<?= Html::a("戻る",Yii::$app->request->getReferrer(), ['class' => 'btn btn-default']) ?>

</p>

<h1><?= Html::encode($this->title) ?></h1>

<?= DetailView::widget([
        'model' => $model,
        'attributes' => [

                [
                    'attribute' => 'desc_division',
                    'label'     => '説明区分',
                    'value'     => RemedyDescription::getDivisionForView($model->desc_division)
                ],
                'remedyCategory.remedy_category_name',
                [
                    'attribute' => 'body',
                    'value'     => nl2br($model->body),
                    'format'    => 'raw',
                    'label'     => '本文',

                ],
                'seq:html',
                [
                    'attribute' => 'is_display',
                    'label'     => '表示/非表示',
                    'value'     => $model->displayName,
                ],
                [
                    'label'     => '作成者',
                    'value'     => $model->creator->name01
                ],
                'create_date:datetime',
                [
                    'label'     => '更新者',
                    'value'     => $model->updator->name01
                ],
                'update_date:datetime'
            ],
        'template' => "<tr><th class=\"col-md-2\">{label}</th><td class=\"col-md-8\" style=\"word-break:break-all;\">{value}</td></tr>" // template形式

    ]) ?>
    <div class="pull-left">
        <?= Html::a("編集", ['update', 'id' => $model->remedy_desc_id], ['class' => 'btn btn-primary']) ?>
    </div>

    <div class="pull-right">
        <?= Html::a("削除", ['delete', 'id' => $model->remedy_desc_id], ['class' => 'btn btn-danger','data-confirm'=>'補足説明を削除します。よろしいですか。']) ?>
    </div>

</div>

