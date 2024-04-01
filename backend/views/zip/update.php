<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/zip/update.php $
 * $Id: update.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Zip */

$this->title = 'Update Zip: ' . ' ' . $model->region;
$this->params['breadcrumbs'][] = ['label' => 'Zips', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->region, 'url' => ['view', 'region' => $model->region, 'zipcode' => $model->zipcode, 'pref_id' => $model->pref_id, 'city' => $model->city, 'town' => $model->town]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="zip-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
