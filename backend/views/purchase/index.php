<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/purchase/index.php $
 * $Id: index.php 3720 2017-11-02 03:48:22Z kawai $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */
$this->params['breadcrumbs'][] = ['label'=> '売上', 'url'=> 'index'];

$company_id = ArrayHelper::getValue(Yii::$app->request->get('SearchPurchase'), 'company_id', $searchModel->company_id);

$agency_array = [99 => '', 0 => 'HJ',1 => 'HE',2 => 'HP', 3 => 'HJ HE', 4 => 'HJ HP', 5 => 'HE HP', 6  => 'HJ HE HP'];


if($company_id)
    $company = \common\models\Company::findOne($company_id);
if(isset($company))
    $this->params['breadcrumbs'][] = ['label' => $company->name];

?>
<div class="purchase-index">

    <h1>売上 <small><?= isset($company) ? $company->name : "グループ全社" ?></small></h1>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'action'=> \yii\helpers\Url::toRoute('/purchase/index'),
    'method'=> 'get',
]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'filterUrl'    => \yii\helpers\Url::current(),
        'layout'       => '{items}{pager}{summary}',
        'pager'        => ['maxButtonCount' => 20],
        'summaryOptions' => ['class'=>'pull-right small text-muted'],
        'tableOptions' => ['class'=>'table table-condensed table-striped'],
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],
            ['class' => 'yii\grid\CheckboxColumn'],

            [
                'attribute' => 'purchase_id',
                'format'    => 'html',
                'value'     => function($model){
                    return Html::a(sprintf('%06d',$model->purchase_id), ['/purchase/view', 'id'=> $model->purchase_id]);
                },
                'contentOptions' => ['class'=>'text-right'],
                'headerOptions' => ['class'=>'col-md-1'],
            ],
            [
                'attribute' => 'create_date',
                'format'    => 'html',
                'value'     => function($model){
                    return date('Y-m-d H:i', strtotime($model->create_date)); },
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute'=>'create_date',
                    'language' => 'ja',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class'=>'form-control col-md-12'],
                ]),
                'headerOptions' => ['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => function($model)
                {
                    if($model->customer) {
                        return Html::a($model->customer->name,['/customer/view','id'=>$model->customer_id],['style'=>'color:black']);
                    } else {
                        // 顧客情報を取得できない（未会員が購入した）場合、deliveryから名前を取得する
                        if($d = $model->delivery){ return $d->name; }
                    }
                    return '';
                },
            ],
            ['class' => 'yii\grid\DataColumn' ,
                'attribute' => '代理店',
                //'format'    => 'raw',
                'value'     => function($model)
                {
                    return true == $model->isAgency() ? '代理店' : '個人';
                },
                'filter' => Html::activeDropDownList($searchModel, "is_agency", [
                   '' => '',
                   1 => '個人',
                   2 => '代理店',
                ],['class'=>'form-control']),
            ],
            ['class' => 'yii\grid\DataColumn' ,
                'attribute' => '代理店所属',
                'format'    => 'raw',
                'value'     => function($model)
                {
                    $agency_array = [99 => '', '0' => 'HJ','1' => 'HE', '2' => 'HP', '3' => 'HJ HE', '4' => 'HJ HP', '5' => 'HE HP', '6'  => 'HJ HE HP'];
                    return $agency_array[$model->getAgencies()];
                },
                'filter' => Html::activeDropDownList($searchModel, "agencies", $agency_array,['class' => 'form-control']),
            ],
            [
                'attribute' => 'total_charge',
                'format'    => 'raw',
                'value'     => function($model){
                    return Html::a(Html::tag('i', '&nbsp;￥'.number_format($model->total_charge), ['class'=>'glyphicon glyphicon-print']),['receipt','id'=>$model->purchase_id],['title'=>'レシートを印刷します']);
                },
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'payment_id',
                'value'     => function($model){ return $model->payment->name; },
                'filter'    => \yii\helpers\ArrayHelper::map(\common\models\Payment::find()->all(), 'payment_id','name'),           
            ],
//            [
//                'attribute' => 'shipped',
//                'label'     => '発送の状態',
//                'value'     => function($model){ return $model->shipped ? "発送済" : "未発送"; },
//                'filter'    => [0 => "未発送", 1 => "発送済"],
//            ],
            [
                'attribute' => 'status',
                'value'     => function($model){ return \yii\helpers\ArrayHelper::getvalue($model,'purchaseStatus.name'); },
                'filter'    => \yii\helpers\ArrayHelper::map(\common\models\PurchaseStatus::find()->all(), 'status_id','name'),
            ],
            [
                'attribute' => 'update_date',
                'format'    => 'datetime',
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute'=>'update_date',
                    'language' => 'ja',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class'=>'form-control col-md-12'],
                ]),
                'contentOptions' => ['class'=>'col-md-1'],
            ],
            [
                'attribute' => 'company_id',
                'format'    => 'html',
                'value'     => function($model){ return $model->company ? $model->company->key : null; },
                'filter'    => \yii\helpers\ArrayHelper::map(\common\models\Company::find()->all(), 'company_id','key'),
                'contentOptions' => ['class'=>'text-uppercase'],
                'headerOptions'  => ['class'=>'col-md-1'],
                'visible'   => Yii::$app->user->can('viewSales'),
            ],
            [
                'attribute' => 'branch_id',
                'format'    => 'html',
                'value'     => function($model)
                {
                    if($b = $model->branch)
                            return Html::tag('span',\yii\helpers\StringHelper::truncate($b->name,7),['title'=>$b->name]);
                },
                'filter'    => \yii\helpers\ArrayHelper::map(\common\models\Branch::find()->all(), 'branch_id','name'),
                'headerOptions' => ['class'=>'col-md-1'],
                'visible'   => 1 < count(array_unique(ArrayHelper::getColumn($dataProvider->models, 'branch_id')))
            ],
            [
                'attribute' => 'staff_id',
                'format'    => 'text',
                'value'     => function($model)
                {
                    if($s = $model->staff){ return $s->name01; }
                },
                'filter'    => \yii\helpers\ArrayHelper::map(\backend\models\Staff::find()->all(), 'staff_id','name'),
            ],
        ],
    ]); ?>

<p>
<?= Html::submitButton("納品書を印刷",[
    'class'    => 'btn btn-primary',
    'title'    => '納品書をダウンロードしますが、発送の状態は変更しません',
    'name'     => 'target',
    'value'    => 'default',
    'onClick'  => "this.form.action='print'",
]) ?>
</p>

<?php if(!Yii::$app->user->identity->hasRole(["tenant"])) { ?>
<p>
<?= Html::submitButton("ラベル",[
    'class'    => 'btn btn-info',
    'title'    => '滴下レメディーのラベルを出力します',
    'name'     => 'target',
    'value'    => 'remedy',
    'onClick'  => "this.form.action='print-label'",
]) ?>
</p>
<?php } ?>

<p>
<?= Html::submitButton("ヤマト便CSV",[
    'class'    => 'btn btn-default',
    'title'    => 'ヤマト便のためにCSVを出力します',
    'name'     => 'target',
    'value'    => 'default',
    'onClick'  => "this.form.action='print-csv'",
]) ?>

<?= Html::submitButton("ゆうプリ用CSV",[
    'class'    => 'btn btn-default',
    'title'    => 'ゆうプリ用のCSVを出力します',
    'name'     => 'target',
    'value'    => 'default',
    'onClick'  => "this.form.action='print-csv-for-yu-print'",
]) ?>
</p>

<?php $form->end() ?>

</div>
