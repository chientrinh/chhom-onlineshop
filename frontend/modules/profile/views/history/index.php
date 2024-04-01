<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/history/index.php $
 * $Id: index.php 3109 2016-11-25 04:20:50Z mori $
 *
 * $customer Customer
 * $dataProvider DataProvider of Purchase
 */

use \yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label' => "一覧"];
?>

<div class="profile-history-index">

    <h1 class="mainTitle">マイページ</h1>
	<p class="mainLead">お客様ご本人のご購入履歴やお届け先の閲覧・編集などができます。</p>

    <div class="col-md-3">
    <div class="Mypage-Nav">
	<div class="inner">
    <h3>Menu</h3>
    <?= Yii::$app->controller->nav->run() ?>
	</div>
    </div>
    </div>
    
    <div class="col-md-9">
	<h2>ご購入の履歴</h2>

<?php if(0 == $dataProvider->totalCount): ?>
	<p class="windowtext">購入履歴はありません。</p>
<?php else: $dataProvider->sort = false; ?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '全 {totalCount} 件',
        'layout'       => '{pager}{items}{pager}{summary}',
        'columns'      => [
            [
                'attribute' => 'create_date',
                'format'    => 'date',
                'headerOptions' => ['class'=>'Date'],
            ],
            [
                'attribute' => 'branch_id',
                'value'     => function($data){ if($data->branch) return $data->branch->name; },
                'headerOptions' => ['class'=>'Date'],
            ],
            [
                'attribute' => 'purchase_id',
                'value'     => function($data){ return sprintf('%06d',$data->purchase_id); },
                'contentOptions'=>['class'=>'text-right'],
                'headerOptions' => ['class'=>'Code'],
            ],
            [
                'attribute'=> 'total_charge',
                'format'=>'currency',
                'contentOptions'=>['class'=>'text-right'],
                'headerOptions' => ['class'=>'Price'],
            ],
            [
                'attribute' => 'status',
                'value'     => function($data){ return $data->statusName; },
                'headerOptions' => ['class'=>'Status'],
            ],
            [
                'label' => "詳細",
                'format'=> 'html',
                'value' => function($data){ return Html::a("詳細",['view','id'=>$data->purchase_id],['class'=>'btn btn-primary']); },
                'contentOptions'=>['class'=>'text-center'],
                'headerOptions' => ['class'=>'Details'],
            ],
        ],
]); ?>
<?php endif ?>

    </div><!-- col-md-9 -->

</div>
