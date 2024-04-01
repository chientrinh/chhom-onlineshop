<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/favorite/index.php $
 * $Id: index.php 3109 2016-11-25 04:20:50Z mori $
 *
 * $customer Customer
 * $dataProvider DataProvider of CustomerFavorite
 */

use \yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label'=>'一覧'];

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
	<h2>お気に入り</h2>
	<p class="windowtext"><?= $dataProvider->totalCount ?> 件のお気に入りがあります。</p>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'id'           => 'mypage-favorite-list',
        'layout'       => '{pager}{items}{pager}',
        'showHeader'   => false,
        'tableOptions' => ['class' => 'table'],
        'columns'      => [
            [
            'label'    => "商品画像",
            'format'   => 'html',
                        'value'    => function($data,$key,$idx,$col)
                {
                    $img = Html::img('@web/img/default.jpg'); // file must be there

                    if(($p = $data->product) && ($models = $p->images))
                        if(is_array($models))
                            if($model = array_shift($models))
                                $img = Html::img($model->url);

                    if($data->isProduct())
                        return Html::a($img, ['/product/view','id'=>$data->product_id]);
                    
                    return Html::a($img, ['/remedy/view','id'=>$data->remedy_id]);
                },
            'contentOptions'=>['class'=>'Thum'],
            ],
            [
                'label'    => '品名',
                'attribute'=> 'product.name',
                'format'   => 'html',
                'value'    =>function($data,$key,$idx,$col)
                {
                    $p = $data->product;

                    if($data->isProduct())
                        $name = Html::a($p->name, ['/product/view','id'=>$data->product_id]);
                    else
                        $name = Html::a($p->name . $p->ja, ['/remedy/view','id'=>$data->remedy_id]);

                    $btn = Html::a("×", ['delete', 'id'=>$data->product_id,'rid'=>$data->remedy_id], ['class'=>'pull-right btn btn-xs btn-default','title'=>'削除']);

                    if($p->getAttribute('code'))
                        $code = '商品コード： '. $p->code;
                    else
                        $code = null;

                    if($p->canGetProperty('company'))
                        $com = 'ショップ名：' . $p->company->name;
                    else
                        $com = null;

                    if($p->getAttribute('price'))
                        $price = Yii::$app->formatter->asCurrency($p->price);
                    else
                        $price = null;

                    return $btn
                         . Html::tag('span', implode('<br>', [$name, $code, $com]),['class'=>'pull-left'])
                         . Html::tag('span', $price.'&nbsp;', ['class'=>'pull-right']);
                }
            ],
            [
                'label'  => '',
                'format' => 'html',
                'value'  => function($data){
                    if($data->isProduct())
                        return Html::a("カートに入れる",['/cart/default/add','pid'=>$data->product_id],['class'=>'btn btn-sm btn-warning']);
                    elseif($data->remedy_id)
                        return Html::a("もっと見る",['/remedy/view','id'=>$data->remedy_id],['class'=>'btn btn-sm btn-info']);
                },
            ],
        ],
]); ?>

</div>
