<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/homoeopath/update.php $
 * $Id: update.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this  yii\web\View
 * @var $model common\models\sodan\Homoeopath
 */

$this->params['breadcrumbs'][] = ['label' => $model->customer->homoeopathname, 'url' => ['view', 'id' => $model->homoeopath_id]];
$this->params['breadcrumbs'][] = ['label' => '更新'];

$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels);

?>
<div class="homoeopath-update">

    <h1>
        <?= $model->customer->homoeopathname ?>
    </h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
