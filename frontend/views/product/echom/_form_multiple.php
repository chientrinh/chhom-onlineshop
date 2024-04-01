<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/product/_form.php $
 * $Id: _form.php 2901 2016-09-30 04:11:33Z mori $
 */

use \yii\helpers\ArrayHelper;
use \yii\helpers\Html;
use \yii\helpers\Url;

use \common\models\Subcategory;
use \common\models\LiveInfo;
use \common\models\LiveItemInfo;


/**
 * 試作段階
 * バックエンドに登録した「追加情報」を取得してフォームを構成する
 * 
 */
$liveItemInfo = LiveItemInfo::find()->where(['product_id'=>$model->product_id])->one();
$liveInfo = $liveItemInfo ? $liveItemInfo->info : null;

$places = $liveInfo ? explode(',',$liveInfo->place) : [];
$school = ['0' => '会場を選択してください'];
foreach($places as $place) {
  $school[$place] = $place;
}

if($model->product_id != 2609) {
  $school['99'] = '自宅受講（オンライン）';
}

if($liveInfo) {
  $jscode = "
  var submit =　'';
  var school_0 = '';
  var school_1 = '';
  var school_2 = '';

  window.onload = function () {
    submit = $('button[type=\"submit\"]')[0];
    school_0 = $('[name=school_0]').val();
    school_1 = $('[name=school_1]').val();
    school_2 = $('[name=school_2]').val();
    
    if('0' == school_0 || '0' == school_1 || '0' == school_2) {
      submit.disabled = true;
      $('#error_place')[0].hidden = false;
    } else {
      submit.disabled = false;
      $('#error_place')[0].hidden = true;
    }
  };

  $('[name^=school]').change(function()
  {
    school_0 = $('[name=school_0]').val();
    school_1 = $('[name=school_1]').val();
    school_2 = $('[name=school_2]').val();

    if('0' == school_0 || '0' == school_1 || '0' == school_2) {
      submit.disabled = true;
      $('#error_place')[0].hidden = false;
    } else {
      submit.disabled = false;
      $('#error_place')[0].hidden = true;
    }

    var online_coupon_enable = $('[name=online_coupon_enable]').val();
    if(online_coupon_enable == 0 && ('99' == school_0 || '99' == school_1 || '99' == school_2)) {
      $('#coupon').val('');
      $('#coupon').attr('disabled',true);
      $('#coupon').attr('placeholder','自宅受講時はご利用いただけません');

    } else {
      $('#coupon').attr('placeholder','コードをお持ちの場合は入力してください');
      $('#coupon').attr('disabled',false);
    }

    var online_option_enable = $('[name=online_option_enable]').val();
    var radios = document.getElementsByName('option');
    if($('#option_description')[0] != undefined && online_option_enable == 0 && ('99' == school_0 || '99' == school_1 || '99' == school_2)) {
      $('#option_description')[0].innerHTML = '※自宅受講時はご利用いただけません';
      $('#option').attr('disabled',true);
      
      for (var i = 0, length = radios.length; i < length; i++) {
        
        if (radios[0].checked) {
          // do whatever you want with the checked radio
          radios[1].checked = true;
        }
        radios[i].disabled = true;

      }
    } else {
      if($('#option') != undefined) {
        $('#option').attr('disabled',false);
        for (var i = 0, length = radios.length; i < length; i++) {
          radios[i].disabled = false;
        }
        if($('#option_description')[0] != undefined) {
          $('#option_description')[0].innerHTML = '※".$liveInfo->option_description."';
        }
      }
    }
  });
  ";

  $this->registerJs($jscode);
}
// var_dump($school);
// exit;

$extra_form = "";

if($liveInfo) {
    $extra_form .= Html::hiddenInput('info_id', $liveInfo->info_id);
    $extra_form .= Html::hiddenInput('online_coupon_enable', $liveInfo->online_coupon_enable);
    $extra_form .= Html::hiddenInput('online_option_enable', $liveInfo->online_option_enable);
    // 一括購入用処理 2021/05/13 #435 TODO: 一括購入判定用のフラグ。でも３つを一つずつ入れても値引きしてあげて良い気が・・
    $extra_form .= Html::hiddenInput('lump_sum', 1);
    if($liveInfo->place) {
      $extra_form .=  Html::label('6/3（木）会場の選択', 'school_0',[
        'style'   => 'width:25%']).Html::dropDownList('school_0', null, $school,[
        'class'   => 'form-control',
        'style'   => 'width:75%; display:inline',])."<br>";
      $extra_form .=  Html::label('6/10（木）会場の選択', 'school_1',[
        'style'   => 'width:25%']).Html::dropDownList('school_1', null, $school,[
        'class'   => 'form-control',
        'style'   => 'width:75%; display:inline',])."<br>";
      $extra_form .=  Html::label('6/24（木）会場の選択', 'school_2',[
        'style'   => 'width:25%']).Html::dropDownList('school_2', null, $school,[
        'class'   => 'form-control',
        'style'   => 'width:75%; display:inline',])."<br>";
      
    }
    if($liveInfo->option_name) {
      $extra_form .=  Html::label($liveInfo->option_name, 'option',[
        'style'   => 'width:25%']).
        Html::radioList('option', 2, [1 => '申し込む', 2 => '申し込まない'],[
        'id'      => 'option',
        'class'   => 'form-control input-m radio',
        'style'   => 'width:75%; display:inline;',
      ])."<br><p id='option_description' style='margin-vertical:10px; display:inline;'>※".$liveInfo->option_description ."</p><br>";
    }
    if($liveInfo->coupon_name) {
      $extra_form .=  Html::label($liveInfo->coupon_name, 'coupon',[
        'style'   => 'width:25%']).Html::textInput('coupon', null, [
        'id'      => 'coupon',
        'class'   => 'form-control',
        'placeholder' => 'コードをお持ちの場合は入力してください',
        'style'   => 'width:75%; display:inline',
      ]) ."<br><br>";
    }
}

$qtyOption = [];

for($i = 1; $i <= $model->upper_limit; $i++)
{
    if(! isset($stockQty) || ($i <= $stockQty))
        $qtyOption[$i] = Html::tag('option', $i, ['value'=>$i]);
}
// 野菜セット購入時メルマガ特典リンクを表示させるための設定(product_id=234・・・季節の野菜セット)
$magazine_flg = ($model->product_id == 234 && Yii::$app->request->get('magazine'));
?>
<script>
    function postProduct() {
        // ストレージデータhiddenタグを作成
        $("<input>", {
            type: 'hidden',
            name: 'sp_product',
            value: '1'
        }).appendTo('form');
        $('form').submit();
    }
</script>
<form action="<?= Url::toRoute(['/cart/default/add']); ?>">

<?= Html::hiddenInput('pid', $model->product_id) ?>
<?= $extra_form ?>

<p class="Cart">数量： <select name="qty">
    <?= implode("\n", $qtyOption) ?>
</select>&nbsp;個&nbsp;&nbsp;


<?= Html::submitButton("カートに入れる", ['class'=>'btn btn-warning']) ?>
<span id="error_place" class="text-danger" hidden>※会場を選択してください</span>

<?php if ($magazine_flg): ?>
    <?= Html::submitButton("カートに入れて特典商品を見る", ['class'=>'btn btn-danger', 'onclick' => 'return postProduct();']) ?>
<?php endif; ?>
</p>
</form>

