<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/backend/views/pointing/search-client.php $
 * $Id: search-client.php 2518 2016-05-18 04:10:44Z mori $
 *
 * @var $this yii\web\View
 */

?>

<div class="panel panel-warning">
<div class="panel-heading">顧客の検索</div>
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

<div class="row col-md-12">
    <div class="col-md-10 col-sm-6">
        <?= Html::textInput('keyword', $keyword, [
            'class' => 'form-control',
            'placeholder' => '氏名、かな、Kana、TEL、誕生日（YYYY/MM/DD）',
            'tabindex' => 2
        ]) ?>
    </div>
    <div class="col-md-2 col-sm-6">
        <?= Html::submitButton('検索', ['class' => 'btn btn-default', 'tabindex' => 3]) ?>
    </div>
    <p class="hint-block col-md-12">
        指定したい顧客をここで検索してください <br>
    </p>
</div>
<?php $form->end(); ?>

    <div id="search-result" class="row col-md-12">
    <?= \common\modules\sodan\widgets\SearchClient::widget([
        'keyword' => $keyword,
        'param'   => 'customer_id',
    ]) ?>
    </div>

</div>
</div>
