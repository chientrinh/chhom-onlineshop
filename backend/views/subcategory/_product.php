<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Category;
use common\models\ProductMaster;
use common\models\ProductSubcategory;

/*
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/subcategory/_product.php $
 * $Id: _product.php 2776 2016-07-23 07:51:23Z naito $
 *
 * @var $this yii\web\View
 * @var $model common\models\Subcategory
 */

$categories = Category::find()->where(['seller_id'=>$model->company_id])->all();
$categories = ArrayHelper::map($categories, 'category_id', 'name');

$createUrl = Url::to(['product-subcategory/create']);
$deleteUrl = Url::to(['product-subcategory/delete']);
$subcategory_id = $model->subcategory_id;

$jscode = "
$('#toggle-btn').click(function(){
   $('#product-grid').toggle();
});

$('input[type=checkbox]').click(function() {
    if($(this).is(':checked'))
       url = '$createUrl';
    else
       url = '$deleteUrl';

    data = {
        'subcategory_id': '$subcategory_id',
        'ean13'         : $(this).val()
    };

    $.ajax({
            type: 'POST',
            url:  url,
            data: data,
            success: function(data)
            {
               $('#ajax-response').hide();
            },
            error: function(data)
            {
               $('#ajax-response').show().html('失敗しました。ページを再読込して最新の状態を確認してください');
            },
    });

});
";
$this->registerJs($jscode);


// build Query for GridView
$query       = ProductMaster::find()->andWhere(['company_id'=>$model->company_id]);
$searchModel = new ProductMaster();
$searchModel->load(Yii::$app->request->get());
if($param = $searchModel->getDirtyAttributes(['kana','category_id']))
{
    if($cid = ArrayHelper::getValue($param,'category_id'))
        $query->andWhere(['category_id' => $cid]);

    if($kana = ArrayHelper::getValue($param,'kana'))
        $query->andWhere(['like','kana', $kana]);
}
else
{
    $query->where('0=1');
    $searchModel->addError('category_id',"何か選択してください");
}

?>

<div class="form-group">
    <?= Html::button('追加・削除',['id'=>'toggle-btn','class'=>'btn btn-success']) ?>
</div>

<div id="product-grid" style="<?= $searchModel->hasErrors('category_id') ? 'display:none' : null ?>" >

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]),
        'filterModel' => $searchModel,
        'summary'     => '',
        'columns'     => [
            [
                'label' => '',
                'format' => 'raw',
                'value' => function($data)use($subcategory_id)
                {
                    $active = ProductSubcategory::find()->where([
                        'ean13'          => $data->ean13,
                        'subcategory_id' => $subcategory_id,
                    ])->exists();

                    return Html::checkbox('ean13', $active, [
                        'value' => $data->ean13,
                        'label' => '',
                        'title' => $active ? '削除する' : '追加する',
                    ]);
                },
            ],
            [
                'attribute' => 'category_id',
                'value'     => function($data){ return ($c = $data->category) ? $c->name : null; },
                'filter'    => $categories,
            ],
            'name',            
            'kana',
        ]
    ]) ?>

</div>
