<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use \common\models\ProductSubcategory;
use \common\models\Subcategory;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/inventory/filter-item.php $
 * $Id: filter-item.php 2293 2016-03-24 03:40:01Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Inventory
 */

$q1 = ProductSubcategory::find()->where(['ean13'=>$model->getItems()->select('ean13')->column()]);
$q2 = Subcategory::find()->where(['subcategory_id'=>$q1->column()]);
$subcategories    = ArrayHelper::map($q2->all(), 'subcategory_id','fullname');
$subcategories[0] = '';
$subcategory_id   = Yii::$app->request->get('subcategory_id', 0);
$kana             = Yii::$app->request->get('kana');

?>

<div class="panel panel-info">
<div class="panel-heading">
絞り込み
</div>
<div class="panel-body">

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'id'          => 'form-inventory-item-add',
    'method'      => 'get',
    'layout'      => 'horizontal',
    'fieldConfig' => [
        'template' => "{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'offset'  => '',
            'label'   => 'col-sm-3',
            'wrapper' => 'col-sm-12',
            'error'   => '',
            'hint'    => 'col-sm-3',
        ]
    ]
]); ?>

<?= Html::hiddenInput('id', $model->inventory_id) ?>

<div class="row col-md-6">
    <?= Html::textInput('kana', $kana, [
        'class'      => 'form-control',
        'placeholder'=> '商品名'
    ]) ?>
</div>

<div class="row col-md-6">

    <div class="col-md-10 col-sm-6">
        <?= Html::dropDownList('subcategory_id',$subcategory_id,$subcategories,['class'=>'form-control']) ?>
    </div>

    <div class="col-md-2 col-sm-6">
        <?= Html::submitButton('絞込', ['class' => 'btn btn-default']) ?>
    </div>

</div>

<?php $form->end(); ?>

</div>
</div>
