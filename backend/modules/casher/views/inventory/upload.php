<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/inventory/upload.php $
 * $Id: upload.php 2503 2016-05-12 09:38:58Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\FileForm
 */

$this->params['breadcrumbs'][] = ['label'=>'一括編集'];

?>
<div class="casher-inventory-upload">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'id'     => 'form-agreement',
        'layout' => 'default',
        'method' => 'post',
        'options'=> ['enctype' => 'multipart/form-data'],
    ]); ?>

    <?= $form->field($model, 'tgtFile')->label(false)->fileInput() ?>

    <?= Html::submitButton('送信',['class'=>'btn btn-success']) ?>
    </p>

</div>
