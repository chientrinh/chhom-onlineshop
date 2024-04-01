<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ピックコード';
$this->params['breadcrumbs'][] = ['label'=>$this->title, 'url' => ['/product-pickcode/index']];
?>
<div class="product-pickcode-index">

    <h1><?= Html::encode($this->title) ?></h1>

ean13,product_code,pickcode,model.name<br>
<?php foreach($dataProvider->models as $model): ?>
<?= implode(',', [
    Yii::$app->formatter->asRaw($model->ean13),
    Yii::$app->formatter->asRaw($model->product_code),
    $model->pickcode,
    Yii::$app->formatter->asRaw($model->model ? $model->model->name : null),
]) ?><br>
<?php endforeach ?>

</div>
