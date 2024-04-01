<?php

use yii\helpers\Html;
use yii\helpers\Url;
use \backend\models\Staff;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/inventory/_qty.php $
 * $Id: _qty.php 2293 2016-03-24 03:40:01Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Inventory
 */

$submenu_id = 'block-item-'.$model->iitem_id;
$jscode  = "
$('#btn-{$submenu_id}').click(function(){
     {
         $('#{$submenu_id}').show();
         $(this).hide();
     }
 	return false;
});
";
$this->registerJs($jscode);

$nobody = (Staff::PKEY_NOBODY == $model->updated_by);
?>

<div id="btn-<?= $submenu_id ?>" class="text-right">
    <strong class="h3"><?= $model->actual_qty ?></strong>
    <?= Html::a('','',['class'=>'btn glyphicon glyphicon-pencil '.
                               ((Staff::PKEY_NOBODY == $model->updated_by) ? 'btn-warning' : 'btn-default')
    ]) ?>
</div>

<div id="<?= $submenu_id ?>" style="display:none">

    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'id'     => 'form-'.$model->iitem_id,
        'action' => Url::to(['update',
                             'id'    => $model->inventory_id,
                             'page'  => Yii::$app->request->get('page'),
        ]),
        'method' => 'post',
        'fieldConfig' => [
            'template' => "{input}{error}",
            'horizontalCssClasses' => [
                'error'   => 'small',
            ],
        ]
    ]); ?>

    <?= $form->field($model, 'iitem_id')->hiddenInput(['name'=>'iitem_id']) ?>

    <?= Html::submitButton('✔', [
        'class' => 'pull-right btn '. ($nobody ? 'btn-success' : 'btn-default'),
        'title' => '棚卸数を更新する',
    ]) ?>

    <?= $form->field($model, 'actual_qty')->textInput(['name'=>'actual_qty','class'=>'input-lg js-zenkaku-to-hankaku','style'=>'width:50%']) ?>

    <?php $form->end(); ?>

</div>
