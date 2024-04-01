<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Category;
use common\models\Company;
use common\models\Product;
use common\models\ProductMaster;
use common\models\RemedyStock;
use common\models\RemedyPotency;
use common\models\RemedyVial;
use common\models\Subcategory;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/hj/wholesale.php $
 * $Id: wholesale.php 3923 2018-06-06 01:40:13Z mori $
 *
 * @var $this         yii\web\View
 * @var $title        string for <title>
 * @var $h1           string for <h1>
 * @var $breadcrumbs  array
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  common\models\ProductSearch
 * @var $company      common\models\Company
 */

$title = sprintf('販売店・取扱所様専用注文入力 | %s | %s', $company->name, Yii::$app->name);
$this->title                 = $title;
$this->params['body_id']     = 'Search';
$this->params['breadcrumbs'][] = ['label'=>'販売店・取扱所様専用注文入力', 'url' => ['wholesale']];
$this->params['body_id']       = 'Company';

$query = Subcategory::find()->orderBy('weight DESC, subcategory_id ASC')
                           ->andWhere(['company_id' => $company->company_id])
                           ->andWhere(['parent_id' => null])
                           ->andWhere(['<=', 'restrict_id',4]);
if ($company->company_id == Company::PKEY_HE) {
    $query->orWhere(['subcategory_id' => Subcategory::PKEY_ONLY_HE]);
}
$subs_parent = $query->all();
$subs = array();
foreach ($subs_parent as $parent) {
    $subs[] = $parent;
    $subs = array_merge($subs, $parent->getChildren()->all());    
}

$subs = ArrayHelper::map($subs, 'subcategory_id', 'fullname');

$query = Category::find()->where(['seller_id'=>$company->company_id]);
$query->andWhere(['category_id' =>
    ProductMaster::find()->select('category_id')
                         ->andWhere(['<=','restrict_id', 4])
]);
$categories = $query->all();
$categories = ArrayHelper::map($categories, 'category_id', 'name');

$vials     = null;
$potencies = null;
if(Company::PKEY_HJ == $company->company_id)
{
$vials = RemedyVial::find()->andWhere('vial_id <> 10')->all();
$vials = ArrayHelper::map($vials, 'vial_id', 'name');

$potencies = RemedyPotency::find()->all();
$potencies = ArrayHelper::map($potencies, 'potency_id', 'name');
$potencies[RemedyPotency::COMBINATION] = 'コンビネーション';
}

$jscode = "
$('button').on('click',function(e)
{
    var form = $(this).parents('form:first');

    $.ajax({
         url:  form.attr('action'),
         type: form.attr('method'),
         data: form.serialize(),
         success: function (data) {
              form.html('<strong>' + data + '</strong>');
         },
         error: function(response) {
              $('body').html(response);
         }
    });

    return false;
});
";
$this->registerJs($jscode);
?>

<div class="product-index">

    <?php if(Company::PKEY_HJ == $company->company_id): ?>
    <p class="text-right">
        <?= Html::a('適用書レメディーの購入',['/cart/recipe/batch'],['class'=>'btn btn-success']) ?>
    </p>
    <?php endif ?>

    <h1 class="mainTitle">販売店・取扱所様専用注文入力</h1>

    <p class="mainLead"><?= $company->name?></p>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'layout'       => '{pager}{items}{pager}{summary}',
        'pager'        => ['maxButtonCount' => 20],
        'columns'      => [
            [
                'label'     => '大区分',
                'attribute' => 'category_id',
                'value'     => function($data){ return ($c = $data->category) ? $c->name : null; },
                'filter'    => $categories,
            ],
            [
                'label'     => '小区分',
                'attribute' => 'subcategory_id',
                'value'     => function($data)use($searchModel)
                {
                    $q = \common\models\Subcategory::find();

                    if($sid = $searchModel->subcategory_id)
                        $q->where([Subcategory::tableName().'.subcategory_id' => $sid]);
                    else
                        $q->where(['subcategory_id' =>
                            \common\models\ProductSubcategory::find()->where(['ean13'=>$data->ean13])
                                                                     ->select(\common\models\ProductSubcategory::tableName().'.subcategory_id')
                        ]);

                    return $q->exists() ? $q->one()->fullname : null;
                },
                'filter'    => $subs,
            ],
            [
                'label'     => '品名',
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function($data){
                    return $data->name;
                },
            ],
            [
                'attribute' => 'remedy_id',
                'value'     => function($data){ return ($r = $data->remedy) ? $r->name : null; },
                'filter'    => $vials,
                'visible'   => $vials,
            ],
            [
                'attribute' => 'vial_id',
                'value'     => function($data)use($vials){ return ArrayHelper::getValue($vials, $data->vial_id); },
                'filter'    => $vials,
                'visible'   => $vials,
            ],
            [
                'attribute' => 'potency_id',
                'value'     => function($data)use($potencies){ return ArrayHelper::getValue($potencies, $data->potency_id); },
                'filter'    => $potencies,
                'visible'   => $potencies,
            ],
            [
                'label'     => '小売価格',
                'attribute' => 'price',
                'format'    => 'currency',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'label'  => '注文数',
                'format' => 'raw',
                'value'  => function($data)
                {
                    if(0 < $data->product_id)
                        return $this->render('form-product',['model'=>$data]);

                        return $this->render('form-remedy', ['model'=>$data]);
                },
                'headerOptions' => ['class'=>'col-md-2'],
            ],
        ],
    ]) ?>

</div>
