<?php
/**
 * $URL: $
 * $Id: $
 *
 *
 * $allModels array of RemedyDescription
 * $caption   string
 */

use \yii\helpers\Html;
use \common\models\RemedyVial;
use common\models\RemedyDescription;

?>

<?= \yii\helpers\Html::a("補足を追加",['remedy-description/create','remedy_id'=>$remedy_id],['class'=>'btn btn-xs btn-success']) ?>


<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider([
        'allModels'  => $allModels,
    ]),
    'layout' => '{items}',
    'caption' => $caption,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label'     => 'レメディーカテゴリー名',
            'value'     => function($data) {
                               $remedy_category = $data->remedyCategory;
                               return (! $remedy_category) ? '&nbsp ― &nbsp' : $remedy_category->remedy_category_name;
                           },
            'format'    => 'html',
            'headerOptions' => ['class'=>'col-md-2'],
        ],
        [
            'attribute' => 'title',
            'label'     => '見出し',
            'format'    => 'html',
            'headerOptions' => ['class'=>'col-md-2'],
        ],
        [
            'attribute' => 'body',
            'label'     => '本文',
            'value'     => function($data) {
                               if (! (mb_strlen($data->body) > 30))
                                   return $data->body;

                               return mb_substr($data->body, 0 , 30). "....";
                           },
            'format'    => 'html',
            'headerOptions' => ['class'=>'col-md-5'],
        ],
        [
            'attribute' => 'desc_division',
            'label'     => '説明区分',
            'format'    => 'html',
            'value'     => function($data) {
                               return RemedyDescription::getDivisionForView($data->desc_division);
                           }
        ],
        [
            'attribute' => 'seq',
            'label'     => '表示順',
            'format'    => 'html',
            'headerOptions' => ['class'=>'col-md-1'],
        ],
        [
            'attribute' => 'is_display',
            'label'     => '表示/非表示',
            'value'     => function($data){ return $data->displayName; },
            'headerOptions' => ['class'=>'col-md-1'],
        ],
        [
            'label'     => '',
            'format'    => 'raw',
            'value'     => function($data)
            {
                $link = [];

                if($data->isNewRecord)
                    return implode('', $link);
//                 Html::tag('i','',['class'=>'glyphicon glyphicon-shopping-cart btn btn-xs btn-success']),
                $link[] = Html::a(/*'詳細',*/Html::tag('i','',['class'=>'glyphicon glyphicon-eye-open']),
                        ['/remedy-description/view',
                        'id'  => $data->remedy_desc_id,
                        ]
//                         , ['class' => 'btn btn-success pop']
                )."&nbsp";

                $link[] = Html::a(/*'編集',*/Html::tag('i','',['class'=>'glyphicon glyphicon-pencil']),
                        ['/remedy-description/update',
                        'id'  => $data->remedy_desc_id,
                        ]
//                         , ['class' => 'btn btn-primary update']
                )."&nbsp";

                return implode('&nbsp', $link);
            },
            'headerOptions' => ['class'=>'col-md-1'],
        ],
    ],
]); ?>
