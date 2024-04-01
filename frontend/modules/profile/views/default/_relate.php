<?php 
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/default/_relate.php $
 * $Id: _relate.php 1656 2015-10-14 02:27:18Z mori $
 */

use \yii\helpers\Html;

$model = new \common\models\Membercode();

$jscode = "
$('#toggle-btn').click(function(){
     {
         $('#sub-menu').toggle();
     }
 	return true;
});
";
$this->registerJs($jscode);
?>

<p class="text-right">
<?= Html::a('会員証を更新しますか？','#',['id'=>'toggle-btn']) ?>
</p>

<div id="sub-menu" style="display:none">

    <p class="help-block">お手元に別のNOを記載した会員証がある場合、その会員証に更新できます。統合後はお手元の会員証のみ有効となります。</p>
    <div class="well">
    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'action' => ['update'],
        'method' => 'get',
    ]); ?>
    <?= $form->field($model, 'code')->textInput(['name'=>'id']) ?>
    <?= $form->field($model, 'pw')->passwordInput(['name'=>'pw']) ?>
    <?= Html::submitbutton("更新する",['class'=>'btn btn-primary']) ?>
    <?php $form->end(); ?>
    </div>

</div>
