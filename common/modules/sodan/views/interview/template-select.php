<?php

use \yii\helpers\Html;
use common\models\sodan\BookTemplate;
use \yii\helpers\ArrayHelper;
use \yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/interview/template-select.php $
 * $Id: index.php 2676 2016-07-08 05:40:40Z mori $
 *
 * $this \yii\web\View
 */
$query = BookTemplate::find();
$templates = ArrayHelper::map($query->all(), 'template_id', 'body');

$linkUrl = Url::to(['print', 'id' => $id, 'page' => $page, 'format' => $format]);
$jscode = "
$(function(){
  $('#template-submit').click(function(){
    var url = '{$linkUrl}' + '&template_id=' + $('input[name=template_id]:checked').val();
    window.open(url, '_blank');
  });
});
";
$this->registerJs($jscode);

$title = sprintf('テンプレート選択');
$this->params['breadcrumbs'][] = ['label' => $title];
$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;
?>

<div class="wait-list-form">

    <p><b>予約票に記載するテンプレートを選択して「印刷」をクリックしてください。</b></p>

    <?php $form = \yii\bootstrap\ActiveForm::begin(); ?>

    <?= \yii\grid\GridView::widget([
    'tableOptions' => ['class'=>'table table-condensed table-striped'],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'showOnEmpty'  => true,
        'columns' => [
            [
                'attribute' => 'body',
                'format'    => 'raw',
                'value'     => function($data){
                    $options = [
                        'label'  => nl2br($data->body),
                        'value'  => $data->template_id,
                        'uncheck'=> null,
                        'checked'=> null,
                    ];
                    return Html::radio('template_id', $data->template_id === 1, $options);
                },
                'filter' => false
            ],
            [
                'attribute' => 'update_date',
                'filter' => false
            ]
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('印刷', ['class' => 'btn btn-primary', 'id' => 'template-submit']) ?>
    </div>

    <?php $form->end(); ?>
</div>

