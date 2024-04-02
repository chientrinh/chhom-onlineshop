<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\ProductMaster;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/coupon/view.php $
 *
 * @var $this yii\web\View
 * @var $model common\models\sodan\BookTemplate
 */

$title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $title];
$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;

$master = ProductMaster::findOne(['product_id'=>$model->product_id]);
?>
<div class="wait-list-view">

    <?php if($model->product_id): ?>
        <p class="pull-right">
            <?= Html::a('編集', ['update', 'id' => $model->product_id], ['class' => 'btn btn-primary']) ?>
        </p>
    <?php endif ?>

    <h1><?= $model->name ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'category',
                'format'    => 'html',
                'value'     => Html::a($model->category->name, ['/category/view','id'=>$model->category_id]),
            ],
            [
                'attribute' => 'subcategories',
                'format'    => 'html',
                'value'     => \yii\grid\GridView::widget([
                    'dataProvider'=>new \yii\data\ActiveDataProvider(['query'      => $model->getSubcategories(),
                                                                      'pagination' => false,]),
                    'columns' => [
                        [
                            'attribute'=> 'name',
                            'format'   => 'html',
                            'value'    => function($data){ return Html::a($data->fullname, ['/subcategory/view','id'=>$data->subcategory_id]); },
                        ],
                    ],
                    'layout'       => '{items}',
                    'tableOptions' => ['class' => 'table-condensed'],
                    'showHeader'   => false,
                    'emptyText'    => '<span class="not-set">未定義です </span>' . Html::a('追加',['update','id'=>$model->product_id,'#'=>'subcategory'],['class'=>'btn btn-xs btn-warning']),
                ]),
            ],
            [
                'attribute' => 'code',
                'format'    => 'raw',
                'value'     => Html::tag('code', $model->code),
            ],
            [
                'attribute' => 'barcode',
                'format'    => 'raw',
                'value'     => $model->barcode
            ],
            [
                'label'    => '表示名',
                'format'   => 'html',
                'value'    => $master
                            ? $master->name . Html::a('変更',['/product-master/update','id'=>$master->ean13],['class'=>'btn btn-xs btn-default pull-right'])
                            : null,
            ],
            'name',
            'kana',
            [
                'attribute' => 'price',
                'format'    => 'raw',
                'value'     => sprintf("&yen;%s", number_format($model->price)),
            ],
            [
                'attribute' => 'recommend_flg',
                'format'    => 'raw',
                'value'     => $model->recommend_flg ? '表示する' : '表示しない',
                'visible'   => !$model->restrict_id //「制限なし」の商品以外は表示しない
            ],
            [
                'attribute' => 'recommend_seq',
                'format'    => 'html',
                'visible'   => $model->recommend_flg
            ],
            [
                'attribute' => 'summary',
                'format'    => 'html',
            ],
            [
                'attribute' => 'description',
                'format'    => 'html',
            ],
            'start_date:date',
            [
                'attribute' => 'expire_date',
                'format'    => ['date', 'php:Y-m-d'],
            ],
        ],
    ]) ?>
</div>
