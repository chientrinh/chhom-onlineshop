<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/vegetable/create.php $
 * $Id: create.php 2931 2016-10-07 04:44:08Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Vegetable
 */

$this->params['breadcrumbs'][] = ['label' => '追加'];
?>
<div class="campaign-create">

    <h1>キャンペーンを登録</h1>

    <?= $this->render('_form', [
        'campaign' => $campaign,
        'streamings' => $streamings
    ]) ?>

</div>
