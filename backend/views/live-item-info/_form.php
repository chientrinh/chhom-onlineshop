<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/live-item-info/_form.php $
 * $Id: _form.php 2286 2016-03-21 06:11:00Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\widgets\ActiveForm
 * @var $model common\models\LiveItemInfo
 */

$liveTickets = \common\models\Product::find()->select(['product_id','name'])->where(['category_id' => \common\models\Category::LIVE])->active()->orderBy('product_id DESC')->asArray()->all();
// var_dump($liveTickets);
$liveTickets = \yii\helpers\ArrayHelper::map($liveTickets, 'product_id', 'name');
// var_dump($liveTickets);
// exit;
$liveInfos = \common\models\LiveInfo::find()->select(['info_id','name'])->asArray()->all();
$liveInfos = \yii\helpers\ArrayHelper::map($liveInfos, 'info_id', 'name');

?>

<div class="live-item-info-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'info_id')->dropDownList($liveInfos) ?>

    <?= $form->field($model, 'product_id')->dropDownList($liveTickets) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

