<?php
/**
 * $URL$
 * $Id$
 */
use yii\helpers\Html;
?>
<p>
いま「発送済み」にしたとらのこ会員の住所録を準備中です。
しばらく待ってから「ダウンロード」をクリックしてください
</p>

<p class="info">
<?= Html::a('ダウンロード',['file','basename'=>$basename]) ?>
</p>
