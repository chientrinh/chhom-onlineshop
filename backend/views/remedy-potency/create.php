<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-potency/create.php $
 * $Id: create.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RemedyPotency */

$this->title = 'Create Remedy Potency';
$this->params['breadcrumbs'][] = ['label' => 'Remedy Potencies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remedy-potency-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
