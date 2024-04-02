<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/create/machine.php $
 * $Id: machine.php 3916 2018-06-01 07:13:51Z mori $
 *
 * $carts array of Cart
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

// View config
$this->params['body_id'] = 'Mypage';

$title = '特別レメディーを追加';
$this->title = sprintf("%s | 新規作成 | 適用書 | %s", $title, Yii::$app->name);
// Potency 3 - 24 に限定（コンビネーション、Φ、LM系を除外）
$potencies = \common\models\RemedyPotency::find()->where(['between','potency_id', 3, 24])->orderBy(['weight'=>SORT_DESC])->all();
$potencies = \yii\helpers\ArrayHelper::map($potencies, 'potency_id', 'name');
$potencies[''] = null;
ksort($potencies);

$jscode = '
$("button[id=btn-plus]").click(function() {
    $(this).hide();
    $("#secondary-remedy").show();
});
$("button[id=btn-minus]").click(function() {
    $("#secondary-remedy").hide();
    $("#btn-plus").show();
});
';
$this->registerJs($jscode);
?>

<div class="cart-view">

    <?= $this->render('_tab', ['model' => $recipe]) ?>

    <div class="col-md-9">
    <h2><span><?= $title ?></span></h2>


    <?php $form = ActiveForm::begin([
            'id' => 'recipe-create-machine',
            'validateOnBlur'  => true,
            'validateOnChange'=> true,
            'validateOnSubmit'=> true,
            'fieldConfig' => [
                'template' => "{input}\n{error}",
                'horizontalCssClasses' => [
                    'offset' => 'col-sm-offset-4',
                    'error' => '',
                    'hint' => '',
                ],
            ],
        ])?>

        <div class="col-md-12">

        <h3></h3>
        <div id="primary-remedy" class="row">

        <div class="col-md-8">
        <?= $model->getAttributeLabel('abbr') ?>
        <?= $form->field($model, 'abbr1') ?>
        </div>

        <div class="col-md-3">
        <?= $model->getAttributeLabel('potency') ?>
        <?= $form->field($model, 'potency1')->dropDownList($potencies) ?>
        </div>

        <div class="col-md-1">
            &nbsp;
            <?= Html::button('＋', ['id'=>'btn-plus', 'class' => 'btn btn-success']) ?>
        </div>

        </div>

        <div id="secondary-remedy" class="row" style="<?= (! $model->abbr2 && ! $model->potency2) ? 'display:none' : null ?>">

        <div class="col-md-8">
        <?= $model->getAttributeLabel('abbr') ?>
        <?= $form->field($model, 'abbr2') ?>
        </div>

        <div class="col-md-3">
        <?= $model->getAttributeLabel('potency') ?>
        <?= $form->field($model, 'potency2')->dropDownList($potencies) ?>
        </div>

        <div class="col-md-1">
            &nbsp;
            <?= Html::button('ー', ['id'=>'btn-minus', 'class' => 'btn btn-success']) ?>
        </div>

        </div>

        <div class="form-group">
            <?= Html::submitButton('カートに追加', ['class' => 'btn btn-warning']) ?>
        </div>

            <?php $form->end() ?>

    </div>

    </div>

    <div class="col-md-3">
        <?= $this->render('recipe-item-grid',['model'=>$recipe]) ?>
    </div>

</div>
