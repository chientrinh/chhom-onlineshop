<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/stat.php $
 * $Id: stat.php 3080 2016-11-13 06:21:17Z mori $
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;

$this->params['breadcrumbs'][] = ['label' => '日次集計', 'url' => ['daily']];

$models = $model->itemProvider->getModels();
$header_models = $model->headerItemProvider->getModels();
$return_models = $model->returnItemProvider->getModels();
$minus_models = $model->minusItemProvider->getModels();
$point_models = $model->pointProvider->getModels();

foreach ($models as $key => $value) {
    if ($return_models) {
        foreach ($return_models as $return) {
            if (checkEqualValue($value, $return)) {
                $models[$key]['returnCharge'] = $return['basePrice'];
                $models[$key]['quantity'] -= $return['quantity'];
            }
        }
    }
    if ($minus_models) {
        foreach ($minus_models as $minus) {
            if (checkEqualValue($value, $minus)) {
                $models[$key]['returnCharge'] = $minus['basePrice'];
                $models[$key]['quantity'] -= $minus['quantity'];
            }
        }
    }
}

foreach ($header_models as $header) {
    $check = false;
    foreach ($models as $key => $value2) {
        if ($value2['company_id'] === $header['summary_company_id'] && $value2['branch_id'] === $header['branch_id'] && !$value2['category_id']) {
            $models[$key]['discount'] = $header['discount'];
            $models[$key]['point_consume'] = $header['point_consume'];
            $models[$key]['postage'] = $header['postage'];
            $models[$key]['handling'] = $header['handling'];
            $check = true;
            break;
        }
    }
    if (!$check) {
            if (($header['discount'] + $header['point_consume'] + $header['postage'] + $header['handling']) > 0){
                $models[] = [
                    'company_id'     => $header['summary_company_id'],
                    'branch_id'      => $header['branch_id'],
                    'category_id'    => null,
                    'discount'       => $header['discount'],
                    'point_consume'  => $header['point_consume'],
                    'basePrice'      => 0,
                    'discountTotal'  => 0,
                    'taxTotal'       => 0,
                    'returnCharge'   => 0,
                    'postage'        => $header['postage'],
                    'handling'       => $header['handling'],
                    'point_given'    => 0,
                    'quantity'       => 0
                ];
            }
    }
}

foreach ($point_models as $point_model) {
    array_push($models, array(
        'company_id'     => $point_model['company_id'],
        'branch_id'      => 99,
        'category_id'    => null,
        'discount'       => 0,
        'point_consume'  => $point_model['point_consume'],
        'basePrice'      => 0,
        'discountTotal'  => 0,
        'taxTotal'       => 0,
        'returnCharge'   => 0,
        'postage'        => 0,
        'handling'       => 0,
        'point_given'    => $point_model['point_given'],
        'quantity'       => 0
    ));
}

foreach ($models as $key => $value) {
    $sort_company[$key] = $value['company_id'];
    $sort_branch[$key] = $value['branch_id'];
    // 「その他」は一旦カテゴリID最大にする
    $sort_category[$key] = ($value['category_id']) ? $value['category_id'] : 100;
}
if ($models)
    array_multisort($sort_company, $sort_branch, $sort_category, $models);
$summary = [
    'company_id'    => null,
    'branch_id'     => null,
    'category'      => '合計',
    'basePrice'     => array_sum(ArrayHelper::getColumn($models, 'basePrice')),
    'returnCharge'  => array_sum(ArrayHelper::getColumn($models, 'returnCharge')),
    'taxTotal'      => array_sum(ArrayHelper::getColumn($models, 'taxTotal')),
    'discountTotal' => array_sum(ArrayHelper::getColumn($models, 'discountTotal')),
    'pointTotal'    => array_sum(ArrayHelper::getColumn($models, 'pointTotal')),
    'quantity'      => array_sum(ArrayHelper::getColumn($models, 'quantity')),
    'discount'      => array_sum(ArrayHelper::getColumn($models, 'discount')),
    'point_consume' => array_sum(ArrayHelper::getColumn($models, 'point_consume')),
    'point_given'  => array_sum(ArrayHelper::getColumn($models, 'point_given')),
    'postage'       => array_sum(ArrayHelper::getColumn($models, 'postage')) + array_sum(ArrayHelper::getColumn($models, 'handling')),
];

array_unshift($models, $summary);

$provider = new ArrayDataProvider([
    'allModels' => $models,
    'pagination' => [
        'pageSize' => 0,
    ],
]);

/**
 * 配列マージのためのチェック関数
 * company_id, branch_id, category_idが同じであれば返品商品、値引き商品を加算させる
 * @param type $model
 * @param type $value
 * @return boolean
 */
function checkEqualValue ($model, $value) {
    if ($model['company_id'] === $value['company_id'] && $model['branch_id'] === $value['branch_id'] && $model['category_id'] === $value['category_id']) {
        return true;
    }
    return false;
}

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
        <h2>日次集計</h2>

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

        <?= $form->field($model, 'start_date')->TextInput([
            'name' => 'start_date',
            'filter' => \yii\jui\DatePicker::widget([
                'model'      => $model,
                'attribute'  => 'start_date',
                'language'   => 'ja',
                'dateFormat' => 'yyyy-MM-dd 00:00:00',
                'options'    => ['class'=>'form-control col-md-6'],
            ])
        ])?>

        <?= $form->field($model, 'end_date')->TextInput([
            'name' => 'end_date',
            'filter' => \yii\jui\DatePicker::widget([
                'model'      => $model,
                'attribute'  => 'end_date',
                'language'   => 'ja',
                'dateFormat' => 'yyyy-MM-dd 23:59:59',
                'options'    => ['class'=>'form-control col-md-6'],
            ])
        ])?>

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

        <div id="tab" class="btn-group pull-left" data-toggle="buttons" style="margin-bottom:15px;">
            <p>拠点</p>
            <?php
                $branch_id = (is_null(Yii::$app->request->get('branch'))) ? '99' : Yii::$app->request->get('branch');
                $branch_list = [99 => 'すべて'] + ArrayHelper::map(\common\models\Branch::find()->each(), 'branch_id', 'name');
            ?>
            <?= Html::dropDownList('branch', Yii::$app->request->get('branch'), $branch_list, ['class' => 'form-control']) ?>
        </div>

        <div class="col-md-4 pull-right">
            <?= Html::submitInput('集計',['class' => 'form-control btn btn-info pull-right', 'style' => 'width:50%;']) ?>
            <?= Html::a('集計CSV出力',
                ['print-stat', 'start_date' => $model->start_date, 'end_date' => $model->end_date, 'company' => $model->company_id, 'branch' => $model->branch_id],
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
    'rowOptions' => function($model) {
        if(isset($model['category']))
            return ['class'=>'text-right','style'=>'font-weight:bold'];
    },
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

                if ($data['branch_id'] == 99)
                    return 'その他';

                return null;
            },
        ],
        [
            'attribute' => 'category',
            'label'     => '売上品種',
            'format'    => 'html',
            'value'     => function($data)
            {
                if (isset($data['category'])) {
                    return $data['category'];
                }
                $category_id = ArrayHelper::getValue($data, 'category_id');
                $company_id = ArrayHelper::getValue($data, 'company_id');

                // 生野菜、酒類は別カテゴリ化
                if ($category_id == '99') {
                    $summary_category = \common\models\SummaryCategory::find()->where(['summary_category_id' => $company_id . '97'])->one();
                } else if ($category_id == '98') {
                    $summary_category = \common\models\SummaryCategory::find()->where(['summary_category_id' => $company_id . $category_id])->one();
                } else if (common\models\Category::findOne($category_id)) {
                    $summary_category = \common\models\SummaryCategory::find()->where(['category_id' => $category_id])->one();
                }  else {
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
            'attribute' => 'basePrice',
            'label'     => '商品計',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format(array_sum(ArrayHelper::getColumn($model->itemProvider->models, 'basePrice'))),
        ],
        [
            'attribute' => 'returnCharge',
            'label'     => '返品計',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format($summary['returnCharge']),
        ],
        [
            'attribute' => 'discountTotal',
            'label'     => '値引計',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format(array_sum(ArrayHelper::getColumn($model->itemProvider->models, 'discountTotal'))),
        ],
        [
            'attribute' => 'totalCharge',
            'label'     => '売上合計',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'value'     => function($data)
            {
                return ArrayHelper::getValue($data, 'basePrice')
                     - ArrayHelper::getValue($data, 'discountTotal')
                     - ArrayHelper::getValue($data, 'returnCharge');
            },
            'footer'    => '￥'.number_format(
                    array_sum(ArrayHelper::getColumn($model->itemProvider->models, 'basePrice'))
                  - array_sum(ArrayHelper::getColumn($model->itemProvider->models, 'discountTotal'))
                  - $summary['returnCharge'])
        ],
        [
            'attribute' => 'taxTotal',
            'label'     => '消費税',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format(array_sum(ArrayHelper::getColumn($model->itemProvider->models, 'taxTotal'))),
        ],
        [
            'attribute' => 'discount',
            'label'     => '値引き',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format($summary['discount']),
        ],
        [
            'attribute' => 'point_consume',
            'label'     => 'ポイント値引き',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format($summary['point_consume']),
        ],
        [
            'attribute' => 'point_given',
            'label'     => '付与ポイント',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format($summary['point_given']),
        ],
        [
            'attribute' => '',
            'label'     => '純売上（税込）',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'value'     => function ($data)
            {
                $sum = ArrayHelper::getValue($data, 'basePrice')
                    - ArrayHelper::getValue($data, 'returnCharge')
                    - ArrayHelper::getValue($data, 'discountTotal')
                    + ArrayHelper::getValue($data, 'taxTotal')
                    - ArrayHelper::getValue($data, 'discount')
                    - ArrayHelper::getValue($data, 'point_consume');
                return $sum;
            },
            'footer'    => '￥'.number_format(
                      array_sum(ArrayHelper::getColumn($model->itemProvider->models, 'basePrice'))
                    - array_sum(ArrayHelper::getColumn($model->itemProvider->models, 'discountTotal'))
                    - $summary['returnCharge']
                    + array_sum(ArrayHelper::getColumn($model->itemProvider->models, 'taxTotal'))
                    - $summary['discount']
                    - $summary['point_consume'])
        ],
        [
            'attribute' => '',
            'label'     => '送料・手数料',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'value'     => function ($data)
            {
                return ArrayHelper::getValue($data, 'postage') + ArrayHelper::getValue($data, 'handling');
            },
            'footer'    => '￥'.number_format($summary['postage'])
        ],
        [
            'attribute' => 'quantity',
            'label'     => '数量',
            'format'    => 'integer',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => number_format(array_sum(ArrayHelper::getColumn($model->itemProvider->models, 'quantity'))),
        ],
    ],
    'showFooter' => true,
    'footerRowOptions' => ['class'=>'text-right','style'=>'font-weight:bold'],
]) ?>
  </div>
</div>

