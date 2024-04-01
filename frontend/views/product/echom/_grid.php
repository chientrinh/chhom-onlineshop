<?php
/*
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/product/_grid.php $
 * $Id: _grid.php 1704 2015-10-22 04:38:06Z mori $
 *
 * $dataProvider
 */
use \yii\helpers\Html;
?>

<?php
$formatter = new \yii\i18n\Formatter();

$layout = '{summary}<div class="sorter">{sorter}</div>{pager}{items}{pager}';
if($searchModel->keywords)
    $layout = sprintf("検索ワード：<strong>%s</strong><br>\n", $searchModel->keywords) . $layout;
elseif($searchModel->categoryName)
    $layout = sprintf("<h2><span>『%s』カテゴリーの商品一覧</span></h2>\n", $searchModel->categoryName) . $layout;

$dataProvider->pagination->pagesize = 30;
$dataProvider->setSort([
    'attributes' => [
            'name' => [
                'asc'   => ['name' => SORT_ASC, 'price' => SORT_ASC],
                'desc'  => ['name' => SORT_DESC,'price' => SORT_ASC],
                'label' => '名前',
                'default' => SORT_ASC,
            ],
            'price' => [
                'asc'   => ['price' => SORT_ASC, 'name' => SORT_ASC],
                'desc'  => ['price' => SORT_DESC,'name' => SORT_ASC],
                'label' => '価格',
                'default' => SORT_ASC,
            ],
    ],
]);

echo \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'       => $layout,
        'summary'      => "全<strong> {totalCount} </strong>件中 {begin}～{end}件を表示中</p>",
            'tableOptions' => ['class'=>'table table-striped table-bordered'],
            'options'=>['class'=>'grid-view col-md-9 product-search-list2'],
        'sorter'       => [
            'class'      => '\frontend\widgets\LinkSorter',
            'attributes' => ['price','name'],
            'options'    => ['title'=>"検索結果を並べ替える"],
            'linkOptions'=> ['class'=>'btn btn-default'],
        ],
        'columns'      => [
            [
                'attribute' => 'category.name',
                'label'     => "カテゴリー",
                'label'     => $searchModel->getAttributeLabel('categories'),
                'visible'   => $searchModel->categories,
                'headerOptions' => ['class'=>'Category'],
            ],
            [
                'attribute' => 'name',
                'label'     => "商品名",
                'format'    => 'html',
                'value'     => function($data){
                    return Html::a($data->company->name,['/'.$data->company->key],['class'=>'small','style'=>'color:#999']) .'<br>'.
                            (($data->isProduct()
                            ? Html::a($data->name, ['view','id'=>$data->product_id])
                            : Html::a($data->name, ['/remedy/view','id'=>$data->remedy_id]))
                            );
                },
                'headerOptions' => ['class'=>'Name'],
                'enableSorting' => false,
            ],
            [
                
                'attribute' => 'price',
                'label'     => "価格",                    //'format'    => '',
                    'value'     => function($data)use($formatter){ return $data->isProduct()
                    ? $formatter->asCurrency($data->price)
                    : '';
                },
                'contentOptions' => ['class'=>'text-right'],
                'headerOptions' => ['class'=>'Price'],
                'enableSorting' => false,
            ],
            [
                'label' => '',
                    'format'=>'html',
                    'value' => function($model){
$html = [];
$html[] = Html::a("もっと見る", $model->url, ['class'=>'btn btn-info']);

if($model->isProduct())
    $html[] = Html::a("カートに入れる", \yii\helpers\Url::toRoute(['/cart/default/add', 'pid'=> $model->product_id]), ['class'=>'btn btn-warning']);

        return implode(' ',$html);
},
                'headerOptions' => ['class'=>'Cart'],
            ],
        ],
    ]);
?>
