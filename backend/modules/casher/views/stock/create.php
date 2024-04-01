<?php

use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $model common\models\Commission
 * 
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/stock/views/default/create.php $
 * $Id: $
 */

$title = '追加';
$this->params['breadcrumbs'][] = ['label' => '在庫', 'url'=> ['index'] ];
$this->params['breadcrumbs'][] = ['label' => '追加'];

$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$labels[] = Yii::$app->name;
$this->title = implode(' | ', $labels);
?>
<div class="commission-create">

    <h1><?= Html::encode($title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'branchs' => $branchs,
    ]) ?>

</div>
