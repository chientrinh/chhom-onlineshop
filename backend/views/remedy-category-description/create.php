<?php
/**
 * $URL: $
 * $Id: $
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RemedyPotency */

$this->title = '新規作成';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remedy-potency-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
