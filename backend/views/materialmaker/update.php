<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/materialmaker/update.php $
 * $Id: update.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MaterialMaker */

$this->title = 'Update Material Maker: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Material Makers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->maker_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="material-maker-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
