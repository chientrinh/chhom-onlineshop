<?php
/**
 * $URL  $
 * $Id: index.php 2840 2016-08-12 08:47:12Z mori $
 *
 * @var $this yii\web\View
 * @params parametors
 */

use \yii\bootstrap\ActiveForm;
use \yii\helpers\Html;

?>
<h1>バーコードを作る</h1>

<?php $form = ActiveForm::begin([
'action' => ['index'],
'method' => 'get',
]) ?>

<p>
バーコード
<?= Html::input('text', 'code', $code) ?>
</p>

<p>
ラベル（オプション）
<?= Html::input('text', 'label', $label) ?>
</p>

<p>
形式
<?= Html::dropDownList('format', $format, ['png'=>'png','jpg'=>'jpg','gif'=>'gif']) ?>
</p>

<p>
    <?= Html::img(['draw', 'code'=>$code, 'label'=>$label, 'format'=>$format]) ?>
</p>

<?= Html::submitbutton('draw') ?>

<?php $form->end() ?>
