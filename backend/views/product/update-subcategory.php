<?php

use yii\helpers\Html;
use \common\models\Subcategory;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product/_subcategory.php $
 * $Id: _subcategory.php 2017 2016-01-28 03:53:34Z mori $
 *
 * @var $this    yii\web\View
 * @var $model   Subcategory
 * @var $actives array of subcategory_id
 */

$active = in_array($model->subcategory_id, $actives);

?>

<div id="subcategory">
<?= Html::checkbox('subcategory_id', $active, [
    'value' => $model->subcategory_id,
    'label' => $model->fullname
]) ?>
</div>

<?php foreach($model->children as $child): ?>

<?= $this->render('update-subcategory',['model'=>$child,'actives'=>$actives]) ?>

<?php endforeach ?>
