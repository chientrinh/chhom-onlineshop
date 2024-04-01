<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/campaign/_tab.php $
 * $Id: _tab.php $
 */

use \yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use \common\models\Campaign;

$actionId = Yii::$app->controller->action->id;

?>

<?= $this->render('_tab', ['campaign' => $campaign]); ?>

<p class="pull-right btnArea">
        <?= Html::a('カテゴリー追加', ['add', 'id' => $campaign->campaign_id, 'target' => 'category'], ['class' => 'btn btn-green']) ?>
</p>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'pager'        => ['maxButtonCount' => 20],
    'layout'       => '{items}{pager}{summary}',
    'emptyText'    => 'カテゴリーはありません',
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['class'=>'col-searial'],
        ],
        [
            'attribute' => 'category',
            'label'     => 'カテゴリー名',
            'format'    => 'raw',
            'value'     => function($data){ 
                if (! $data->category) return null;
                return Html::a($data->category->fullname, ['category/view', 'id'=>$data->category_id]);
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
            'visible' => $campaign->campaign_type == Campaign::DISCOUNT ? true :false
        ],
        [
            'label'     => '会員ランク',
            'format'    => 'raw',
            'value'     => function($data){
                if (! $data->grade) return null;
                return Html::a($data->grade->name, ['customer-grade/view', 'id'=>$data->grade_id]);
            },
            'headerOptions' =>['class'=>'col-md-2'],
            'visible' => $campaign->campaign_type == Campaign::POINT ? true :false
        ],
        [
            'attribute' => 'point_rate',
            'label'     => 'ポイント付与率（％）',
            'format'    => 'raw',
            'value'     => function($data){ 
                return $data->point_rate;
            },
            'headerOptions' =>['class'=>'col-md-1'],
            'visible' => $campaign->campaign_type == Campaign::POINT ? true :false
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
                        'id'  => $data->campaign_id,
                        'category_id'  => $data->category_id,
                        'grade_id'     => $data->grade_id,
                        'target'       => 'category',
                        ], 
                        ['class' => 'btn btn-primary update']
                )."&nbsp";
                $link[] = Html::a('削除',
                        ['del',
                        'id'  => $data->campaign_id,
                        'category_id'  => $data->category_id,
                        'grade_id'     => $data->grade_id,
                        'target'       => 'category',
                        ],
                        ['class' => 'btn btn-danger update']
                )."&nbsp";

                
                return implode('&nbsp', $link);
            },
            'headerOptions' => ['class'=>'col-md-2'],
        ],
    ],
]); ?>


