<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/agency-rating/create.php $
 * $Id: create.php 1800 2015-11-13 15:10:43Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\AgencyRating
 */

?>
<div class="agency-rating-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
