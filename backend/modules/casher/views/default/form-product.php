<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/form-product.php $
 * $Id: $
 *
 * $model \common\models\ProductMaster
 */
use \yii\helpers\Html;
use \yii\helpers\Url;

$quantity = range(0,99);
\yii\helpers\ArrayHelper::remove($quantity, 0);
?>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'action' => 'apply',
    'method' => 'get',
    'layout' => 'inline',
    'class'  => 'product-add-form',
]); ?>

<?php if ($model->hasAttribute('recipe_id') && $model->recipe_id && $target == 'recipe'): ?>
	<?= Html::hiddenInput('id', $model->recipe_id) ?>
<?php elseif ($model->hasAttribute('product_id') && $model->product_id): ?>
	<?= Html::dropDownList('qty', 1, $quantity, $options = ['maxlangth'=>3,'class'=>'pull-left form-control','style'=>'width:inherit'])?>
	&nbsp;
	<?= Html::hiddenInput('id', $model->product_id) ?>
<?php endif; ?>

<?= Html::hiddenInput('target', $target) ?>
<?= Html::submitButton('追加',['class'=>'btn btn-sm btn-warning']) ?>
<?php $form->end() ?>
