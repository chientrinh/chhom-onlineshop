<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/hj/form-remedy.php $
 * $Id: form-remedy.php 3884 2018-05-22 08:20:20Z mori $
 *
 * $model \common\models\ProductMaster
 */
use \yii\helpers\Html;
use \yii\helpers\Url;

?>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'id'     => $model->ean13,
    'action' => Url::toRoute(['/cart/remedy/add']),
    'method' => 'get',
    'layout' => 'inline',
    'class'  => 'product-add-form',
]); ?>

    <?= Html::textInput('qty', 1, ['size'=>3,'maxlangth'=>3,'class'=>'pull-left form-control','style'=>'width:inherit']) ?>
    &nbsp;
    <?= Html::hiddenInput('rid', $model->remedy_id ) ?>
    <?= Html::hiddenInput('pid', $model->potency_id) ?>
    <?= Html::hiddenInput('vid', $model->vial_id   ) ?>

    <?php if ($model->in_stock): ?>
        <?= Html::submitButton('追加',['class'=>'btn btn-sm btn-warning']) ?>
    <?php else: ?>
        <span style="color:red;">欠品中</span>
    <?php endif; ?>
<?php $form->end() ?>

