<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\ProductMaster;
use common\models\ProductSubcategory;
use common\models\RemedyVial;
use common\models\Subcategory;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/remedy/_related.php $
 * $Id: _related.php 3442 2017-06-21 11:57:57Z mori $
 *
 * $this  yii\base\View
 * $model common\models\ProductMaster
 */
$q1 = ProductMaster::find()
    ->where(['remedy_id' => $remedy->remedy_id]);

$q2 = ProductSubcategory::find()
    ->where(['ean13' => $q1->select('ean13') ]);

$q3 = Subcategory::find()
    ->where([
        'subcategory_id' => $q2->select('subcategory_id')
    ])
    ->andWhere(['restrict_id' => 0])
    ->orderBy(['weight'=>SORT_DESC, 'parent_id'=>SORT_ASC]);
?>

<div id="itemLineup" class="col-md-8 col-sm-6 col-xs-12">
    <h2 class="maintitle">同じカテゴリーの商品</h2>

    <?php foreach($q3->each() as $sub): ?>
        <?php
        $query = \common\models\ProductMaster::find()
                   ->where(['ean13' =>
                       \common\models\ProductSubcategory::find()
                       ->where(['or',
                                ['not', ['remedy_id' => $remedy->remedy_id]],
                                ['remedy_id' => null],
                       ])
                       ->andWhere(['subcategory_id' => $sub->subcategory_id])
                       ->select('ean13'),
                   ])
                   ->andWhere(['restrict_id' => 0 ])
                   ->andWhere(['or',
                               ['not', ['vial_id' => RemedyVial::DROP] ],
                               ['vial_id' => null],
                   ])
                   ->orderBy(['dsp_priority' => SORT_DESC]);

        if(100 < $query->count())
            continue; // 同一カテゴリに 100 件超の商品があれば描画を省略する
        ?>

        <h3 class="cattitle">
            <span class="inner">
                <?php foreach($sub->ancestors as $k => $anc): ?>
                    <?= Html::a($anc->name, ["/{$remedy->company->key}/subcategory",'id'=>$anc->subcategory_id]) ?> /
                <?php endforeach ?>
                <?= Html::a($sub->name, ["/{$remedy->company->key}/subcategory",'id'=>$sub->subcategory_id]) ?>
                (<?= (int)$query->count() ?>)
            </span>
        </h3>

        <?= \yii\widgets\ListView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => 100, // 最大 100 件まで描画
                ],
                'sort' => ['defaultOrder' => ['dsp_priority' => SORT_ASC]],
            ]),
            'layout' => '{items}',
            'options' => ['class' => 'slider1 category-item'],
            'itemView' => function ($model, $key, $index, $widget)
            {
                $cache   = new \yii\caching\FileCache([
                    'keyPrefix' => 'product-view-related-',
                ]);

                $page_id = $cache->buildKey($model->ean13);

                if($html = $cache->get($page_id))
                    return $html;

                $duration   = 3600 * 24 * 365; // 365 days
                $dependency = new \yii\caching\DbDependency([
                    'sql'    => 'SELECT update_date FROM mvtb_product_master WHERE ean13 = :e',
                    'params' => [':e' => $model->ean13],
                ]);

                $html = $widget->render('@frontend/views/product/_related-item', ['model'=>$model]);

                $cache->set($page_id, $html, $duration, $dependency);

                return $html;
            },
        ]) ?>
    <?php endforeach ?>

</div>

