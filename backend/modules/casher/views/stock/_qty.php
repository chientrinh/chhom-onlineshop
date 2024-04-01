<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use \backend\models\Staff;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/inventory/_qty.php $
 * $Id: _qty.php 2293 2016-03-24 03:40:01Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Inventory
 */

$qtyOption = [];
$i = $model->maximum_qty;

while ( $i >= 0 ) {
    $qtyOption [$i] = $i;
    $i --;
}

$submenu_id = 'block-item-' . $model->stock_id;
$jscode = "
$('#stock-actual_qty').on('change', function(){
     {
         $(this).parent().siblings('.btn').addClass('btn-success');
     }
     return false;
});
";
$this->registerJs ( $jscode );

$nobody = (Staff::PKEY_NOBODY == $model->updated_by);

$change?>

<div id="btn-<?= $model->stock_id ?>" class="text-right">

        <?php
            $form = \yii\bootstrap\ActiveForm::begin([
                'id'     => 'form-'.$model->stock_id,
                'action' => Url::to(['update',
                                     'id'    => $model->stock_id,
                                     'page'  => Yii::$app->request->get('page'),
                ]),
                'method' => 'post',
                'fieldConfig' => [
                    'template' => "{input}{error}",
                    'horizontalCssClasses' => [
                        'error'   => 'small',
                    ],
                ]
            ]);
            echo Html::submitButton('更新', [
                'class' => 'pull-right btn btn-default', //($nobody ? 'btn-success' : 'btn-default'),
                'title' => '在庫数を更新する',
            ]);
            echo $form->field($model, 'actual_qty')
                    ->dropdownList($qtyOption,
                                    $model->isNewRecord ? ['prompt'=>'数量を選択してください', 'name' => 'actual_qty', 'id' => 'stock-actual_qty' ]
                                                        : ['name' => 'actual_qty', 'style'=>'width:65px', 'id' => 'stock-actual_qty']);

            echo $form->field($model, 'product_id')->hiddenInput(['name'=>'product_id']);
            echo $form->field($model, 'branch_id')->hiddenInput(['name'=>'branch_id']);
            echo $form->field($model, 'stock_id')->hiddenInput(['name'=>'stock_id']);
            echo $form->field($model, 'version')->hiddenInput(['name'=>'version']);$form->end ();?>


</div>
