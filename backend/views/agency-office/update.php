<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/agency-office/update.php $
 * $Id: update.php 1799 2015-11-13 14:01:58Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\AgencyOffice
 */

?>
<div class="agency-office-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
