<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer/swap.php $
 * $Id: swap.php 2920 2016-10-05 05:48:48Z naito $
 *
 * @var $this yii\web\View
 * @var $model common\models\Customer
 */

use yii\helpers\Html;

$title = "親子入れ替え";

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->customer_id]];
$this->params['breadcrumbs'][] = ['label' => $title];

$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels) . ' | ' . Yii::$app->name;
?>
<div class="customer-update">

    <h1><?= Html::encode($model->name) ?></h1>

    <p class="help-block">
        家族に本会員の役割をゆずります。Emailとパスワードが設定されている場合、そのまま維持されます。（元の親が使っていたEmailとパスワードでログインできます）
    </p>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $model->children,
            'pagination' => false,
        ]),
        'layout' => '{items}',
        'columns' => [
            [
                'label' => '',
                'format'=> 'html',
                'value' => function($data)use($model){
                    return Html::a('入れ替え', ['swap','id'=>$model->customer_id,'child_id'=>$data->customer_id],['class'=>'btn btn-warning']);
                },
            ],
            'name',
            'kana',
        ],
    ]) ?>
</div>
