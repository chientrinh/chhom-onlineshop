<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_popular.php $
 * $Id: _popular.php 2908 2016-10-02 01:52:37Z mori $
 *
 * $searchModel  Model
 * $dataProvider ActiveDataProvider of \backend\models\Product
 */
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use \common\models\Category;


$csscode="
    .col-searial { width: 2px; }
    .col-apply   { width: 12%; }
";
$this->registerCss($csscode);

$dataProvider->pagination->defaultPageSize = '40';
$dataProvider->pagination->pageSize = '40';

$categories = ArrayHelper::map(Category::find()->getCosmeAndFood()->all(), 'category_id', 'name');
?>

<?php if(Yii::$app->user->can('viewProduct')): ?>
<?= $this->render('__tabs',[
    'company' => $searchModel->company,
]) ?>

<?= \yii\grid\GridView::widget([
    'id' => 'product-grid-view',
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'layout'       => '{pager}{summary}{items}{pager}',
    'emptyText'    => '商品はありません',
    'columns'   => [
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
        [
            'attribute' => 'name',
            'label'  => '品名',
            'format'    => 'html',
            'value'     => function($data)
            {
                return $data->name;
            },
            'headerOptions' => ['class'=>'col-md-7'],
        ],
        [
            'attribute' => 'price',
            'label'  => '価格',
            'format'    => 'currency',
            'headerOptions' => ['class'=>'col-md-4'],
            'contentOptions'=> ['class'=>'text-right'],
        ],
    ],
    
])

?>

<?php endif ?>