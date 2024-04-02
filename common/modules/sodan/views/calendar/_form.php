<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\sodan\Client;
use common\models\sodan\Homoeopath;
use yii\helpers\Url;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/interview/_form.php $
 * @version $Id: _form.php 2591 2016-06-18 02:19:10Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  common\models\sodan\Room
 */

$ajaxUrl = Url::to(['interview/fetch-products']);
$ajaxUrl2 = Url::to(['interview/fetch-homoeopath']);
$ajaxUrl3 = Url::to(['interview/fetch-ticket']);
$jscode = "

$('select[name=\'Interview[client_id]\'], #interview-itv_date').change(function(){
console.log('fetch_product on change');
  fetch_product();
});

function fetch_product() {

console.log('fetch_product called');
  var client_id = $('select[name=\'Interview[client_id]\']').val();
  var itv_date = $('#interview-itv_date').val();
  var request_data = {client_id : client_id};
  if(itv_date) { 
      request_data['itv_date'] = itv_date;
  }
  $.ajax({
    type: 'POST',
    url: '{$ajaxUrl}',
    data: request_data
  }).done(function(result) {
    var products = JSON.parse(result);
    // 相談種別リストを作成し直す
    $('select[name=\'Interview[product_id]\']').empty();
    $('select[name=\'Interview[product_id]\']').append($('<option>').text('').val(''));
    for (var i in products) {
      if (!products[i]) {
        continue;
      }
      var plist = $('<option>').text(products[i]).val(i);
      $('select[name=\'Interview[product_id]\']').append(plist);
    }
  }).fail(function(result) {
    alert('データ取得に失敗しました');
  });

$.ajax({
    type: 'POST',
    url: '{$ajaxUrl3}',
    data: {
      client_id : client_id
    }
  }).done(function(result) {
    var tickets = JSON.parse(result);
    // 相談種別リストを作成し直す
    $('select[name=\'Interview[ticket_id]\']').empty();
    $('select[name=\'Interview[ticket_id]\']').append($('<option>').text('').val(''));
    for (var i in tickets) {
      if (!tickets[i]) {
        continue;
      }
      var plist = $('<option>').text(tickets[i]).val(i);
      $('select[name=\'Interview[ticket_id]\']').append(plist);
    }
  }).fail(function(result) {
    alert('データ取得に失敗しました');
  });
}

branch_filter($('select[name=\'Interview[branch_id]\']').val());
$('select[name=\'Interview[branch_id]\']').change(function(){
  branch_filter($(this).val());
});

// フィルタ設定
var homoeopathArray2 = new Array();
var clientArray2 = new Array();
var productArray2 = new Array();

$('select[name=\'Interview[homoeopath_id]\']').children().each(function(){
   homoeopathArray2.push( { value:$(this).val(), body:$(this).html() });
});

$('select[name=\'Interview[client_id]\']').children().each(function(){
   clientArray2.push( { value:$(this).val(), body:$(this).html() });
});

$('select[name=\'Interview[product_id]\']').children().each(function(){
   productArray2.push( { value:$(this).val(), body:$(this).html() });
});

$('#homoeopath-filter2').keyup(function(){
    option_filter2($(this).val(), homoeopathArray2, 'homoeopath_id');
});

$('#client-filter2').keyup(function(){
    option_filter2($(this).val(), clientArray2, 'client_id');
});

$('#product-filter2').keyup(function(){
    option_filter2($(this).val(), productArray2, 'product_id');
});
";
$this->registerJs($jscode);

$csscode = "
#ui-datepicker-div {
    z-index: 9999 !important;
}
";
$this->registerCss($csscode);

echo <<<EOF
<script>
function branch_filter(branch_id) {
  if (!branch_id) {
    return false;
  }
  $.ajax({
    type: 'POST',
    url: '{$ajaxUrl2}',
    data: {
      branch_id : branch_id
    }
  }).done(function(result) {
    var homoeopaths = JSON.parse(result);

    // 相談種別リストを作成し直す
    $('select[name=\'Interview[homoeopath_id]\']').empty();
    $('select[name=\'Interview[homoeopath_id]\']').append($('<option>').text('').val(''));
    for (var i in homoeopaths) {
      if (!homoeopaths[i]) {
        continue;
      }
      plist = $('<option>').text(homoeopaths[i]).val(i);
      $('select[name=\'Interview[homoeopath_id]\']').append(plist);
    }
    $('select[name=\'Interview[homoeopath_id]\']').val({$model->homoeopath_id});
  }).fail(function(result) {
    alert('データ取得に失敗しました');
  });
}
function option_filter2 (s, options, attr) {
    $('select[name=\'Interview[' + attr + ']\']').empty();
    if (s == ''){
       $(options).each(function(i, o){
          $('select[name=\'Interview[' + attr + ']\']').append( $('<option>').val(o.value).text(o.body));
       });
    } else {
       $('select[name=\'Interview[' + attr + ']\']').append( $('<option>').val('').text(''));
       options.filter(function(o, i){
          if (o.body.toLowerCase().indexOf(s.toLowerCase()) != -1){
             $('select[name=\'Interview[' + attr + ']\']').append( $('<option>').val(o.value).text(o.body));
          }
       });
    }
}
</script>
EOF;

$query = ($branch_id) ? Homoeopath::find()->active()->multibranch($branch_id) : Homoeopath::find()->active();
$hpath = ArrayHelper::merge([''], ArrayHelper::map($query->all(),'homoeopath_id','customer.homoeopathname'));
asort($hpath);

$branch = \common\models\Branch::find()->center()->all();
$branch = ArrayHelper::map($branch,'branch_id','name');

$query = \common\models\sodan\InterviewStatus::find();
if('app-frontend' == Yii::$app->id)
    $query->andWhere(['or',
                      ['<=','status_id',\common\models\sodan\InterviewStatus::PKEY_DONE],
                      ['status_id' => \common\models\sodan\InterviewStatus::PKEY_VOID],
    ]);

$status   = $query->all();
$status   = ArrayHelper::map($status,'status_id','name');

$query = \common\models\Product::find()->sodanProduct()->orderBy(['kana' => SORT_ASC]);
if($model->client)
{
    $client = Client::findOne($model->client_id);
    if($client->isAnimal())
        $query->andWhere(['not like','dtb_product.name','小人'])
              ->andWhere(['not like','dtb_product.name','大人']);
    elseif($client->getInterviews()->active()->exists()) {
        $query->andWhere(['not like','dtb_product.name','動物']);
        // 相談会を受けたことがある、または予約中である
        $age = $model->client->getAge($itv_date);
        if(null === $age) { }
        elseif(13 <= $age) { $query->andWhere(['not like','dtb_product.name','小人']); }
        elseif($age < 13) { $query->andWhere(['not like','dtb_product.name','大人']); }
    }
}
$products = ArrayHelper::merge(['' => ''], ArrayHelper::map($query->all(),'product_id','name'));

$clients[''] = '';
$client_list = Client::find()->active()->asArray()->all();
if ($client_list) {
    foreach ($client_list as $client) {
        $clients[$client['client_id']]  = (!$client['customer']['name01'] && !$client['customer']['name02']) ? "{$client['customer']['kana01']} {$client['customer']['kana02']}" : "{$client['customer']['name01']} {$client['customer']['name02']}";
        $clients[$client['client_id']] .= (!$client['ng_flg']) ? '（公開OK）' : '（公開NG）';
    }
}
asort($clients);

$tickets = ['' => ''];
if ($model->client_id) {
    $results = common\models\DiscountProductLog::find()->active()->andWhere(['used_flg' => 0])->all();
    if ($results) {
        foreach ($results as $result) {
            $tickets[$result->ticket_id] = sprintf('%05d', $result->ticket_id) . ':' . $result->discountProduct->product->name . "(有効期限：" . date('Y/m/d', strtotime($result->expiredate)) . ")";
        }
    }
}
?>

<div class="interview-form">

    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'enableClientScript' => false,
        'fieldConfig' => [
        ],
    ]); ?>

    <div class="row" style="margin: 10px;">
    <div class="col-md-6">
    <div class="row">
    <?= $form->field($model, 'branch_id')->dropDownList($branch) ?>

    <div class="row">
        <div class="col-md-4">
        <?= $form->field($model, 'itv_date')
         ->widget(\yii\jui\DatePicker::className(),
                  [
                      'language' => Yii::$app->language,
                      'clientOptions' =>[
                          'dateFormat'    => 'yy-m-d',
                          'language'      => Yii::$app->language,
                          'country'       => 'JP',
                          'showAnim'      => 'fold',
                          'yearRange'     => 'c-5:c+5',
                          'changeMonth'   => true,
                          'changeYear'    => true,
                          'autoSize'      => true,
                          'showOn'        => "button",
                          'htmlOptions'=>[
                              'style'=>'width:80px;',
                              'font-weight'=>'x-small',
                          ],],
                      'options' => ['class' => 'form-control'],
                  ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'itv_time')
            ->widget(\kartik\time\TimePicker::className(),[
                'pluginOptions' => [
                    'defaultTime'  => '09:30',
                    'showMeridian' => false,
                    'showSeconds'  => false,
                    'minuteStep'   => 5,
                ]
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'duration')->textInput(['maxlength' => 8]) ?>
        </div>
    </div>
        <?= $form->field($model, 'note')->textarea(['rows' => 8]) ?>
    </div>
    </div>

    <div class="col-md-6 col-xs-4">
        <?php if(Yii::$app->get('user') && Yii::$app->user->identity instanceof \backend\models\Staff): /* when Staff logged in */?>
            <!-- hpath_id -->
            <div class="col-md-5">
                <?= Html::tag('label',$model->getAttributeLabel('homoeopath_id')) ?>
            </div>
            <div class="col-md-5">
                <?= Html::textInput('homoeopath-filter2', '', [
                    'class' => 'form-control',
                    'id' => 'homoeopath-filter2',
                    'autocomplete' => 'off',
                    'style' => 'margin:5px 50%;'
                ]) ?>
            </div>
            <?= $form->field($model, 'homoeopath_id')->dropDownList($hpath)->label(false) ?>

            <!-- client_id -->
            <div class="col-md-5">
                <?= Html::tag('label',$model->getAttributeLabel('client_id')) ?>
            </div>
            <div class="col-md-5">
                <?= Html::textInput('client-filter2', '', [
                    'class' => 'form-control',
                    'id' => 'client-filter2',
                    'autocomplete' => 'off',
                    'style' => 'margin:5px 50%;'
                ]) ?>
            </div>
            <?= $form->field($model, 'client_id')->dropDownList($clients)->label(false) ?>

            <!-- product_id -->
            <div class="col-md-5">
                <?= Html::tag('label',$model->getAttributeLabel('product_id')) ?>
            </div>
            <div class="col-md-5">
                <?= Html::textInput('product-filter2', '', [
                    'class' => 'form-control',
                    'id' => 'product-filter2',
                    'autocomplete' => 'off',
                    'style' => 'margin:5px 50%;'
                ]) ?>
            </div>
            <?= $form->field($model, 'product_id')->dropDownList($products)->label(false) ?>

        <?php else: /* when ホメオパス logged in */?>
            <!-- hpath_id -->
            <?= Html::tag('label',$model->getAttributeLabel('homoeopath_id')) ?>
            <?= Html::tag('p',$model->homoeopath->name, ['class'=>'col-md-offset-1']) ?>

            <!-- client_id -->
            <?= Html::tag('label',$model->getAttributeLabel('client_id')) ?>
            <?= Html::tag('p',($client = $model->client) ? $client->name : '(未指定)', ['class'=>'col-md-offset-1']) ?>

            <!-- product_id -->
            <?= Html::tag('label',$model->getAttributeLabel('product_id')) ?>
            <?= Html::tag('p',($product = $model->product) ? $product->name : '(未指定)', ['class'=>'col-md-offset-1']) ?>
        <?php endif ?>

        <?= $form->field($model, 'status_id')->dropDownList($status,['class'=> 'form-control']) ?>

        <?= $form->field($model, 'ticket_id')->dropDownList($tickets, ['class' => 'form-control']) ?>
        <?= $form->field($model, 'itv_id')->hiddenInput()->label(false) ?>
    </div>

    <?php if (Yii::$app->id === 'app-backend'):?>
        <div class="col-md-12 col-xs-8">
            <?= $form->field($model, 'officer_use')->textArea() ?>
        </div>
    <?php endif;?>

    <div class="col-md-12 col-xs-8">

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
        <a href="" class="modalClose btn btn-danger">閉じる</a>
    </div>
    </div>

    </div>

    <?php $form->end(); ?>
</div>
