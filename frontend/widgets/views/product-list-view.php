<?php
/*
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/views/product-list-view.php $
 * $Id: product-list-view.php 3356 2017-05-31 14:37:32Z kawai $
 *
 * $dataProvider
 * $searchModel
 */

$heading = isset($keywords) ? $keywords : null;
if(isset($searchModel) && $searchModel->keywords)
    $heading = $searchModel->keywords;
if(isset($heading))
    $heading = sprintf("<h2><span>『%s』の商品一覧</span></h2>", $heading);
?>

<?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'layout'       => sprintf("%s\n{summary}\n<div class='sorter'>{sorter}</div>\n{pager}\n{items}\n{pager}", $heading),
        'summary'      => "全<strong> {totalCount} </strong>件中 {begin}～{end}件を表示中</p>",
        'sorter'       => [
            'class'      => '\frontend\widgets\LinkSorter',
            'attributes' => ['price','name'],
            'options'    => ['title'=>"検索結果を並べ替える"],
            'linkOptions'=> ['class'=>'btn btn-default'],
        ],
        'itemView'     => '_item',
        'viewParams'   => ['remedyStock'=>$remedyStock],
        'options' => ['class'=>'list-view col-md-10 product-search-list'],
    ]);
?>
