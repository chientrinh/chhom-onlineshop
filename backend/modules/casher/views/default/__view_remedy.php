<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_remedy.php $
 * $Id: _remedy.php 3021 2016-10-27 00:46:32Z mori $
 *
 * $searchModel  Model
 * $dataProvider ActiveDataProvider of \backend\models\Product
 */
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use \common\models\Subcategory;

$csscode="
    .col-searial { width: 2px; }
    .col-apply   { width: 12%; }
";
$this->registerCss($csscode);

$dataProvider->pagination->defaultPageSize = '40';
$dataProvider->pagination->pageSize = '40';
?>

<?= \yii\grid\GridView::widget([
    'id'           => 'product-grid-view',
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'layout'       => '{pager}{summary}{items}{pager}',
    'emptyText'    => '商品はありません',
    'columns'      => [
        [
            'label'  => '',
            'format' => 'raw',
            'value'  => function($data) use ($target)
            {
                $form_data = ['model'=>$data, 'target'=>$target];
                
                if(0 < $data->product_id)
                    return $this->render('form-product', $form_data);

                elseif(0 < $data->remedy_id)
                    return $this->render('form-remedy', $form_data);
            },
            'headerOptions' => ['class'=>'col-apply'],
        ],
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['class'=>'col-searial'],
        ],
        // [
        //     // 'attribute' => 'ID',
        //     'label'     => 'ID',
        //     'format'    => 'html',
        //     'value'     => function ($data) 
        //             {
        //                 return $data->product_id ? $data->product_id : ($data->remedy_id ? $data->remedy_id : null);
        //             },
        //     // 'headerOptions' =>['class'=>'col-md-1'],
        // ],
        // [
        //     'attribute' => 'subcategory_id',
        //     'label'     => 'サブカテゴリー',
        //     'format'    => 'raw',
        //     'filter'    => ArrayHelper::map(Subcategory::find()->$target()->all(), 'subcategory_id', 'fullname'),
        //     'value'     => function($data) use ($searchModel)
        //     {
        //         // return $data->subcategory->
        //         $q = \common\models\Subcategory::find();

        //         if($sid = $searchModel->subcategory_id)
        //             $q->where(['subcategory_id' => $sid]);
        //         else
        //             return null;

        //         return $q->exists() ? $q->one()->name : null;
        //     },
        //     'headerOptions' =>['class'=> in_array($target, ['flower', 'flower2']) ? 'col-md-4' : ($target == 'tincture' ? 'col-md-5' : 'col-md-3')],
        // ],
        [
            'attribute'     => 'kana',
            'label'         => '商品名',
            'format'        => 'html',
            'value'         => function ($data) 
            { 
                return $data->name; 
            },
            'headerOptions' => function () use ($target) 
            {
                if (in_array($target, ['flower', 'flower2']))
                    return "['class'=> 'col-md-5']";
                elseif ($target == 'tincture')
                    return "['class'=> 'col-md-2']"; 

                return "['class'=> 'col-md-3']";
            },
        ],
        [
            'attribute'     => 'potency_id',
            'label'         => 'ポーテンシー',
            'format'        => 'html',
            'filter'        => $potencies,
            'value'         => function($data) { return $data->potency ? $data->potency->name : null; },
            'headerOptions' => ['class'=>'col-md-1'],
            'visible'       => ! in_array($target, ['tincture', 'flower', 'flower2']),
        ],
        [
            'attribute'     => 'vial_id',
            'label'         => '容器',
            'format'        => 'html',
            'filter'        => $vials,
            'value'         => function($data) { return $data->vial ? $data->vial->name : null; },
            'headerOptions' => ['class'=>'col-md-2'],
            'visible'       => ! in_array($target, ['flower', 'flower2', 'kit']),
        ],
        [
            'attribute'      => 'price',
            'label'          => '価格',
            'format'         => 'raw',
            'value'          => function($data) { return $data->price; },
            'headerOptions'  => ['class'=>'col-md-1'],
            'contentOptions' => ['class'=>'text-right'],
        ],
    ],
])?>