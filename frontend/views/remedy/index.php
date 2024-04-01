<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/remedy/index.php $
 * $Id: index.php 3356 2017-05-31 14:37:32Z kawai $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\ProductSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

if($firstLetter = Yii::$app->request->get('firstLetter'))
    $this->params['breadcrumbs'][] = ['label' => $firstLetter, 'url' => [$firstLetter]];

$company = \common\models\Company::findOne(\common\models\Company::PKEY_HJ);
$columns = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($columns);
$this->title = implode(' | ', $columns);

$this->params['body_id'] = 'Search';

?>

<h1 class="mainTitle">
    <?php if($firstLetter): ?>
        <?= $firstLetter ?><small>ではじまるレメディー</small>
    <?php else: ?>
        すべてのレメディー
    <?php endif ?>
</h1>

<div class="col-md-2 col-sm-6 col-xs-12">
        <?= \frontend\widgets\SearchMenu::widget([
            'company'     => \common\models\Company::PKEY_HJ,
            'searchModel' => new \frontend\models\SearchProductMaster([
                'customer'    => Yii::$app->user->identity,
                'category_id' => Yii::$app->request->get('category'),
                'keywords'    => Yii::$app->request->get('firstLetter'),
            ]),
            'submenu'     => ['remedy'],
            'action'      => ['/product/search'],
        ]) ?>
        
        <?= \frontend\widgets\SearchMenuForSmartPhone::widget([
            'company'     => \common\models\Company::PKEY_HJ,
            'searchModel' => new \frontend\models\SearchProductMaster([
                'customer'    => Yii::$app->user->identity,
                'category_id' => Yii::$app->request->get('category'),
                'keywords'    => Yii::$app->request->get('firstLetter'),
            ]),
            'submenu'     => ['remedy'],
            'action'      => ['/product/search'],
        ]) ?>
</div>

<div class="col-md-9 col-sm-6 col-xs-12">

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'abbr',
                'format'    => 'html',
                'value'     => function($model){ return Html::a($model->abbr, ['view','id'=>$model->remedy_id]); },
            ],
            'ja',
        ],
    ]); ?>

</div>
