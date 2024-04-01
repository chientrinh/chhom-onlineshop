<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/agency-rank/view/_subcategory.php $
 * $Id: _subcategory.php $
 */

use \yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use \common\models\AgencyRank;

?>

<?= $this->render('_tab', ['model' => $rank]); ?>


<p class="pull-right btnArea">
        <?= Html::a('サブカテゴリー追加', ['add', 'id' => $rank->rank_id, 'target' => 'subcategory'], ['class' => 'btn btn-green']) ?>
</p>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout'       => '{items}{pager}{summary}',
    'pager'        => ['maxButtonCount' => 20],
    'emptyText'    => 'サブカテゴリーはありません',
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['class'=>'col-searial'],
        ],
        [
            'attribute' => 'subcategory',
            'label'     => 'サブカテゴリー名',
            'format'    => 'raw',
            'value'     => function($data){ 
                if (! $data->subCategory) return null;
                return Html::a($data->subCategory->fullname, ['subcategory/view', 'id'=>$data->subcategory_id]);
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
                        'id'  => $data->rank_id,
                        'subcategory_id'  => $data->subcategory_id,
                        'target'       => 'subcategory',
                        ], 
                        ['class' => 'btn btn-primary update']
                )."&nbsp";

                $link[] = Html::a('削除',
                        ['del',
                        'id'  => $data->rank_id,
                        'subcategory_id'  => $data->subcategory_id,
                        'target'       => 'subcategory',
                        ],
                        ['class' => 'btn btn-danger update']
                )."&nbsp";

                return implode('&nbsp', $link);
            },
            'headerOptions' => ['class'=>'col-md-2'],
        ],
    ],
]); ?>
