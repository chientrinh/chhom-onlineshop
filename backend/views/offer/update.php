<?php

use yii\helpers\Html;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/views/offer/update.php $
 * @version $Id: update.php 2891 2016-09-29 01:23:00Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Offer
 *
 */

$title = sprintf('%s:%s', $model->category->name, $model->grade->name);
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['view','category_id' => $model->category_id, 'grade_id' => $model->grade_id]];
$this->params['breadcrumbs'][] = ['label' => '修正'];
?>
<div class="offer-update">

    <h1><?= $title ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
