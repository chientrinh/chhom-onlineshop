<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/materialmaker/create.php $
 * $Id: create.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\MaterialMaker */

$this->title = 'Create Material Maker';
$this->params['breadcrumbs'][] = ['label' => 'Material Makers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-maker-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
