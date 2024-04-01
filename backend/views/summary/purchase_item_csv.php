<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/purchase-item-csv.php $
 * $Id: purchase-item-.php 3080 2020-02-12: 23:21:17Z kawai $
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;

$this->params['breadcrumbs'][] = ['label' => '売上明細CSV', 'url' => ['purchase-item-csv']];


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
        <h2>売上明細CSV</h2>

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


        <div class="col-md-4 pull-right">
            <?= Html::a('CSV出力',
                ['export-csv', 'start_date' => $model->start_date, 'end_date' => $model->end_date],
                [
                 'class' => 'btn btn-default pull-right',
                 'style' => 'margin-right:1em; width:40%;',
                 'title' => '指定した期間の売上集計データをCSVに出力します',
            ]) ?>
        </div>
    </div>
</div>
<?php $form->end() ?>


