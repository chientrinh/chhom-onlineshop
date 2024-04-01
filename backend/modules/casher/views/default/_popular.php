<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_popular.php $
 * $Id: _popular.php 3504 2017-07-25 11:23:46Z kawai $
 *
 * $searchModel  Model
 * $dataProvider ActiveDataProvider of \backend\models\Product
 */
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;


$csscode="
    .col-searial { width: 2px; }
    .col-apply   { width: 12%; }
";
$this->registerCss($csscode);

$dataProvider->pagination->defaultPageSize = '40';
$dataProvider->pagination->pageSize = '40';

$subcategories = \common\models\Subcategory::find()->where(['subcategory_id' => [7,8]])->all();
?>

<?= $this->render('__tabs',[
    'company' => $searchModel->company_id,
]) ?>

<?= \yii\grid\GridView::widget([
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
        //     'label'     => 'ID',
        //     'format'    => 'html',
        //     'value'     => function ($data) { return ($p = $data->product) ? $p->product_id : ''; },
        //     // 'headerOptions' =>['class'=>'col-md-1'],
        // ],
        // [
        //     'label'     => 'サブカテゴリー名',
        //     'attribute' => 'subcategory_id',
        //     'format'    => 'html',
        //     'value'     => function ($data)
        //             {
        //                 if ($sub = $data->getSubcategories()->where(['subcategory_id' => [7,8]])->one())
        //                     return $sub->name;

        //                 return '';
        //             },
        //     'headerOptions' =>['class'=>'col-md-3'],
        //     'filter'    => ArrayHelper::map($subcategories, 'subcategory_id', 'fullname'),
        // ],
        [
            'attribute' => 'kana',
            'label'     => '商品名',
            'format'    => 'html',
            'value'     => function ($data)
                    {
                        if($p = $data->product)
                            return $p->name;

                        elseif($r = $data->remedy)
                            return $r->abbr;

                        else 
                            return $r->abbr;
                    },
            'headerOptions' =>['class'=>'col-md-7'],
        ],
        [
            'attribute' => 'price',
            'label'     => '価格',
            'format'    => 'raw',
            'value'     => function($data) { return ($p = $data->product) ? $p->price : ''; },
            'headerOptions'  => ['class'=>'col-md-4'],
            'contentOptions' => ['class'=>'text-right'],
        ],
    ],
])
?>