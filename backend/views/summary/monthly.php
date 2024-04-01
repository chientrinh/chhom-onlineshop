<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/stat.php $
 * $Id: stat.php 3080 2016-11-13 06:21:17Z mori $
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;

$this->params['breadcrumbs'][] = ['label' => '月次集計', 'url' => ['monthly']];

$models = $dataProvider->getModels();
foreach ($models as $key => $value) {
    $sort_company[$key] = $value['company_id'];
    $sort_branch[$key] = $value['branch_id'];
    // 「その他」は一旦カテゴリID最大にする
    $sort_category[$key] = ($value['category_id']) ? $value['category_id'] : 100;
}
if ($models)
    array_multisort($sort_company, $sort_branch, $sort_category, $models);

$provider = new ArrayDataProvider([
    'allModels' => $models,
    'pagination' => [
        'pageSize' => 0,
    ],
    'sort' => ['attributes' => ['company_id']],
]);


$ajaxUrl = Url::to(['fetch-branch']);
$jscode = "
$('select[name=\'company\']').change(function(){
  var company_id = $(this).val();
  $.ajax({
    type: 'POST',
    url: '{$ajaxUrl}',
    data: {
      company_id : company_id
    }
  }).done(function(result) {
    var branch = JSON.parse(result);
    // 相談種別リストを作成し直す
    $('select[name=\'branch\']').empty();
    $('select[name=\'branch\']').append($('<option>').text('すべて').val('99'));
    for (var i in branch) {
      if (!branch[i]) {
        continue;
      }
      var plist = $('<option>').text(branch[i]).val(i);
      $('select[name=\'branch\']').append(plist);
    }
  }).fail(function(result) {
    alert('データ取得に失敗しました');
  });
});
";
$this->registerJs($jscode);
?>

<div class="dispatch-default-index">
  <div class="body-content">
    <div class="col-md-10" style="margin-bottom: 15px;">
        <h2>月次集計</h2>

        <?php $form = \yii\bootstrap\ActiveForm::begin([
            'method' => 'get',
            'action' => Url::current(['start_date' => null, 'end_date' => null]),
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "{label}{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label'   => 'col-sm-4',
                    'wrapper' => 'col-sm-8',
                    'error'   => '',
                    'hint'    => '',
                ],
            ],
        ]) ?>

        <div id="tab" class="btn-group pull-left" data-toggle="buttons" style="margin-right:15px;">
            <p>年</p>
            <?php
                $year = (is_null(Yii::$app->request->get('year'))) ? date('Y') : Yii::$app->request->get('year');
            ?>
            <?= Html::textInput('year', $year, ['class' => 'form-control']) ?>
        </div>


        <div id="tab" class="btn-group pull-left" data-toggle="buttons" style="margin-right:15px;">
            <p>月</p>
            <?php
                $month = (is_null(Yii::$app->request->get('year'))) ? date('n') : Yii::$app->request->get('month');
                $month_list = ['99' => 'すべて' , '1' => 1 ,'2' => 2,'3' => 3,'4' => 4,'5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, '10' => 10, '11' => 11, '12' => 12];
            ?>
            <?= Html::dropDownList('month', $month, $month_list, ['class' => 'form-control'], ['options' => [$month => ["Selected" => true]]]) ?>
        </div>

        <div id="tab" class="btn-group pull-left" data-toggle="buttons" style="margin-right:15px;">
            <p>支払方法</p>
            <?php $radioList = [0 => 'すべて'] + ArrayHelper::map(\common\models\Payment::find()->selectPayment()->each(), 'payment_id', 'name'); ?>
            <?= Html::dropDownList('payment', Yii::$app->request->get('payment'), $radioList, ['class' => 'form-control']) ?>
        </div>

        <div id="tab" class="btn-group pull-left" data-toggle="buttons" style="margin-right:15px;">
            <p>会社</p>
            <?php $company_list = [0 => 'すべて'] + ArrayHelper::map(\common\models\Company::find()->each(), 'company_id', 'name'); ?>
            <?= Html::dropDownList('company', Yii::$app->request->get('company'), $company_list, ['class' => 'form-control']) ?>
        </div>

        <div id="tab" class="btn-group pull-left" data-toggle="buttons" style="margin-right:15px;">
            <p>拠点</p>
            <?php
                $branch_id = (is_null(Yii::$app->request->get('branch'))) ? '99' : Yii::$app->request->get('branch');
                $branch_list = [99 => 'すべて'] + ArrayHelper::map(\common\models\Branch::find()->each(), 'branch_id', 'name');
            ?>
            <?= Html::dropDownList('branch', Yii::$app->request->get('branch'), $branch_list, ['class' => 'form-control']) ?>
        </div>

        <div class="col-md-4 pull-right" style="margin-top:15px;">
            <?= Html::submitInput('集計',['class' => 'form-control btn btn-info pull-right', 'style' => 'width:50%;']) ?>
            <?= Html::a('集計CSV出力',
                ['print-stat',  'company' => $company, 'branch' => $branch, 'payment' => $payment, 'year' => $year, 'month' => $month, 'mode' => 'monthly'],
                [
                 'class' => 'btn btn-default pull-right',
                 'style' => 'margin-right:1em; width:40%;',
                 'title' => '現在表示している売上集計データをCSVに出力します',
            ]) ?>
        </div>
    </div>
</div>
<?php $form->end() ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $provider,
    'layout'  => '{items}{pager}',
    'columns' => [
        [
            'attribute' => 'company_id',
            'label'     => '販売会社',
            'format'    => 'html',
            'value'     => function($data)
            {
                $company = \common\models\Company::findOne(ArrayHelper::getValue($data, 'company_id'));
                if($company)
                    return $company->name;

                return null;
            },
        ],
        [
            'attribute' => 'branch_id',
            'label'     => '拠点',
            'format'    => 'html',
            'value'     => function($data)
            {
                $branch = \common\models\Branch::findOne(ArrayHelper::getValue($data, 'branch_id'));
                if($branch)
                    return $branch->name;

                return null;
            },
        ],
        [
            'attribute' => 'category',
            'label'     => 'カテゴリー',
            'format'    => 'html',
            'value'     => function($data)
            {
                $category_id = $data->category_id;
                $company_id = $data->company_id;

                // 生野菜、酒類は別カテゴリ化
                if ($category_id == '99') {
                    $summary_category = \common\models\SummaryCategory::find()->where(['summary_category_id' => $company_id . '97'])->one();
                } else if ($category_id == '98') {
                    $summary_category = \common\models\SummaryCategory::find()->where(['summary_category_id' => $company_id . $category_id])->one();
                } else if (common\models\Category::findOne($category_id)) {
                    $summary_category = \common\models\SummaryCategory::find()->where(['category_id' => $category_id])->one();
                } else {
                    $summary_category = \common\models\SummaryCategory::find()->where(['summary_category_id' => $company_id . '99'])->one();
                }
                if (!$summary_category) {
                    return 'その他';
                }
                return $summary_category->name;
            },
            'footer'    => '合計'
        ],
        [
            'attribute' => 'subtotal',
            'label'     => '商品計',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format(array_sum(ArrayHelper::getColumn($models, 'subtotal'))),
        ],
        [
            'attribute' => 'return_total',
            'label'     => '返品計',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format(array_sum(ArrayHelper::getColumn($models, 'return_total'))),
        ],
        [
            'attribute' => 'discount_total',
            'label'     => '値引計',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format(array_sum(ArrayHelper::getColumn($models, 'discount_total'))),
        ],
        [
            'attribute' => 'total_charge',
            'label'     => '売上合計',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format(
                    array_sum(ArrayHelper::getColumn($models, 'total_charge')))
        ],
        [
            'attribute' => 'tax_total',
            'label'     => '消費税',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format(array_sum(ArrayHelper::getColumn($models, 'tax_total'))),
        ],
        [
            'attribute' => 'discount',
            'label'     => '値引き',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format(array_sum(ArrayHelper::getColumn($models, 'discount'))),
        ],
        [
            'attribute' => 'point_consume',
            'label'     => 'ポイント値引き',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format(array_sum(ArrayHelper::getColumn($models, 'point_consume'))),
        ],
        [
            'attribute' => 'point_given',
            'label'     => '付与ポイント',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format(array_sum(ArrayHelper::getColumn($models, 'point_given'))),
        ],
        [
            'attribute' => 'net_sales',
            'label'     => '純売上（税込）',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format(
                      array_sum(ArrayHelper::getColumn($models, 'net_sales')))
        ],
        [
            'attribute' => '',
            'label'     => '送料・手数料',
            'format'    => 'currency',
            'value'     => function($data)
            {
                return $data->postage + $data->handling;
            },
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format(
                      array_sum(ArrayHelper::getColumn($models, 'postage')) + array_sum(ArrayHelper::getColumn($models, 'handling')))
        ],
        [
            'attribute' => 'quantity',
            'label'     => '数量',
            'format'    => 'integer',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => number_format(array_sum(ArrayHelper::getColumn($models, 'quantity'))),
        ],
    ],
    'showFooter' => true,
    'footerRowOptions' => ['class'=>'text-right','style'=>'font-weight:bold'],
]) ?>
  </div>
</div>

