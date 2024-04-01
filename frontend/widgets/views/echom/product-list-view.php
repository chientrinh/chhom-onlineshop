<?php
/*
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/views/product-list-view.php $
 * $Id: product-list-view.php 1792 2015-11-12 17:44:20Z mori $
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
        'layout'       => sprintf("%s\n{summary}\n{pager}\n{items}\n{pager}", $heading),
        'summary'      => "全<strong> {totalCount} </strong>件中 {begin}～{end}件を表示中</p>",
        'itemView'     => '_item',
        'viewParams'   => ['remedyStock'=>$remedyStock],
        'options' => ['class'=>'list-view col-md-12 product-search-list'],
    ]);
?>
