<?php
/**
 * @link    $URL: http://tarax.toyouke.com/svn/MALL/frontend/modules/cart/views/default/_companion.php $
 * @version $Id: _companion.php 3471 2017-07-01 04:58:29Z naito $
 *
 * $cart \common\components\cart\Cart
 */

use \yii\helpers\ArrayHelper;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\widgets\ActiveForm;
use \yii\widgets\ActiveField;

use \common\models\LiveInfo;
use \common\models\LiveItemInfo;

$companion_data = Yii::$app->session->get('companion_data');

$title = "同行者情報の指定";
$this->params['body_id']       = 'Mypage';
$this->params['breadcrumbs'][] = $title;
$this->title = sprintf('%s | %s | %s', $title, "カート", Yii::$app->name);
/**
 * バックエンドに登録した「追加情報」を取得してフォームを構成する
 * 
 */
$user = Yii::$app->user->identity;
$event_info = [];
$companion = [];
$entry_data = [];
$extra_form = "";

  // $companion = array_merge(['0' => '追加する同行者を選択してください'], explode(',',$model->companion));
  $companion = explode(',',$model->companion);
  $event_capacity = $model->capacity;
  $event_adult_price1 = $model->adult_price1;
  $event_adult_price2 = $model->adult_price2;
  $event_adult_price3 = $model->adult_price3;
  $event_child_price1 = $model->child_price1;
  $event_child_price2 = $model->child_price2;
  $event_child_price3 = $model->child_price3;
  $event_infant_price1 = $model->infant_price1;
  $event_infant_price2 = $model->infant_price2;
  $event_infant_price3 = $model->infant_price3;
  $event_subscription = $model->subscription;
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
  $entry_self_data = $user ? ["type" => "本人", "total" => $event_adult_price1, "price" => $event_adult_price2, "name" => $user->name01." ".$user->name02, "age" => ($user->birth ? floor((date("Ymd") - str_replace("-", "", $user->birth))/10000) : null),"tax" => $event_adult_price3] : ["type" => "本人", "total" => $event_adult_price1, "price" => $event_adult_price2, "name" => $companion_data[0][2], "age" => str_replace("歳","",$companion_data[0][3]),"tax" => $event_adult_price3];
  $entry_data[] = $entry_self_data;

  for($i=0; $i<count($companion_data); $i++) {
    // var_dump($companion_data[$i]);
    if($i == 0)
        continue;
    $data = ["type" => $companion_data[$i][0], "total" => $companion_data[$i][1], "price" => $companion_data[$i][1] - $companion_data[$i][4], "name" => $companion_data[$i][2], "age" => str_replace("歳","",$companion_data[$i][3]), "tax" => $companion_data[$i][4]];
    $entry_data[] = $data;
  }



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
             <span class="table-remove"><button type="button" style="font-size:20px;" class="btn btn-danger btn-rounded btn-sm my-0 waves-effect waves-light">削除</button></span><div hidden></div>
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
  
  if($model) {
    $extra_form .= Html::hiddenInput('info_id', $model->info_id);
    if($model->companion) {
      $extra_form .=  Html::label('同行者を追加 →　', 'companion',[
        'style'   => 'width:25%; font-size:20px; text-align:right']).Html::dropDownList('companion', "追加する同行者を選択してください", $companion,[
        'prompt'=>'追加する同行者を選択してください',
        'class'   => 'form-control',
        'style'   => 'width:75%; height:5%; font-size: 20px; display:inline',])."<br>";

      $extra_form .= '<table id="table" class="editable table table-bordered table-responsive-md table-striped text-center"><thead style="font-size:20px"><th class="text-center">参加区分</th><th class="text-center">料金</th><th class="text-center">氏名</th><th class="text-center">年齢</th></thead><tbody style="font-size:20px">';
      $extra_form .= \yii\widgets\ListView::begin([
        'dataProvider'  => new \yii\data\ArrayDataProvider([
            'allModels'  => $entry_data,
                'pagination' => false,
            ]),
        'viewParams' => ['entry_data' => $entry_data],
        'itemView'     => '_companion_item'  
        
    ])->renderItems();
    $extra_form .= '</tbody></table>';
    }
}
?>
<script>
    function addParam() {
      $params = [];
      $trs = $("#table").find("tbody tr");
      for( $i=0,$l=$trs.length;$i<$l;$i++ ){
        $cells = $trs.eq($i).children();
        for( $j=0,$m=$cells.length;$j<$m;$j++ ){
          if( typeof $params[$i] == "undefined" ) {
            $params[$i] = [];
          }
          // if(!$cells.eq($j).text().includes("追加") && !$cells.eq($j).text().includes("削除")) {
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
          // $params[$i][$j] = $cells.eq($j).text();
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

<h1 class="mainTitle"><?= $title ?></h1>

<div class="row column01">
    <div class="col-md-12">

        <?php $form = ActiveForm::begin([
            'fieldConfig' => [
                'template' => "{input}\n{hint}\n{error}",
            ],
        ]) ?>

        <?= $extra_form ?>

        <div class="form-group" style="text-align:center;">
        <?= Html::a("戻る",['/cart/index'], ['class' => 'btn btn-default', 'style' => 'margin:15px; font-size:20px']) ?>
        <?= Html::Button('決定',['class' => "btn btn-primary", 'style' => 'margin:15px; font-size:20px', "name" => "companion-edit" , "onclick" => "return addParam();"]) ?>
        </div>
        <?php $form->end(); ?>
    </div>
</div>

