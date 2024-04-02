<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Membership;
use common\models\Branch;
use common\models\CustomerMembership;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/homoeopath/_form.php $
 * $Id: _form.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this  yii\web\View
 * @var $model common\models\sodan\Homoeopath
 * @var $form  yii\widgets\ActiveForm
 */
$query = Branch::find()->center();
$branch = ArrayHelper::merge(['' => ''], ArrayHelper::map($query->all(), 'branch_id', 'name'));

$query = CustomerMembership::find()
       ->active()
       ->with('customer')
       ->where(['membership_id' => Membership::PKEY_CENTER_HOMOEOPATH])
       ->andWhere('expire_date >= now()');
$hpath = ArrayHelper::merge(['' => ''], ArrayHelper::map($query->all(),'customer_id','customer.homoeopathname'));

ksort($hpath);

$del_flg = common\models\sodan\Homoeopath::getDelFlg();
?>

<div class="homoeopath-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'homoeopath_id')->dropDownList($hpath) ?>

    <?= $form->field($model, 'branch_id')->dropDownList($branch) ?>

    <?= $form->field($model, 'branch_id2')->dropDownList($branch) ?>

    <?= $form->field($model, 'branch_id3')->dropDownList($branch) ?>

    <?= $form->field($model, 'branch_id4')->dropDownList($branch) ?>

    <?= $form->field($model, 'branch_id5')->dropDownList($branch) ?>

    <?= $form->field($model, 'schedule')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'del_flg')->dropDownList($del_flg) ?>

    <div class="form-group pull-left">
        <?= Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

    <p class="pull-right">
        <?= Html::a('戻る', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

</div>
