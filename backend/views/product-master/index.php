<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-master/index.php $
 * $Id: index.php 3260 2017-04-19 08:56:53Z kawai $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider of RemedyStock
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\helpers\ArrayHelper;
use \common\models\Company;
use \common\models\RemedyPotency;
use \common\models\RemedyVial;


// apply search filter immediately
$jscode = "
$('select').change(function(){
    $(this).submit();
    return false;
});
";
$this->registerJs($jscode);

$dataProvider->sort = new \yii\data\Sort([
    'enableMultiSort' => true,
    'attributes' => [
        'ean13',
        'remedy' => [
            'asc'  => ['remedy_id'=>SORT_ASC ],
            'desc' => ['remedy_id'=>SORT_DESC],
        ],
        'company_id',
        'potency_id',
        'vial_id',
        'kana',
        'price',
        'restrict_id',
        'in_stock',
        'dsp_priority',
    ],
]);

$company_id = Yii::$app->request->get('company_id');

$companies = Company::find()->all();
$companies = \yii\helpers\ArrayHelper::map($companies, 'company_id','key');

$potencies = RemedyPotency::find()->all();
$potencies = \yii\helpers\ArrayHelper::map($potencies, 'potency_id','name');

$vials     = RemedyVial::find()->all();
$vials     = \yii\helpers\ArrayHelper::map($vials, 'vial_id','name');

$restricts = \common\models\ProductRestriction::find()->all();
$restricts = \yii\helpers\ArrayHelper::map($restricts, 'restrict_id','name');

?>

<div class="pull-right">
    <?= Html::a("CSV表示", Url::current(['format'=>'csv']), ['class'=>'btn btn-default']) ?>
    <?php //TODO:ticket:688 商品検索ボタン画面不具合　対応が複雑なため、一次リリース以降に。
     //Html::a("検索",['/product/search','company'=>Yii::$app->request->get('company_id')],['class'=>'btn btn-info']) ?>
</div>

<h1><?= $this->context->title?></h1>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'method'=>'get',
    'action'=> ['index'],
]) ?>

<?= Html::hiddenInput('company_id', $company_id) ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'caption'      => '表示件数 ' . Html::dropDownList('pagesize',Yii::$app->request->get('pagesize',20),[20=>20,50=>50,100=>100,500=>500,1000=>1000]),
    'layout'       => '{summary}{pager}{items}{pager}',
    'pager'        => ['maxButtonCount' => 20],
    'columns' => [
        ['class'=>\yii\grid\SerialColumn::className()],
        [
            'attribute' => 'company_id',
            'value'     => function($data)
            {
                return strtoupper(ArrayHelper::getValue($data,'category.seller.key'));
            },
            'filter'    => $companies,
            'visible'   => ! $company_id,
        ],
        [
            'label' => '供給',
            'value' => function($data){ return strtoupper(ArrayHelper::getValue($data,'category.vendor.key')); },
            'visible'   => ! $company_id,
        ],
        [
            'attribute' => 'category.name',
        ],
        [
            'attribute' => 'name',
            'format'=>'html',
            'value' => function($data){
                if($data->product){ return Html::a($data->name, ['/product/view','id'=>$data->product_id]); }
                if($data->stock  ){ return Html::a($data->name,['/remedy/viewbyname','name'=>$data->stock->remedy->abbr]);}
            },
        ],
        'kana',
        [
            'attribute' => 'price',
            'format' => 'currency',
            'contentOptions' => ['class'=>'text-right'],
        ],
        [
            'label' => 'code',
            'value' => function($data){
                if($data->product){ return $data->product->code; }
            },
        ],
        [
            'attribute' => 'pickcode',
            'value' => function($data){
                if($p = $data->product){ return $p->pickcode; }
                if($s = $data->stock  ){ return $s->pickcode; }
            },
            'visible'   => (Company::PKEY_TROSE != $company_id),
        ],
        [
            'attribute'=> 'ean13',
        ],
        [
            'attribute' => 'remedy',
            'value'     => function($data){ return ArrayHelper::getValue($data, 'stock.remedy.name'); },
            'visible'   => (Company::PKEY_TROSE != $company_id),
        ],
        [
            'attribute' => 'potency_id',
            'value'     => function($data){ return ArrayHelper::getValue($data, 'stock.potency.name'); },
            'filter'    => $potencies,
            'visible'   => (Company::PKEY_TROSE != $company_id),
        ],
        [
            'label'     => 'vial',
            'attribute' => 'vial_id',
            'value'     => function($data){ return ArrayHelper::getValue($data, 'stock.vial.name'); },
            'filter'    => $vials,
            'visible'   => (Company::PKEY_TROSE != $company_id),
        ],
        [ 
            'attribute' => 'restrict_id',
            'value'     => function($data)
            {
                return $data->restriction->name;
            },
            'filter'    => $restricts,
        ],
        [
            'attribute' => 'in_stock',
            'format'    => 'boolean',
            'filter'    => [ 0 => 'いいえ', 1 => 'はい'],
        ],
        'dsp_priority',
        [
            'class'=> \yii\grid\ActionColumn::className(),
            'template' => '{update}',
        ]
    ],
]) ?>

<?php $form->end() ?>

<p class="pull-right">
<?= Html::a('アップロード',['batch-update'],['class'=>'btn btn-warning']) ?>
</p>
