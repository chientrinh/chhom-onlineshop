<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/offer-seasonal/create.php $
 * $Id: create.php 3852 2018-04-26 04:54:39Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\OfferSeasonal
 */

$this->title = 'ご優待を追加';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<div class="offer-seasonal-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
