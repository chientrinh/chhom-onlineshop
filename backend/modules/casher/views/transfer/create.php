<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/transfer/create.php $
 * $Id: create.php 3124 2016-12-01 05:51:48Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\TransferItem
 */

$this->params['breadcrumbs'][] = ['label'=>'新規作成'];

$jscode ="
$('input').change(function(){
    $(this).submit();
    return false;
});
";
$this->registerJs($jscode);

$branches = ArrayHelper::map(\common\models\Branch::find()->all(),'branch_id','name');

$dst = $branches;
$src = $branches;

$src[0] = 'グループ社外';
$dst[0] = '廃棄';

?>

<div class="transfer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div id="transfer-create-form">

        <?= Html::a('商品を追加',['search','target'=>'product'],['class'=>'btn btn-success']) ?>

        <?= $this->render('/default/__items',['model'=>$model,'tabindex'=>1])?>
        <?= Html::a('<i class="close glyphicon glyphicon-remove"></i>',['apply','target'=>'reset'],['style'=>'color:#999','title'=>'すべて初期化します']) ?>

        <?php $form = \yii\bootstrap\ActiveForm::begin([
            'action' => ['finish'],
        ]); ?>

        <?= $form->field($model, 'src_id')->dropDownList($src) ?>

        <?= $form->field($model, 'dst_id')->dropDownList($dst) ?>

        <?= $form->field($model, 'note')->textArea(['maxlength' => 255]) ?>

        <div class="form-group">
            <?= Html::submitButton('一時保存', ['onClick'=>'this.form.action="create"', 'class' => 'btn btn-primary']) ?>
            <?= Html::a('値札',['create','print'=>true],['class'=>'btn btn-default', 'title' => 'いま表示中の商品で値札を印刷します']) ?>
            <?= Html::submitButton('発注する', ['class' => 'btn btn-danger pull-right']) ?>
        </div>

        <?php $form->end(); ?>

    </div>

    <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>

</div>
