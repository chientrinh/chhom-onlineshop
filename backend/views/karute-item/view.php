<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/karute-item/view.php $
 * $Id: view.php 1637 2015-10-11 11:12:30Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\webdb20\Karute
 */

$this->title = sprintf('ID %s | %s | %s', $model->karuteid, '子カルテ', Yii::$app->name);

$histories = [
    1 => "初診",
    2 => "再診",
];
?>
<div class="karute-view">

    <div class="pull-right">

        <?php if($model->prev): ?>
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-left']), ['view','id'=>$model->prev->karuteid], ['class'=>'btn btn-xs btn-default']) ?>
        <?php endif ?>
        <?php if($model->next): ?>
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-right']), ['view','id'=>$model->next->karuteid], ['class'=>'btn btn-xs btn-default','title'=>'次']) ?>
        <?php endif ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           [
               'attribute' => 'karuteid',
               'format'    => 'raw',
               'value'     => Html::a($model->karuteid, ['/karute/view','id'=>$model->karuteid]),
           ],
           'syohoid',
           [
           'attribute' => 'customerid',
           'format'    => 'raw',
           'value'     => ! $model->customer ? null : $model->customer->name . '&nbsp;' . Html::a('webdb20',sprintf('https://webdb20.homoeopathy.co.jp/index.php?m=search&out_html=customer_dsp&customerid=%d',$model->customerid),['target'=>'webdb20','class'=>'glyphicon glyphicon-log-out']),
           ],
           'syoho_date:ntext',
           [
           'attribute' => 'syoho_homeopathid',
           'value'     => ! $model->homoeopath ? null : $model->homoeopath->name,
           ],

           'syoho_coment:ntext',
           'syoho_advice:ntext',
           'user_report:ntext',
           [
               'attribute' => 'sodan_kindid',
               'value'     => ! $model->consultationType ? null : $model->consultationType->name,
           ],
           [
               'attribute' => 'syoho_historyid',
               'value'     => \yii\helpers\ArrayHelper::getValue($histories, $model->syoho_historyid, null),
           ],
           [
               'attribute' => 'denpyo_centerid',
               'value'     => ! $model->branch ? null : $model->branch->name,
           ],
           'syoho_rec_f',
           'syoho_proc_end_f',
           'syoho_std_name',
           'std_proc_end_f',
           'std_name',
           'std_proc_date',
           'syoho_proc_date',
           'syoho_sure_f',
           'syoho_sure_date',
        ],
    ]) ?>

    <p class="pull-right">
        <?= Html::a('修正', ['update', 'id' => $model->karuteid], ['class' => 'btn btn-primary']) ?>
    </p>

</div>
