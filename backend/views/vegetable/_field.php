<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/vegetable/_field.php $
 * $Id: _field.php 3516 2017-07-27 00:50:18Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\Vegetable
 */

?>

<div class="col-md-12 well">

        <?php $form = \yii\bootstrap\ActiveForm::begin([
            'action' => ['print', 'id' => $model->veg_id],
            'method' => 'get',
            'options'=>['target'=>'_blank']
        ]) ?>

        <div class="form-group col-md-1 col-sm-4">
            <?= Html::submitbutton('印刷',['class'=>'btn btn-success','formtarget' => '_blank']) ?>
        </div>

        <div class="form-group col-md-3 col-sm-4">
            <div class="input-group">
                <?= Html::textInput('price', null, ['class'=>"form-control js-zenkaku-to-hankaku"])?>
                <span class="input-group-addon">円</span>
            </div>
        </div>

        <div class="form-group col-md-2 col-sm-4">
            <div class="input-group">
                <?= Html::textInput('qty', null, ['class'=>"form-control js-zenkaku-to-hankaku", 'placeholder'=>"1"])?>
                <span class="input-group-addon">枚</span>
            </div>
        </div>

        <?php $form->end() ?>

        <p class="help-block pull-right">
            値札を印刷できます
        </p>

</div>
