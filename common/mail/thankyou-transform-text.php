<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/mail/thankyou-transform-text.php $
 * $Id: thankyou-transform-text.php 2388 2016-04-07 07:18:12Z mori $
 *
 *
 * @var $this yii\web\View
 * @var $model \common\models\Transfer
 */

use \yii\helpers\Url;

?>
店舗間移動が発注されました。

移動番号 : <?= sprintf('%06d', $model->purchase_id) ?> 
発注日　 : <?= $model->create_date ?> 
発送拠点 : <?= ($s = $model->src) ? $s->name : 'なし' ?> 
受取拠点 : <?= ($d = $model->dst) ? $d->name : 'なし' ?> 
発注者　 : <?= ($c = $model->creator) ? $c->name01 : 'なし' ?> 
備考　　 : <?= ($n = $model->note) ? $n : 'なし' ?> 

詳しくはこちらでご確認ください
<?= Url::toRoute(['/casher/transfer/view','id'=>$model->purchase_id], true)?> 

明細 : <?= $model->getItems()->count()?> 品目、総計 <?= $model->itemCount ?> 点
------------------------------------------------------------
<?php foreach($model->items as $item): ?>
<?= sprintf('%3d', $item->quantity) ?> x <?= $item->name ?> 
<?php endforeach ?>
------------------------------------------------------------

<?= Yii::$app->name ?> 
------------------------------------------------------------
店間移動が確定した時点で送信されるこのメールに心当たりのない場合は <?= Yii::$app->params['adminEmail'] ?> までご連絡いただけますようお願い申し上げます。
------------------------------------------------------------
