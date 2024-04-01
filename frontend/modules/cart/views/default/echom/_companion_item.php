<?php
/**
 * @link    $URL: http://tarax.toyouke.com/svn/MALL/frontend/modules/cart/views/default/_companion_item.php $
 * @version $Id: _companion_item.php 3471 2017-07-01 04:58:29Z naito $
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

// var_dump($model);
$adult_ages = ['12' => '12歳', '13' => '13歳', '14' => '14歳', '15' => '15歳', '16' => '16歳', '17' => '17歳', '18' => '18歳', '19' => '19歳', '20' => '20歳', '21' => '21歳', '22' => '22歳', '23' => '23歳', '24' => '24歳', '25' => '25歳', '26' => '26歳', '27' => '27歳', '28' => '28歳', '29' => '29歳', '30' => '30歳', '31' => '31歳', '32' => '32歳', '33' => '33歳', '34' => '34歳', '35' => '35歳', '36' => '36歳', '37' => '37歳', '38' => '38歳', '39' => '39歳', '40' => '40歳', '41' => '41歳', '42' => '42歳', '43' => '43歳', '44' => '44歳', '45' => '45歳', '46' => '46歳', '47' => '47歳', '48' => '48歳', '49' => '49歳', '50' => '50歳', '51' => '51歳', '52' => '52歳', '53' => '53歳', '54' => '54歳', '55' => '55歳', '56' => '56歳', '57' => '57歳', '58' => '58歳', '59' => '59歳', '60' => '60歳', '61' => '61歳', '62' => '62歳', '63' => '63歳', '64' => '64歳', '65' => '65歳', '66' => '66歳', '67' => '67歳', '68' => '68歳', '69' => '69歳', '70' => '70歳', '71' => '71歳', '72' => '72歳', '73' => '73歳', '74' => '74歳', '75' => '75歳', '76' => '76歳', '77' => '77歳', '78' => '78歳', '79' => '79歳', '80' => '80歳', '81' => '81歳', '82' => '82歳', '83' => '83歳', '84' => '84歳', '85' => '85歳', '86' => '86歳', '87' => '87歳', '88' => '88歳', '89' => '89歳', '90' => '90歳', '91' => '91歳', '92' => '92歳', '93' => '93歳', '94' => '94歳', '95' => '95歳', '96' => '96歳', '97' => '97歳', '98' => '98歳', '99' => '99歳', '100' => '100歳', '101' => '101歳', '102' => '102歳', '103' => '103歳', '104' => '104歳', '105' => '105歳', '106' => '106歳', '107' => '107歳', '108' => '108歳', '109' => '109歳', '110' => '110歳', '111' => '111歳', '112' => '112歳', '113' => '113歳', '114' => '114歳', '115' => '115歳', '116' => '116歳', '117' => '117歳', '118' => '118歳', '119' => '119歳', '120' => '120歳'];
$child_ages = ['6' => '6歳', '7' => '7歳', '8' => '8歳', '9' => '9歳', '10' => '10歳', '11' => '11歳', '12' => '12歳'];
$infant_ages = ['0' => '0歳', '1' => '1歳', '2' => '2歳', '3' => '3歳', '4' => '4歳', '5' => '5歳', '6' => '6歳'];

$adult_select = '<select class="form-control" name="age">
<option value="" selected>選択</option>';
  foreach ( $adult_ages as $key => $age ) {
    if ( $key == $model['age'] ) {
      $adult_select .= '<option value="' . $key . '" selected>' . $age . '</option>';
    } else {
      $adult_select .= '<option value="' . $key . '">' . $age . '</option>';
    }
  }

$child_select = '<select class="form-control" name="age">
  <option value="" selected>選択</option>';
    foreach ( $child_ages as $key => $age ) {
      if ( $key == $model['age'] ) {
        $child_select .= '<option value="' . $key . '" selected>' . $age . '</option>';
      } else {
        $child_select .= '<option value="' . $key . '">' . $age . '</option>';
      }
    }

$infant_select = '<select class="form-control" name="age">
<option value="" selected>選択</option>';
  foreach ( $infant_ages as $key => $age ) {
    if ( $key == $model['age'] ) {
      $infant_select .= '<option value="' . $key . '" selected>' . $age . '</option>';
    } else {
      $infant_select .= '<option value="' . $key . '">' . $age . '</option>';
    }
  }

$view = "";
if($model['type'] == "本人") {
  if(!Yii::$app->user->identity) {
    $view = '<tr class="">
      <td class="pt-3-half">本人</td>
      <td class="pt-3-half">'.$model['total'].'</td>
      <td class="pt-3-half editable" contenteditable="true" data-placeholder="氏名を入力して下さい" onKeyPress="return checkEnter(event);">'.$model['name'].'</td>
      <td class="pt-3-half" data-placeholder="年齢を入力して下さい">'.$adult_select.'</td>
      <td>
          <span class="table-remove"></span><div hidden>'.$model['tax'].'</div>
      </td>';
  } else {
    $view = sprintf('<tr data-key="%d"><td id="type-%d" class="pt-3-half">%s</td><td id="price-%d" class="pt-3-half">%s</td><td id=name-%d class="pt-3-half">%s</td><td id="age-%d" class="pt-3-half">%s歳</td><td><div hidden>%d</div>
    </td></tr>', $key, $key, preg_replace('/\(.*\)/','<span class="mini">${0}</span>',$model['type']),$key, $model['total'],$key, $model['name'], $key, $model['age'], $model['tax']);
  }
} else {
  if($model['type'] == "大人") {
    $view = '<tr class="">
    <td class="pt-3-half">'.$model['type'].'</td>
    <td class="pt-3-half">'.$model['total'].'</td>
    <td class="pt-3-half editable" contenteditable="true" data-placeholder="氏名を入力して下さい" onKeyPress="return checkEnter(event);">'.$model['name'].'</td>
    <td class="pt-3-half" data-placeholder="年齢を入力して下さい">';

    $view .= $adult_select;
    $view .= '</td>
    <td>
    <span class="table-remove"><button type="button" style="font-size:20px" class="btn btn-danger btn-rounded btn-sm my-0 waves-effect waves-light">削除</button></span><div hidden>'.$model['tax'].'</div>
    </td>
    </tr>';
  } else {
    $view = '<tr class="">
    <td class="pt-3-half">'.$model['type'].'</td>
    <td class="pt-3-half">'.$model['total'].'</td>
    <td class="pt-3-half editable" contenteditable="true" data-placeholder="氏名を入力して下さい" onKeyPress="return checkEnter(event);">'.$model['name'].'</td>
    <td class="pt-3-half" data-placeholder="年齢を入力して下さい">';

    $view .= $model['type'] == "小人" ? $child_select : $infant_select;
    $view .= '</td>
    <td>
    <span class="table-remove"><button type="button" style="font-size:20px" class="btn btn-danger btn-rounded btn-sm my-0 waves-effect waves-light">削除</button></span><div hidden>'.$model['tax'].'</div>
    </td>
    </tr>';
  }
}
echo $view;
?>

