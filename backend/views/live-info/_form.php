<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/live-info/_form.php $
 * $Id: _form.php 2286 2016-03-21 06:11:00Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\widgets\ActiveForm
 * @var $model common\models\Streaming
 */

$liveTickets = \common\models\Product::find()->where(['category_id' => \common\models\Category::LIVE])->all();
$liveTickets = \yii\helpers\ArrayHelper::map($liveTickets, 'product_id', 'name');
$liveOption = \common\models\Product::find()->where(['category_id' => \common\models\Category::LIVE_OPTION])->all();
$liveOption = \yii\helpers\ArrayHelper::map($liveOption, 'product_id', function($data){return $data->name.' 税込'.Yii::$app->formatter->asCurrency($data->price+$data->tax);});
$liveOption = ['0' => 'オプション用商品を選択'] + $liveOption;
$onlineEnable = [0 => '不可', 1 => '可'];
$campaign_type = [0 => 'なし', 1 => '通常', 2 => 'コングレス', 3 => 'シンポジウム'];
$support_entry = [0 => '使用しない', 1 => '使用する'];

?>

<div class="live-info-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'place')->textInput() ?>

    <?= $form->field($model, 'option_name')->textInput() ?>

    <?= $form->field($model, 'option_description')->textInput() ?>

    <?= $form->field($model, 'online_option_enable')->dropDownList($onlineEnable) ?>

    <?= $form->field($model, 'product_id')->dropDownList($liveOption)->label('オプション用商品') ?>


    <?= $form->field($model, 'coupon_name')->textInput() ?>

    <?= $form->field($model, 'coupon_code')->textInput() ?>

    <?= $form->field($model, 'coupon_discount')->textInput() ?>

    <?= $form->field($model, 'online_coupon_enable')->dropDownList($onlineEnable) ?>    

    <?= $form->field($model, 'companion')->textInput(['placeholder' => "例: 大人,小人,未就学児"])->label('同行者 カンマ区切りで入力') ?>

    <?= $form->field($model, 'adult_price1')->textInput() ?>

    <?= $form->field($model, 'adult_price2')->textInput() ?>

    <?= $form->field($model, 'adult_price3')->textInput() ?>

    <?= $form->field($model, 'child_price1')->textInput() ?>

    <?= $form->field($model, 'child_price2')->textInput() ?>

    <?= $form->field($model, 'child_price3')->textInput() ?>

    <?= $form->field($model, 'infant_price1')->textInput() ?>

    <?= $form->field($model, 'infant_price2')->textInput() ?>

    <?= $form->field($model, 'infant_price3')->textInput() ?>

    <?= $form->field($model, 'capacity')->textInput() ?>

    <?= $form->field($model, 'subscription')->textInput() ?>

    <?= $form->field($model, 'campaign_code')->textInput() ?>

    <?= $form->field($model, 'campaign_type')->dropDownList($campaign_type) ?>

    <?= $form->field($model, 'campaign_period')->textInput() ?>

    <?= $form->field($model, 'pre_order_code')->textInput() ?>

    <?= $form->field($model, 'pre_order_period')->textInput() ?>

    <?= $form->field($model, 'support_entry')->dropDownList($support_entry) ?>    

    <?= $form->field($model, 'expire_date')->textInput([
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $model,
                    'attribute'=>'expire_date',
                    'language' => 'ja',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class'=>'form-control col-md-12'],
                    'clientOptions' => [
                        'country'     => 'JP',
                        'yearRange'   => 'c-1:c+1',
                        'changeYear'  => true,
                        'changeMonth' => true,
                    ],
                ])]
    ) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

