<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/campaign/_tab.php $
 * $Id: _tab.php $
 */

use \yii\helpers\Html;
use yii\grid\GridView;
use \common\models\AgencyRank;
?>

<?= $this->render('_tab', ['model' => $rank]); ?>

<p class="pull-right btnArea">
        <?= Html::a('商品追加', ['add', 'id' => $rank->rank_id, 'target' => 'product'], ['class' => 'btn btn-green']) ?>
</p>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout'       => '{items}{pager}{summary}',
    'pager'        => ['maxButtonCount' => 20],
    'emptyText'    => '商品はありません',
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['class'=>'col-searial'],
        ],
        [
            'attribute' => 'sku_id',
            'label'     => 'SKU_ID',
            'value'     => function($data){ 
                if (! $data->product) return null;
                return $data->product->sku_id; },
            'headerOptions' =>['class'=>'js-zenkaku-to-hankaku', 'col-md-1'],
        ],
        [
            'attribute' => 'name',
            'label'     => '商品名',
            'format'    => 'raw',
            'value'     => function($data){ 
                if (! $data->product) return null;
                return Html::a($data->product->name, ['/product/view', 'id'=>$data->product->product_id]);
            },
            'headerOptions' =>['class'=>'col-md-7'],
        ],
        [
            'attribute' => 'discount_rate',
            'label'     => '割引率（％）',
            'format'    => 'raw',
            'value'     => function($data){ 
                return $data->discount_rate;
            },
            'headerOptions' =>['class'=>'col-md-1'],
        ],
        [
            'label'     => '',
            'format'    => 'raw',
            'value'     => function($data)
            {
                $link = [];

                if($data->isNewRecord)
                    return implode('', $link);

                $link[] = Html::a('編集', 
                        ['edit',
                        'id'           => $data->rank_id,
                        'sku_id'        => $data->sku_id,
                        'target'       => 'product',
                        ], 
                        ['class' => 'btn btn-primary update']
                )."&nbsp";

                $link[] = Html::a('削除',
                        ['del',
                        'id'           => $data->rank_id,
                        'sku_id'        => $data->sku_id,
                        'target'       => 'product',
                        ],
                        ['class' => 'btn btn-danger update']
                )."&nbsp";

                return implode('&nbsp', $link);
            },
            'headerOptions' => ['class'=>'col-md-2'],
        ],
    ],
]); ?>
