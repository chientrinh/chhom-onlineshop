<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product/index.php $
 * $Id: index.php 3260 2017-04-19 08:56:53Z kawai $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\ProductSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */


if($searchModel->company)
{
    $categories = \yii\helpers\ArrayHelper::map(\common\models\Category::find()->where(['seller_id'=>$searchModel->company])->all(), 'category_id', 'name');
    $companies = ArrayHelper::map(\common\models\Company::find()->where(['company_id'=>$searchModel->company])->all(),  'company_id', 'name');
}
else
{
    $categories = ArrayHelper::map(\common\models\Category::find()->all(), 'category_id', function($model){ return sprintf('%s:%s', strtoupper($model->seller->key), $model->name); });
    $companies  = ArrayHelper::map(\common\models\Company::find()->all(),  'company_id', 'name');
}
array_unshift($categories,'');
array_unshift($companies,'');
asort($categories);

if($cid = Yii::$app->request->get('company'))
    $this->params['breadcrumbs'][] = [
        'label' => ArrayHelper::getValue($companies, $cid),
        'url'   => ['index','company'=>$cid]
    ];

if('search' == Yii::$app->controller->action->id)
    $this->params['breadcrumbs'][] = [
        'label' => "検索",
        'url'   => ['search','company'=>$cid]
    ];

    ?>

<div class="product-index">

    <?php if('search' == Yii::$app->controller->action->id): ?>
        <?= $this->render('_search', ['model' => $searchModel,
                                      'companies' => $companies,
                                      'categories'=> $categories,
        ]); ?>
    <?php else: ?>
        <div class="row">
            <div class="col-md-6">
            </div>
            <div class="col-md-6">
                <p class="dropdown-menu-right">
                    
                    <?php // テナント時には非表示とする
                        if(!Yii::$app->user->identity->hasRole(["tenant"])){ ?>

                        <?php $form = ActiveForm::begin([
                            'action'=> \yii\helpers\Url::toRoute(['/product/index']),
                            'method'=>'get']);
                        ?>
                        <?= $form->field($searchModel, 'company')->label(false)->dropDownList($companies,[
                            'onChange' => 'this.form.submit()',
                            'name'     => 'company',
                        ]) ?>
                        <?php ActiveForm::end(); ?>
                    <?php } ?>
                    <?= Html::a("全商品一括CSV出力", ['index','company'=>$searchModel->company ? $searchModel->company : 0, 'csv'=>1], ['class' => 'btn btn-default pull-right']) ?>
                </p>
            </div>
        </div>
    
<!--        <div class="pull-right">
        <?php
            // TODO:ticket:688 商品検索ボタン画面不具合　一次リリースから除外して後々対応
            //Html::a("検索",['search','company'=>$cid],['class'=>'btn btn-info'])
        ?>
        </div>-->
    <?php endif ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => ('search' == Yii::$app->controller->action->id) ? null : $searchModel,
        'pager'        => ['maxButtonCount' => 20],
        'layout'       => '{summary}{pager}{items}{pager}',
        'columns'      => [

            'product_id',
            [
                'attribute' => 'category_id',
                'format'    => 'raw',
                'value'     => function($data){ if($c = $data->category) return $c->name; },
                'filter'    => $categories,
            ],
            'code',
            [
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a($data->name, ['view','id'=>$data->product_id]); },
            ],
            'kana',
            'price',
            [
                'attribute' => 'in_stock',
                'format'    => 'html',
                'value'     => function($data){ return $data->in_stock ? 'OK' : Html::tag('span','NG',['class'=>'btn btn-danger']); },
                'filter'    => [1=>'OK',0=>'NG'],
            ],
            [
                'attribute' => 'restrict_id',
                'value'     => function($data){ return $data->restriction->name; },
                'filter'    => \yii\helpers\ArrayHelper::map(\common\models\ProductRestriction::find()->all(), 'restrict_id', 'name'),
            ],
            [
                'attribute' => 'start_date',
                'format'    => 'date',
            ],
            [
                'attribute' => 'expire_date',
                'format'    => 'date',
            ],
            [
                'class' => \yii\grid\ActionColumn::className(),
                'template' => '{sales}{offer}',
                'buttons' => [
                    'sales' => function ($url, $model, $key)
                    {
                        return Html::a('', ['view','id'=>$model->product_id,'target'=>'sales'],['class'=>'glyphicon glyphicon-signal','title'=>'売上']);
                    },
                    'offer' => function ($url, $model, $key)
                    {
                        return Html::a('', ['view','id'=>$model->product_id,'target'=>'offer'],['class'=>'glyphicon glyphicon-certificate','title'=>'ご優待']);
                    },
                ],
            ],
        ],
        'rowOptions' => function($model, $key, $index, $grid)
        {
            if($model->isExpired())
                return ['class'=>'text-danger'];
        },
    ]); ?>

    <?php if('index' == Yii::$app->controller->action->id): ?>
    <p>
        <?= Html::a("商品を追加", ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php endif ?>

</div>
