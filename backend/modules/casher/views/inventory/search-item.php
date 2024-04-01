<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/inventory/search-item.php $
 * $Id: search-item.php 3196 2017-02-26 05:11:03Z naito $
 *
 * @var $this yii\web\View
 * @var $model common\models\Inventory
 */

?>

<div class="panel panel-warning">
<div class="panel-heading">
棚卸商品の追加
</div>
<div class="panel-body">

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'id'          => 'form-inventory-item-add',
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


<div id="search-result" class="row col-md-6">
<?= \backend\modules\casher\widgets\inventory\SearchItemResult::widget([
    'keyword'   => $keyword,
    'branch'    => $this->context->module->branch,
    'inventory' => $model,
]) ?>
</div>

<div class="row col-md-6">

    <div class="col-md-10 col-sm-6">
        <?= Html::textInput('keyword', $keyword, [
            'class' => 'form-control',
            'placeholder'=>'バーコード、商品コード、商品名など'
        ]) ?>
    </div>

    <div class="col-md-2 col-sm-6">
        <?= Html::submitButton('検索', ['class' => 'btn btn-default']) ?>
    </div>

    <p class="hint-block col-md-12">
        追加したい商品があればここで検索してください <br>
        なお、追加済み商品は検索結果に現れません<br>
        また、レメディー名で検索できるのは既製品レメディーのみです
    </p>

</div>

<?php $form->end(); ?>

</div>
</div>
