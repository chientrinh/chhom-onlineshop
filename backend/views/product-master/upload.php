<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-master/upload.php $
 * $Id: upload.php 2694 2016-07-10 06:22:45Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\FileForm
 */

$this->params['breadcrumbs'][] = ['label'=>'一括編集'];

?>
<div id="product-master-upload">

    <div class="col-md-12">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'id'     => 'form-agreement',
        'layout' => 'default',
        'method' => 'post',
        'options'=> ['enctype' => 'multipart/form-data'],
    ]); ?>

    <?= $form->field($model, 'tgtFile')->label(false)->fileInput() ?>

    <?= Html::submitButton('送信',['class'=>'btn btn-success']) ?>

    </div>

    &nbsp;

    <div class="col-md-12">
        <div class="alert alert-warning">
            <ul class="help-block">
                <li>文字コードはUTF8に対応しています</li>
                <li>列 ean13 は必須です</li>
                <li>列 name, dsp_priority のみ上書きします</li>
            </ul>
        </div>
    </div>

</div>
