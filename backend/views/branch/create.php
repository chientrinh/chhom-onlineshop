<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/branch/create.php $
 * $Id: create.php 1113 2015-06-28 06:49:42Z mori $
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Branch */

$this->title = 'Create Branch';
$this->params['breadcrumbs'][] = ['label' => '拠点', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="branch-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
