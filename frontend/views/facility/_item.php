<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/facility/_item.php $
 * $Id: _item.php 3161 2016-12-18 05:32:34Z mori $
 *
 * $model:  mixed, the data model
 * $this:   View
 */

use \yii\helpers\Html;
use \yii\bootstrap\Modal;

if($model->private) // 公開しない
    return;

if(! $model->getMemberships()->active()->exists()) // 提携が終了した
    return;

$footer = [];

if($model->url)
    $footer[] = Html::a($model->url, $model->url, ['target'=>'_blank']);

if($model->email)
    $footer[] = Html::a($model->email, "mailto:{$model->email}");

if($model->tel)
    $footer[] = Html::tag('span', 'TEL ' . $model->tel);

if($model->fax)
    $footer[] = Html::tag('span', 'FAX ' . $model->fax);

?>

<?php Modal::begin([
    'id'     => $model->customer_id,
    'header' => Html::tag('h3', $model->name,['style'=>'color:#003F74']),
    'footer' => Html::tag('p', $model->addr) . implode('&nbsp;/&nbsp;', $footer),
    'toggleButton' => ['label' => $model->name,'class'=>'btn btn-default'],
    'options' => ['style'=>'position:relative'],
]) ?>

<div class="alert pull-right text-right">
<?= \yii\widgets\ListView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query'      => $model->getMemberships()->active(),
        'pagination' => false,
    ]),
    'layout'   => '{items}',
    'itemView' => function($data){ return $data->name; },
    'itemOptions' => ['tag'=>'strong','style'=>'color:#777'],
    'separator' => '<br>',
]) ?>
</div>

<div class="alert">
<?= nl2br(Html::encode($model->summary)) ?>
</div>

<div>
    <strong>
        <?= Html::encode($model->title) ?> 
    </strong>
        <?= ($c = $model->customer) ? $c->name : null ?>
</div>

<?php Modal::end(); ?>
