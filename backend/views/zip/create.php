<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/zip/create.php $
 * $Id: create.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Zip */

$this->title = 'Create Zip';
$this->params['breadcrumbs'][] = ['label' => 'Zips', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zip-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
