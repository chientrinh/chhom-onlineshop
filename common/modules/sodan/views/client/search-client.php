<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/client/search-client.php $
 * $Id: search-client.php 2518 2016-05-18 04:10:44Z mori $
 *
 * @var $this yii\web\View
 */

?>

<div class="panel panel-warning">
<div class="panel-heading">
豊受モール顧客検索
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

<div class="row col-md-12">
    <div class="col-md-10 col-sm-6">
        <?= Html::textInput('keyword', $keyword, [
            'class' => 'form-control',
            'placeholder'=>'氏名、かな、Kana、TEL、誕生日（YYYY/MM/DD）'
        ]) ?>
    </div>
    <div class="col-md-2 col-sm-6">
        <?= Html::submitButton('検索', ['class' => 'btn btn-default']) ?>
    </div>
    <p class="hint-block col-md-12">
        指定したいクライアントをここで検索してください <br>
    </p>
    <p class="col-md-12">
        <?= Html::a('顧客を新規作成', ['/customer/create?mode=client'], ['class' => 'btn btn-success']) ?>
    </p>
</div>
<div id="search-result" class="row col-md-12">
    <?= \common\modules\sodan\widgets\SearchClient::widget([
        'keyword' => $keyword,
        'param'   => 'client_id',
        'mode'    => 'client'
    ]) ?>
</div>
<?php $form->end(); ?>

</div>
</div>
