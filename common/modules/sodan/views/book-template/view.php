<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/book-template/view.php $
 *
 * @var $this yii\web\View
 * @var $model common\models\sodan\BookTemplate
 */

$title = sprintf('%06d', $model->template_id);
$this->params['breadcrumbs'][] = ['label' => $title];
$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;

?>
<div class="wait-list-view">

    <?php if($model->template_id): ?>
        <p class="pull-right">
            <?= Html::a('編集', ['update', 'id' => $model->template_id], ['class' => 'btn btn-primary']) ?>
        </p>
    <?php endif ?>

    <h1><?= Html::encode($title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'template_id',
                'format'    => 'html',
                'value'     => $model->template_id
            ],
            [
                'attribute' =>'body',
                'format'    => 'html',
                'value'     => nl2br($model->body)
            ],
        ],
    ]) ?>

    <?= DetailView::widget([
        'options'=>['class'=>'col-md-4 table-condenced text-right pull-right'],
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'create_date',
                'value'     => Yii::$app->formatter->asDate($model->create_date, 'php:Y-m-d H:i ')
                                  . (sprintf('(%s)', $model->creator ? $model->creator->name01 : $model->homoeopath->name)),
            ],
            [
                'attribute' => 'update_date',
                'value'     => Yii::$app->formatter->asDate($model->update_date, 'php:Y-m-d H:i ')
                                  . (sprintf('(%s)', $model->updator ? $model->updator->name01 : $model->homoeopath->name)),
            ],
        ],
    ]) ?>

</div>
