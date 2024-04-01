<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/agency-office/create.php $
 * $Id: create.php 1799 2015-11-13 14:01:58Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\AgencyOffice
 */

?>
<div class="agency-office-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
