<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/karute/_form.php $
 * $Id: _form.php 1637 2015-10-11 11:12:30Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\webdb20\Karute
 */

$homoeopaths = \common\models\webdb20\KaruteHomoeopath::find()->all();
$homoeopaths = \yii\helpers\ArrayHelper::map($homoeopaths, 'syoho_homeopathid','syoho_homeopath');
?>

<div class="karute-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'customerid')->textInput() ?>

    <?= $form->field($model, 'karute_date')->textInput([
        'filter' => \yii\jui\DatePicker::widget([
            'model' => $model,
            'attribute'=>'karute_date',
            'language' => 'ja',
            'dateFormat' => 'yyyy/MM/dd',
        ]),
    ]) ?>

    <?= $form->field($model, 'karute_syuso')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'syoho_homeopathid')->dropDownList($homoeopaths) ?>

    <?= $form->field($model, 'karute_fax_data')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '保存' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

        <?php if(! $model->isNewRecord): ?>
            <?= Html::a('戻る', ['view','id'=>$model->karuteid], ['class'=>'btn btn-default']) ?>
        <?php endif ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
