<?php
/*
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/views/product-grid-view.php $
 * $Id: product-grid-view.php 2779 2016-07-24 04:30:20Z mori $
 *
 * $dataProvider
 * $searchModel
 * $remedyStock
 */

use \yii\helpers\Html;

$heading = isset($keywords) ? $keywords : null;
if(isset($searchModel) && $searchModel->keywords)
    $heading = $searchModel->keywords;
if(isset($heading))
    $heading = sprintf("<h2><span>『%s』の商品一覧</span></h2>", $heading);

$formatter = Yii::$app->formatter;
?>

<?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'       => sprintf("%s\n{summary}\n{pager}\n{items}\n{pager}", $heading),
        'summary'      => "全<strong> {totalCount} </strong>件中 {begin}～{end}件を表示中</p>",
        'tableOptions' => ['class'=>'table table-striped table-bordered'],
        'options'=>['class'=>'grid-view col-md-10 product-search-list2'],
        'columns'      => [
            [
                'attribute' => 'category.name',
                'label'     => "カテゴリー",
                'visible'   => $searchModel->category_id ? false : true,
                'headerOptions' => ['class'=>'Category'],
            ],
            [
                'attribute' => 'name',
                'label'     => "商品名",
                'format'    => 'html',
                'value'     => function($data){
                    $html = [];
                    $html[] = Html::a($data->company->name,['/'.$data->company->key],['class'=>'small','style'=>'color:#999']) .'<br>';

                    if($data->product_id)
                        $html[] = Html::a($data->name, ['/product/view','id'=>$data->product_id]);

                    elseif($data->remedy_id)
                        $html[] = Html::a($data->name, ['/remedy/view','id'=>$data->remedy_id]);

                    return implode('', $html);
                },
                'headerOptions' => ['class'=>'Name'],
                'enableSorting' => false,
            ],
            [
                'attribute' => 'price',
                'label'     => "価格",
                'format'    => 'currency',
                'contentOptions' => ['class'=>'text-right'],
                'headerOptions' => ['class'=>'Price'],
                'enableSorting' => false,
            ],
            [
                'label' => '',
                    'format'=>'html',
                    'value' => function($model)use($remedyStock)
                {
                    return $this->render('__link', ['model'=>$model,'remedyStock'=>$remedyStock])
                             . '&nbsp'
                             . Html::a("もっと見る", $model->url, ['class'=>'btn btn-info']);
                },
                'headerOptions' => ['class'=>'Cart'],
            ],
        ],
    ]);
?>
