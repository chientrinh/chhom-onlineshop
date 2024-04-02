<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/interview/search-client.php $
 * $Id: search-client.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this yii\web\View
 */

?>

<div class="panel panel-warning">
<div class="panel-heading">
クライアントの検索
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
<?= \common\modules\sodan\widgets\SearchClient::widget([
    'keyword' => $keyword,
    'param'   => 'client_id',
]) ?>
</div>

<div class="row col-md-6">

    <div class="col-md-10 col-sm-6">
        <?= Html::textInput('keyword', $keyword, [
            'class' => 'form-control',
            'placeholder'=>'氏名、かな、Kana、TEL'
        ]) ?>
    </div>

    <div class="col-md-2 col-sm-6">
        <?= Html::submitButton('検索', ['class' => 'btn btn-default']) ?>
    </div>

    <p class="hint-block col-md-12">
        指定したいクライアントをここで検索してください <br>
    </p>

</div>

<?php $form->end(); ?>

</div>
</div>
