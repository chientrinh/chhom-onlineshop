<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/inventory/_date.php $
 * $Id: _date.php 2062 2016-02-11 05:51:56Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Inventory
 */

$jscode = "
$('#toggle-btn').click(function(){
     {
         $('#inventory-date-form').show();
         $(this).hide();
     }
 	return false;
});
";
$this->registerJs($jscode);
?>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'id'          => 'update-date',
    'layout'      => 'horizontal',
    'fieldConfig' => [
        'template' => "{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'offset'  => '',
            'label'   => 'col-sm-3',
            'wrapper' => 'col-sm-12',
            'error'   => '',
            'hint'    => 'col-sm-12',
        ]
    ]
]); ?>

<div class="content">
<?= Html::a($model->create_date,'#',['id'=>'toggle-btn']) ?>

<div id="inventory-date-form" class="row" style="display:none">

    <div class="col-md-4 col-sm-6">
        <?= $form->field($model, 'create_date')->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
    </div>
    <div class="col-md-4 col-sm-6">
        <?= Html::submitButton('更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
</div>

<?php $form->end(); ?>
