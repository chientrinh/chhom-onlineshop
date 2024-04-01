<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/product/_related.php $
 * $Id: _related.php 3889 2018-05-22 08:26:11Z mori $
 *
 * $this  yii\base\View
 * $model common\models\ProductMaster
 */

?>

<div id="itemLineup" class="col-md-9 col-sm-6 col-xs-12">
    <h2 class="maintitle">同じカテゴリーの商品</h2>

    <?php foreach($model->subcategories as $sub): ?>
        <?php
        // サイト非公開サブカテゴリは表示しない
        if ($sub->restrict_id == common\models\Subcategory::PKEY_RESTRICT) {
            continue;
        }
        $query = \common\models\ProductMaster::find()
                   ->where(['ean13' =>
                       \common\models\ProductSubcategory::find()
                       ->where(['not', ['ean13' => $model->ean13]])
                       ->andWhere(['subcategory_id' => $sub->subcategory_id])
                       ->andWhere(['not', ['restrict_id' => \common\models\ProductRestriction::PKEY_INSTORE_ONLY]])
                       ->select('ean13'),
                   ]);
        if(! $query->exists() || 100 < $query->count())
            continue; // 同一カテゴリに 100 件超の商品があれば描画を省略する
        ?>

        <h3 class="cattitle">
            <span class="inner">
                <?php foreach($sub->ancestors as $k => $anc): ?>
                    <?= Html::a($anc->name, ["/{$model->company->key}/subcategory",'id'=>$anc->subcategory_id]) ?> /
                <?php endforeach ?>
                <?= Html::a($sub->name, ["/{$model->company->key}/subcategory",'id'=>$sub->subcategory_id]) ?>
                (<?= (int)$query->count() ?>)
            </span>
        </h3>

        <?= \yii\widgets\ListView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => 100, // 最大 100 件まで描画
                ],
                'sort' => ['defaultOrder' => ['dsp_priority' => SORT_DESC]],
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

