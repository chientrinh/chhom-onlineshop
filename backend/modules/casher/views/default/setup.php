<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/setup.php $
 * $Id: setup.php 3957 2018-07-05 04:59:39Z mori $
 */

use \yii\helpers\Html;

$branch = $this->context->module->branch ? $this->context->module->branch : null;
?>

<div class="dispatch-default-index">
  <div class="body-content">

    <div class="list-group col-md-2">
        <?= $this->render('_menu') ?>
    </div>

    <div class="col-md-10">
    <div class="jumbotron">
    ただいまの拠点
        <h1><?= $branch ? $branch->name : null ?></h1>
    </div>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => \common\models\Branch::find()->where(['branch_id'=>[8,9,10,11,12,13,14,2,3,4,9,10,11,12,15,16,7,17,18,19]]),
            'sort' => [
                'attributes' => ['name',
                                 'company_id',
                                 'pref'=>['asc'=>['pref_id'=>SORT_ASC],'desc'=>['pref_id'=>SORT_DESC]],
                ],
                'defaultOrder' => ['company_id' => SORT_ASC, 'name' => SORT_ASC],
            ],
            'pagination'=>false,
        ]),
        'layout' =>'{items}',
        'tableOptions' =>['class'=>'table table-condensed table-striped'],
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{setup}' ,
                'buttons' => [
                    'setup' => function ($url, $model, $key) { return Html::a('✔', $url, ['class'=>'btn btn-success']); },
                ],
            ],
            'name',
            [
                'attribute' => 'company_id',
                'label'     => '会社',
                'value'     => function($data){ return $data->company->key; },
                'contentOptions' => ['class' => 'text-uppercase'],
            ],
            [
                'attribute' => 'pref',
                'value'     => function($data){ return ($p = $data->pref) ? $p->name : null; }
            ]
        ],
    ]);?>

    </div>

  </div>
  </div>
</div>
