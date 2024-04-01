<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ProductJan;
use common\models\RemedyStockJan;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/jancode/_form.php $
 * $Id: _form.php 4231 2020-02-05 05:34:50Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\widgets\ActiveForm
 * @var $model ( ProductJan | RemedyStockJan )
 */

if($model instanceof ProductJan)
    $name = $model->product->name;
elseif($model instanceof RemedyStockJan)
    $name = $model->stock->name;
else
    $name = '<span class="not-set">不正なモデルです</span>';
?>

<div class="jancode-form">

    <?php $form = ActiveForm::begin([
        'method'=>'post',
    ]); ?>

    <h1><?= Html::encode($name) ?></h1>

    <div class="col-md-6">
        <?= $form->field($model, 'jan')->textInput() ?>
    </div>

    <div class="col-md-12 form-group">

        <?php if($model->isNewRecord): ?>
            <?= Html::submitButton("保存", ['class' => 'btn btn-success']) ?>
        <?php else: ?>
            <?= Html::submitButton("更新", ['class' => 'btn btn-primary']) ?>
            <?= Html::a("削除", ['delete','id'=>$model->getOldAttribute('jan')], ['class' => 'btn btn-danger pull-right', 'title'=>'JANコードを削除します', 'data' =>['confirm'=>"このJANコードを削除し、商品「{$name}」との紐付けを解除します。よろしいですか？"]]) ?>
        <?php endif ?>

    <?= $form->errorSummary($model) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
