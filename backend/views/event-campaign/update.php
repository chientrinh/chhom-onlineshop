<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/campaign/update.php $
 * $Id: update.php 2931 2016-10-07 04:44:08Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Vegetable
 */
$this->params['breadcrumbs'][] = ['label' => $campaign->campaign_code, 'url' => ['view', 'id' => $campaign->ecampaign_id]];
$this->params['breadcrumbs'][] = ['label' => '更新'];
?>
<div class="campaign-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'campaign' => $campaign,
    ]) ?>
</div>
