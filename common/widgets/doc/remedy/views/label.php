<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/remedy/views/label.php $
 * $Id: label.php 2621 2016-06-24 09:57:25Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Purchase
 * @var $title string
 */

?>
<Table style="page-break-after:always"></table>
<span style="line-height:7.0pt;font-size:7.0pt">
<?= $title ?>
<br>
<?= $vial_name ?>
<br>
</span>
<?php foreach($model->drops as $drop): ?>
<span style="line-height:7.0pt;font-size:7.0pt">
<?php if(! $drop->remedy){ var_dump($drop->attributes); } ?>
+<?= $drop->remedy ? $drop->remedy->name : '<span style="color:red">error</span>' ?> <?= $drop->potency ? preg_replace('/combination/','',$drop->potency->name) : '<span style="color:red">error</span>' ?>
</span><br>
<?php endforeach ?>

