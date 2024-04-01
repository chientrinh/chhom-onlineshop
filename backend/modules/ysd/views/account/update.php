<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/views/account/update.php $
 * $Id: update.php 3103 2016-11-24 05:38:13Z mori $
 */

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

$this->params['breadcrumbs'][] = ['label' => ($c = $model->customer) ? $c->name : null, 'url'=>['view','id'=>$model->customer_id]];
$this->params['breadcrumbs'][] = ['label' => '編集'];

$values = \common\models\ysd\AccountStatus::find()->all();
$values = ArrayHelper::map($values, 'expire_id', 'name');
?>

<h1><?= ArrayHelper::getValue($model, 'customer.name') ?></h1>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'fieldConfig'=>['options' => ['class'=>'col-md-4']],
]) ?>

<?= $form->field($model, 'credit_limit')?>

<?= $form->field($model, 'expire_id')->dropDownList($values) ?>

<div class="col-md-12">
<?= Html::submitbutton('更新',['class'=>'btn btn-warning']) ?>
</div>

<?php $form->end() ?>
