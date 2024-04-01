<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/facility/update.php $
 * $Id: update.php 3987 2018-08-17 02:30:40Z mori $
 *
 * $model: common\models\Facility
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Pref;

$this->params['breadcrumbs'][] = ['label'=> '編集'];

$prefs = ArrayHelper::map(Pref::find()->asArray()->select(['pref_id','name'])->all(),'pref_id','name');
array_unshift($prefs, ' ');

?>

<div class="facility-admin-update">

<h1>
    <?= ArrayHelper::getValue($model,'name') ?>
</h1>

<?php $form = \yii\bootstrap\ActiveForm::begin(); ?>

<?= $form->errorSummary($model) ?>

<?= $form->field($model, 'name') ?>

<?= $form->field($model, 'email') ?>
<?= $form->field($model, 'url') ?>
<?= $form->field($model, 'title') ?>

<?= $form->field($model, 'summary')->textArea(['rows'=>10]) ?>

<div class="form-group col-md-12 well">
    <p><strong><?= $model->getAttributeLabel('addr')?></strong></p>
    <p class="help-block">
        公開したくない部分は空白にしてください
    </p>
    <div class="col-md-3">
        <?= $form->field($model, 'zip01') ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'zip02') ?>
    </div>
    <div class="col-md-3 text-center">
        <p><strong>&nbsp;</strong></p>
        <?= Html::submitButton('住所を検索',['name'=>'scenario','value'=>'zip2addr','class'=>'btn btn-primary']) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'pref_id')->dropDownList($prefs) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'addr01') ?>
        <?= $form->field($model, 'addr02') ?>
    </div>
</div>

<div class="form-group col-md-12 well">
    <p class="help-block">
        公開したくない場合は空白にしてください
    </p>
    <div class="col-md-12">
    <label class="control-label">
        <?= $model->getAttributeLabel('tel') ?>
    </label>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'tel01')->label(null,['class'=>'small text-muted']) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'tel02')->label(null,['class'=>'small text-muted']) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'tel03')->label(null,['class'=>'small text-muted']) ?>
    </div>

    <div class="col-md-12">
    <label class="control-label">
        <?= $model->getAttributeLabel('fax') ?>
    </label>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'fax01')->label(null,['class'=>'small text-muted']) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'fax02')->label(null,['class'=>'small text-muted']) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'fax03')->label(null,['class'=>'small text-muted']) ?>
    </div>

</div>

<div class="col-md-6">
    <?= $form->field($model, 'pub_date')->textInput([
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $model,
                    'attribute'=>'pub_date',
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

</div>
<div class="col-md-6">
    <?= $form->field($model, 'private')->checkBox() ?>
</div>

<div class="col-md-12">
    <div class="form-group">
    <p class="pull-left">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </p>
    <p class="text-right">
        <?php if(! $model->isNewRecord): ?>
        <?= Html::a('削除', ['delete','id'=>$model->facility_id], [
            'class' => 'btn btn-danger',
            'data'  => ['confirm' => "本当に削除していいですか"],
        ]) ?>
        <?php endif ?>
    </p>
    </div>
</div>

<?php $form->end(); ?>

</div>
