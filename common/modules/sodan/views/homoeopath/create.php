<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/homoeopath/create.php $
 * $Id: create.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this  yii\web\View
 * @var $model common\models\sodan\Homoeopath
 */

$this->params['breadcrumbs'][] = ['label' => '作成', 'url' => ['create']];
$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels); $labels[] = Yii::$app->name;
$this->title = implode(' | ', $labels);
?>
<div class="homoeopath-create">

    <h1>ホメオパス作成</h1>
    <p class="help-block">相談会開催可能なホメオパスを本部ホメオパス認定されている顧客から作成します</p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
