﻿<?php
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
 * バックエンドに登録した「追加情報」を取得してフォームを構成する
 * 
 */
$liveInfo = $liveItemInfo ? $liveItemInfo->info : null;
$user = Yii::$app->user->identity;
$event_info = [];
$companion = [];
$entry_data = [];
$extra_form = "";
if($liveInfo && isset($liveInfo->companion)) {
  // $companion = array_merge(['0' => '追加する同行者を選択してください'], explode(',',$liveInfo->companion));
  $companion = explode(',',$liveInfo->companion);
  $event_capacity = $liveInfo->capacity;
  $event_adult_price1 = $liveInfo->adult_price1;
  $event_adult_price2 = $liveInfo->adult_price2;
  $event_adult_price3 = $liveInfo->adult_price3;
  $event_child_price1 = $liveInfo->child_price1;
  $event_child_price2 = $liveInfo->child_price2;
  $event_child_price3 = $liveInfo->child_price3;
  $event_infant_price1 = $liveInfo->infant_price1;
  $event_infant_price2 = $liveInfo->infant_price2;
  $event_infant_price3 = $liveInfo->infant_price3;
  $event_subscription = $liveInfo->subscription;
  $event_info["capacity"] = $event_capacity;
  $event_info["adult_price1"] = $event_adult_price1;
  $event_info["adult_price2"] = $event_adult_price2;
  $event_info["adult_price3"] = $event_adult_price3;
  $event_info["child_price1"] = $event_child_price1;
  $event_info["child_price2"] = $event_child_price2;
  $event_info["child_price3"] = $event_child_price3;
  $event_info["infant_price1"] = $event_infant_price1;
  $event_info["infant_price2"] = $event_infant_price2;
  $event_info["infant_price3"] = $event_infant_price3;
  $event_info["subscription"] = $event_subscription;
  // var_dump($event_info,$companion);
  $entry_self_data = $user ? ["type" => "本人", "total" => $event_adult_price1, "price" => $event_adult_price2, "name" => $user->name01." ".$user->name02, "age" => ($user->birth ? floor((date("Ymd") - str_replace("-", "", $user->birth))/10000) : null),"tax" => $event_adult_price3] : ["type" => "本人", "total" => $event_adult_price1, "price" => $event_adult_price2, "name" => "", "age" => null,"tax" => $event_adult_price3];
// var_dump($entry_self_data);
  $entry_data[] = $entry_self_data;




  // 専用のEditbleなTableを作成
  $csscode = '.editable:empty:before {
    content: attr(data-placeholder);
    color: #999;

  }
  .form-control {
    height: 5%;
    font-size:20px;    
  }';
  $this->registerCss($csscode);

  $js_code = '
  const $tableID = $("#table");
  const $BTN = $("#export-btn");
  const $EXPORT = $("#export");
  const $typeSELECT = $("select[name=\"companion\"]");
  const $newTr = `
         <tr>
           <td class="pt-3-half"></td>
           <td class="pt-3-half"></td>
           <td class="pt-3-half editable" contenteditable="true" data-placeholder="氏名を入力して下さい" onKeyPress="return checkEnter(event);"></td>
           <td class="pt-3-half" data-placeholder="年齢を入力して下さい"><select class="form-control" name="age"></select></td>
       <td>
             <span class="table-remove"><button type="button" class="btn btn-danger btn-rounded btn-sm my-0 waves-effect waves-light" style="font-size:20px;">削除</button></span><div hidden></div>
           </td>
         </tr>`;

  $typeSELECT.change(function() {
    $type = $(this).val();
    $type_text = "";
    $total = 0;
    $price = 0;
    $tax = 0;
    $min_age = 0;
    $max_age = 120;
    let option = document.createElement("option");

    if($type == 0) {
      console.log("大人");
      $type_text = "大人";
      $total = '.$event_adult_price1.';
      $price = '.$event_adult_price2.';
      $tax = '.$event_adult_price3.';
      $min_age = 12;

    } else if($type == 1) {
      console.log("小人");
      $type_text = "小人";
      $total = '.$event_child_price1.';
      $price = '.$event_child_price2.';
      $tax = '.$event_child_price3.';
      $min_age = 6;
      $max_age = 12;
    } else if($type == 2) {
      console.log("未就学児");
      $type_text = "未就学児";
      $total = '.$event_infant_price1.';
      $price = '.$event_infant_price2.';
      $tax = '.$event_infant_price3.';
      $min_age = 0;
      $max_age = 6;
    }

    var $clone = $newTr;

    $("tbody").append($newTr);
    $tableID.find("tbody tr").last().find("td").eq(0).html($type_text);
    $tableID.find("tbody tr").last().find("td").eq(1).html($total);

    option = document.createElement("option");
    option.setAttribute("value", "");
    option.selected = true;
    option.innerHTML = "選択";
    $tableID.find("tbody tr").last().find("td").eq(3).find("select").eq(0)[0].appendChild(option);

    for (let i = $min_age; i <= $max_age; i++) {
      let option = document.createElement("option");
      option.setAttribute("value", i);
      option.innerHTML = i + "歳";
      $tableID.find("tbody tr").last().find("td").eq(3).find("select").eq(0)[0].appendChild(option);
    } 

    $tableID.find("tbody tr").last().find("td").eq(4).find("div").eq(0).html($tax);
    $typeSELECT[0].selectedIndex = 0;
  });

  $tableID.on("click", ".table-remove", function () {

    $(this).parents("tr").detach();
  });

  ';
  $this->registerJs($js_code);
}


if($liveItemInfo) {

  $places = $liveInfo ? explode(',',$liveInfo->place) : [];
  
  if($model->product_id != 3308) {
    $school = ['0' => '参加方法を選択してください'];
   } else {
    $school = ['0' => 'ブラウス型紙サイズを選択してください'];
  }
  if($model->product_id != 3386) {
    $school = ['0' => '参加方法を選択してください'];
   } else {
    $school = ['0' => 'ツアーの申込パターンを選択してください'];
  }
  
  foreach($places as $place) {
    $school[$place] = $place;
  }

  if($model->product_id != 4516 && ($liveInfo && !$liveInfo->companion)) {
      $school['99'] = '自宅受講（オンライン）';
  }

  if($liveInfo) {
    $jscode = "
    $('[name=school]').change(function()
    {
      var online_coupon_enable = $('[name=online_coupon_enable]').val();
      if(online_coupon_enable == 0 && 99 == $(this).val()) {
        $('#coupon').val('');
        $('#coupon').attr('disabled',true);
        $('#coupon').attr('placeholder','自宅受講時はご利用いただけません');

      } else {
        $('#coupon').attr('placeholder','コードをお持ちの場合は入力してください');
        $('#coupon').attr('disabled',false);
      }

      var online_option_enable = $('[name=online_option_enable]').val();
      var radios = document.getElementsByName('option');
      if(online_option_enable == 0 && 99 == $(this).val()) {
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
        $('#option').attr('disabled',false);
        for (var i = 0, length = radios.length; i < length; i++) {
          radios[i].disabled = false;
        }
        $('#option_description')[0].innerHTML = '※".$liveInfo->option_description."';
      }
    });
    ";
    $this->registerJs($jscode);
  }


} else {

/*
$colors = $sizes = null;


$q1 = $model->getSubcategories()->andWhere(['parent_id'=>Subcategory::PKEY_TROSE_COLOR]);
$q2 = $model->getSubcategories()
            ->andWhere(['parent_id'=>Subcategory::PKEY_TROSE_SIZE])
            ->orWhere(['parent_id'=>Subcategory::find()
                                               ->where(['parent_id' => Subcategory::PKEY_TROSE_SIZE])
                                               ->column() ]);
if(1 < $q1->count())
    $colors = ArrayHelper::map(array_merge([''=>''], $q1->all()),'subcategory_id','name');

if(1 < $q2->count())
    $sizes = ArrayHelper::map(array_merge([''=>''], $q2->all()), 'subcategory_id','name');
*/
/**
 * お子様連れ、お弁当・ランチ予約のセレクトボックス生成処理
 * いずれ管理画面で制御していけるように・・・
 * お子様連れ　children 0 - 5
 * お弁当予約（6/6）　lunchbox_200606 0 - 5
 * お弁当予約（6/7）　lunchbox_200607 0 - 5
 * ランチ予約（6/6）　lunch_200606 0 - 5
 * ランチ予約（6/7）　lunch_200607 0 - 5
 */
$children = ['0' => '同伴なし', '1' => '１名同伴', '2' => '２名同伴', '3' => '３名同伴', '4' => '４名同伴', '5' => '５名同伴'];
$lunch    = ['0' => '不要', '1' => '１個', '2' => '２個', '3' => '３個', '4' => '４個', '5' => '５個'];

$school   = ['0' => '会場を選択してください', '東京' => '東京校', '札幌' => '札幌校', '名古屋' => '名古屋校', '大阪' => '大阪校'];
}

switch($model->product_id) {
  case 1765:
    $extra_form .=  "お弁当予約（6/6）： ".Html::dropDownList('lunchbox_200606', null, $lunch)."<br>";
    $extra_form .=  "お弁当予約（6/7）： ".Html::dropDownList('lunchbox_200607', null, $lunch)."<br><br>";
    break;
  case 1766:
    $extra_form .=  "お弁当予約（6/7）： ".Html::dropDownList('lunchbox_200607', null, $lunch)."<br>";
    $extra_form .=  Html::hiddenInput('lunchbox_200606', 0)."<br>";
    break;
  case 1767:
    $extra_form .=  "ランチ予約（6/6）： ".Html::dropDownList('lunch_200606', null, $lunch)."<br>";
    $extra_form .=  "ランチ予約（6/7）： ".Html::dropDownList('lunch_200607', null, $lunch)."<br><br>";
    break;
  case 1768:
    $extra_form .=  "ランチ予約（6/7）： ".Html::dropDownList('lunch_200607', null, $lunch)."<br>";
    $extra_form .=  Html::hiddenInput('lunch_200606', 0)."<br>";
    break;
  case 1769:
    $extra_form .=  "ランチ予約（6/6）： ".Html::dropDownList('lunch_200606', null, $lunch)."<br>";
    $extra_form .=  Html::hiddenInput('lunch_200607', 0)."<br><br>";
    break;
  case 1770:
    $extra_form .=  Html::hiddenInput('lunch_200607', 0)."<br>";
    $extra_form .=  Html::hiddenInput('lunch_200606', 0)."<br>";
    break;
  case 1771:
    $extra_form .=  Html::hiddenInput('lunch_200606', 0)."<br>";
    $extra_form .=  Html::hiddenInput('lunch_200607', 0)."<br>";
    break;
  case 1772:
    $extra_form .=  Html::hiddenInput('lunch_200607', 0)."<br>";
    $extra_form .=  Html::hiddenInput('lunch_200606', 0)."<br>";
    break;
  case 1775:
    $extra_form .=  "お弁当予約（6/6）： ".Html::dropDownList('lunchbox_200606', null, $lunch)."<br>";
    $extra_form .=  Html::hiddenInput('lunchbox_200607', 0)."<br>";
    break;
  case 1776:
    $extra_form .=  "ランチ予約（6/6）： ".Html::dropDownList('lunch_200606', null, $lunch)."<br>";
    $extra_form .=  Html::hiddenInput('lunch_200607', 0)."<br>";
    break;
  case 1777:
    $extra_form .=  "ランチ予約（6/6）： ".Html::dropDownList('lunch_200606', null, $lunch)."<br>";
    $extra_form .=  Html::hiddenInput('lunch_200607', 0)."<br>";
    break;
  case 1778:
    $extra_form .=  Html::hiddenInput('lunch_200606', 0)."<br>";
    $extra_form .=  Html::hiddenInput('lunch_200607', 0)."<br>";
    break;
  case 1922:
    $extra_form =  "参加会場： ".Html::dropDownList('school', null, $school)."<br>";
    $extra_form .=  "お子様連れ<br>（小学生以下）： ".Html::dropDownList('children', null, $children)."<br><br>";
    break;
  case 1954:
    $extra_form =  "参加会場： ".Html::dropDownList('school', null, $school)."<br>";
    $extra_form .=  "お子様連れ<br>（小学生以下）： ".Html::dropDownList('children', null, $children)."<br>";
    $extra_form .=  "お弁当・ランチ予約（10/3）： ".Html::dropDownList('lunch_201003', null, $lunch)."<br><br>";
    break;
  case 1955:
    $extra_form =  "参加会場： ".Html::dropDownList('school', null, $school)."<br>";
    $extra_form .=  "お子様連れ<br>（小学生以下）： ".Html::dropDownList('children', null, $children)."<br>";
    $extra_form .=  "お弁当・ランチ予約（10/4）： ".Html::dropDownList('lunch_201004', null, $lunch)."<br><br>";
    break;

  case 1966:
    $extra_form =  "参加会場： ".Html::dropDownList('school1', null, $school1)."<br>";
    $extra_form .=  "お子様連れ<br>（小学生以下）： ".Html::dropDownList('children', null, $children)."<br><br>";
    break;
  case 1967:
    $extra_form =  "参加会場： ".Html::dropDownList('school2', null, $school2)."<br><br>";
    break;

  case 2290:
  case 2291:
//    $school   = ['0' => '会場を選択してください', '東京' => '東京校', '札幌' => '札幌校', '名古屋' => '名古屋校', '大阪' => '大阪校','99' => 'オンライン受講'];    
    $school   = ['0' => '会場を選択してください', '東京' => '東京校', '大阪' => '大阪校','99' => 'オンライン受講'];    
    $extra_form =  "参加会場： ".Html::dropDownList('school', null, $school)."<br>";
    $extra_form .=  Html::hiddenInput('lunch_210227', 0)."<br><br>";
    break;

  case 2342:
  case 2343:
  case 2344:
  case 2345:
  case 2346:
  case 2347:
  case 2348:
    $school   = ['0' => '会場を選択してください', '東京' => '東京', '大阪' => '大阪', '東京＋ワークショップキット' => '東京＋ワークショップキット', '大阪＋ワークショップキット' => '大阪＋ワークショップキット', '東京＋LINEクーポン' => '東京＋LINEクーポン', '大阪＋LINEクーポン' => '大阪＋LINEクーポン', '東京＋ワークショップキット＋LINEクーポン' => '東京＋ワークショップキット＋LINEクーポン', '大阪＋ワークショップキット＋LINEクーポン' => '大阪＋ワークショップキット＋LINEクーポン', '99' => 'オンライン受講'];
//$school   = ['0' => '会場を選択してください', 'T' => '東京', 'O' => '大阪', '東京＋キット' => '東京＋キット', 'OK' => '大阪＋キット', 'TL' => '東京＋LINEクーポン', 'OL' => '大阪＋LINEクーポン', 'TKL' => '東京＋キット＋LINEクーポン', 'OKL' => '大阪＋キット＋LINEクーポン', '99' => 'オンライン受講'];
//    $options = ['style' => ['font-size' => '16px']];
    $extra_form =  "参加会場： ".Html::dropDownList('school', null, $school)."<br>";
//    $extra_form .=  "LINE Coupon： ".Html::input('line_coupon', )."<br><br>";
//    $extra_form .=  "LINE Coupon： ".Html::textField('line_coupon', '')."<br><br>";
//    $extra_form .=  "LINE Coupon： ".Html::textField('line_coupon', '')."<br><br>";
//Html::input('text', 'username', $user->name, ['class' => $username])
////    $extra_form .= "LINEクーポンをお持ちの方： ".Html::input('text', 'line_coupon','',['placeholder'=>'クーポンコードを入力して下さい','size'=>'24'])."<br><br>";
//    $extra_form .=  "LINEクーポンをお持ちの方： ".Html::input('text', 'line_coupon')."<br><br>";
//CHtml::activeTextField($model,'username')
    break;


  default:
    $extra_form = "";
}

if($liveInfo) {
    $extra_form .= Html::hiddenInput('info_id', $liveInfo->info_id);
    $extra_form .= Html::hiddenInput('online_coupon_enable', $liveInfo->online_coupon_enable);
    $extra_form .= Html::hiddenInput('online_option_enable', $liveInfo->online_option_enable);
    if($liveInfo->companion) {
      $extra_form .=  Html::label('同行者を追加 →　', 'companion',[
        'style'   => 'width:25%; font-size: 20px; text-align:right']).Html::dropDownList('companion', "追加する同行者を選択してください", $companion,[
        'prompt'=>'追加する同行者を選択してください',
        'class'   => 'form-control',
        'style'   => 'width:75%; height:5%; font-size: 20px; display:inline',])."<br>";

      $extra_form .= '<table id="table" class="editable table table-bordered table-responsive-md table-striped text-center"><thead style="font-size:20px"><th class="text-center">参加区分</th><th class="text-center">料金</th><th class="text-center">氏名</th><th class="text-center">年齢</th></thead><tbody style="font-size:20px">';
      $extra_form .= \yii\widgets\ListView::begin([
        'dataProvider'  => new \yii\data\ArrayDataProvider([
            'allModels'  => $entry_data,
                'pagination' => false,
            ]),
        'itemView'     => function ($model, $key, $index, $widget){
            if(!Yii::$app->user->identity) {
              return sprintf('<tr class="">
              <td class="pt-3-half">本人</td>
              <td class="pt-3-half">%d</td>
              <td class="pt-3-half editable" contenteditable="true" data-placeholder="氏名を入力して下さい" onKeyPress="return checkEnter(event);"></td>
              <td class="pt-3-half" data-placeholder="年齢を入力して下さい"><select style="font-size:20px" class="form-control" name="age"><option value="" selected>選択</option><option value="12">12歳</option><option value="13">13歳</option><option value="14">14歳</option><option value="15">15歳</option><option value="16">16歳</option><option value="17">17歳</option><option value="18">18歳</option><option value="19">19歳</option><option value="20">20歳</option><option value="21">21歳</option><option value="22">22歳</option><option value="23">23歳</option><option value="24">24歳</option><option value="25">25歳</option><option value="26">26歳</option><option value="27">27歳</option><option value="28">28歳</option><option value="29">29歳</option><option value="30">30歳</option><option value="31">31歳</option><option value="32">32歳</option><option value="33">33歳</option><option value="34">34歳</option><option value="35">35歳</option><option value="36">36歳</option><option value="37">37歳</option><option value="38">38歳</option><option value="39">39歳</option><option value="40">40歳</option><option value="41">41歳</option><option value="42">42歳</option><option value="43">43歳</option><option value="44">44歳</option><option value="45">45歳</option><option value="46">46歳</option><option value="47">47歳</option><option value="48">48歳</option><option value="49">49歳</option><option value="50">50歳</option><option value="51">51歳</option><option value="52">52歳</option><option value="53">53歳</option><option value="54">54歳</option><option value="55">55歳</option><option value="56">56歳</option><option value="57">57歳</option><option value="58">58歳</option><option value="59">59歳</option><option value="60">60歳</option><option value="61">61歳</option><option value="62">62歳</option><option value="63">63歳</option><option value="64">64歳</option><option value="65">65歳</option><option value="66">66歳</option><option value="67">67歳</option><option value="68">68歳</option><option value="69">69歳</option><option value="70">70歳</option><option value="71">71歳</option><option value="72">72歳</option><option value="73">73歳</option><option value="74">74歳</option><option value="75">75歳</option><option value="76">76歳</option><option value="77">77歳</option><option value="78">78歳</option><option value="79">79歳</option><option value="80">80歳</option><option value="81">81歳</option><option value="82">82歳</option><option value="83">83歳</option><option value="84">84歳</option><option value="85">85歳</option><option value="86">86歳</option><option value="87">87歳</option><option value="88">88歳</option><option value="89">89歳</option><option value="90">90歳</option><option value="91">91歳</option><option value="92">92歳</option><option value="93">93歳</option><option value="94">94歳</option><option value="95">95歳</option><option value="96">96歳</option><option value="97">97歳</option><option value="98">98歳</option><option value="99">99歳</option><option value="100">100歳</option><option value="101">101歳</option><option value="102">102歳</option><option value="103">103歳</option><option value="104">104歳</option><option value="105">105歳</option><option value="106">106歳</option><option value="107">107歳</option><option value="108">108歳</option><option value="109">109歳</option><option value="110">110歳</option><option value="111">111歳</option><option value="112">112歳</option><option value="113">113歳</option><option value="114">114歳</option><option value="115">115歳</option><option value="116">116歳</option><option value="117">117歳</option><option value="118">118歳</option><option value="119">119歳</option><option value="120">120歳</option></select></td>
              <td>
                <span class="table-remove"></span><div hidden>%d</div>
              </td>
            </tr>', $model['total'], $model['tax']);
            }

            return sprintf('<tr data-key="%d"><td id="type-%d" class="pt-3-half">%s</td><td id="price-%d" class="pt-3-half">%s</td><td id=name-%d class="pt-3-half">%s</td><td id="age-%d" class="pt-3-half">%s</td><td><div hidden>%d</div>
            <!-- <span class="table-add"><button type="button" class="btn btn-success btn-rounded btn-sm my-0 waves-effect waves-light">
            //     追加
               </button></span> -->
          </td></tr>
          <!--<tr class="">
            <td class="pt-3-half"></td>
            <td class="pt-3-half"></td>
            <td class="pt-3-half editable" contenteditable="true" data-placeholder="氏名を入力して下さい" onKeyPress="return checkEnter(event);"></td>
            <td class="pt-3-half editable" style="font-size:20px" contenteditable="true" data-placeholder="年齢を入力して下さい"  onKeyPress="return checkEnter(event);"></td>
          <td>
              <span class="table-remove"><button type="button" style="font-size:20px" class="btn btn-danger btn-rounded btn-sm my-0 waves-effect waves-light">削除</button></span>
            </td>
          </tr>-->', $key, $key, preg_replace('/\(.*\)/','<span class="mini">${0}</span>',$model['type']),$key, $model['total'],$key, $model['name'], $key, $model['age'], $model['tax']);
          }
          // return sprintf('<p><strong>%s</strong><br>%s</p><hr>', $model->type, $model->fee); },
    ])->renderItems();
    $extra_form .= '</tbody></table>';
    
    

    } else {

    if($liveInfo->place) {
      $extra_form .=  Html::label('選択', 'school',[
        'style'   => 'width:25%']).Html::dropDownList('school', null, $school,[
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

    function addParam() {
      $params = [];
      $trs = $("#table").find("tbody tr");
      for( $i=0,$l=$trs.length;$i<$l;$i++ ){
        $cells = $trs.eq($i).children();
        for( $j=0,$m=$cells.length;$j<$m;$j++ ){
          if( typeof $params[$i] == "undefined" ) {
            $params[$i] = [];
          }

          $text = $cells.eq($j).text();
          if($j == 2 && $text == "") {
            alert("氏名を必ず入力して下さい");
            return false;
          }

          if($j == 4) {
            $text =  $cells.eq($j).find("div").text();
          }

          if($j == 3) {
            if($cells.eq(0).text() == "本人") {
              if($cells.eq($j).find("select").eq(0).val() != undefined) {
                $text =  $cells.eq($j).find("select").eq(0).val();
                if($text == "") {
                  alert("年齢が未選択です");
                  return false;              
                }
                
              } else {
                $text = $cells.eq($j).text();
              }
            } else {
              $text =  $cells.eq($j).find("select").eq(0).val();
              if($text == "") {
                alert("年齢が未選択です");
                return false;              
              }
            }
            $text = $text+"歳";
          }
          $params[$i][$j] = $text;
        }
      }
      
      // 追加で送信するパラメータ
      var newValue = document.createElement("input");
      // 画面に表示されてしまうので、隠す
      newValue.type = "hidden";
      // パラメータ名
      newValue.name = "companion";
      // パラメータ値
      newValue.value = JSON.stringify($params);
    
      // フォームの要素に加えることで、submit時に追加したパラメータも送信される
      document.forms[0].appendChild(newValue);
      document.forms[0].submit();

    }
    
    function checkEnter($event) {
      if($event.key === 'Enter') {
        return $event.preventDefault()
      }
    }

</script>
<form action="<?= Url::toRoute(['/cart/default/add']); ?>">

<?= Html::hiddenInput('pid', $model->product_id) ?>
<?= $extra_form ?>

<p class="Cart">人数： <select name="qty">
    <?= implode("\n", $qtyOption) ?>
</select>&nbsp;人&nbsp;&nbsp;


<?php if ($model->product_id != 3991 && !$companion): ?>
  <?= Html::submitButton("カートに入れる", ['class'=>'btn btn-warning']) ?>

<?php elseif($companion): ?>
  <?= Html::Button('カートに入れる', ['class'=>'btn btn-warning', 'onclick' => 'return addParam();']) ?>
<?php endif; ?>

<?php if ($magazine_flg): ?>
    <?= Html::submitButton("カートに入れて特典商品を見る", ['class'=>'btn btn-danger', 'onclick' => 'return postProduct();']) ?>
<?php endif; ?>
</p>
</form>

