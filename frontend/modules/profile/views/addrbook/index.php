<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/addrbook/index.php $
 * $Id: index.php 3964 2018-07-13 06:01:08Z mori $
 *
 * $customer Customer
 * $dataProvider DataProvider of CustomerAddrbook
 * $searchModel  SearchCustomerAddrBook
 */

use \yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label'=>"一覧"];

$link4create = '<div class="form-group">'
                  . Html::a("新しいお届け先を追加", ['create'], ['class'=>'btn btn-primary'])
                  .'</div>';

?>

<div class="cart-view">
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
	<h2><span>お届け先の追加・変更</span></h2>
	<p class="windowtext">登録住所以外へのご住所へ送付される場合等にご利用いただくことができます。<br>※最大800件まで登録できます。</p>

<?= $link4create ?>

    <?= \yii\grid\GridView::widget([
        'id'           => 'grid-view',
        'dataProvider' => $dataProvider,
        'columns'      => [
            [
                'attribute' => 'name',
                'format'    => 'html',
                'value' => function($data){ return sprintf('%s %s  <strong><font color="red">%s</font></strong>', $data->name, $data->code ? "<br> 会員証：".$data->code." （サポート注文用）" : "", !$data->code || (Yii::$app->user && Yii::$app->user->identity->grade_id >= \common\models\CustomerGrade::PKEY_TA) ? "" : "<br />※現在の会員ランクではご利用いただけません"); },
            ],
            [
                'attribute' => 'zip',
                'label'     => '住所',
                'format'    => 'html',
                'value' => function($data){ return sprintf('〒%s <br> %s', $data->zip, $data->addr); },
            ],
            [
                'attribute' => 'tel',
                'format'    => 'html',
            ],
            [
                'class' => \yii\grid\ActionColumn::className(),
                'contentOptions'=>['class'=>'text-center'],
                    'buttons'=> [
                        'view'   => function ($url, $model, $key) { return ''; },
                        'update' => function ($url, $model, $key) { return Html::a("編集", $url, ['class'=>'btn btn-default', 'disabled' => $model->code && (Yii::$app->user && Yii::$app->user->identity->grade_id < \common\models\CustomerGrade::PKEY_TA) ? true : false]); },
                        'delete' => function ($url, $model, $key) { return ''; },
                ],
            ],
        ],

        'layout'       => '{items}'.$link4create.'{pager}{summary}',
]); ?>

</div><!-- col-md-9 -->
</div><!-- cart-view -->

