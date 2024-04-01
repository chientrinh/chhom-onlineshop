<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/product/_form.php $
 * $Id: _form.php 2901 2016-09-30 04:11:33Z mori $
 */

use \yii\helpers\ArrayHelper;
use \yii\helpers\Html;
use \yii\helpers\Url;

use \common\models\Subcategory;

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
$school1   = ['0' => '会場を選択してください', '0921東京' => '東京校', '0921札幌' => '札幌校', '0921名古屋' => '名古屋校', '0921大阪' => '大阪校'];
$school2  = ['0828東京' => '東京校'];


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
    $extra_form =  "参加会場： ".Html::dropDownList('school2', null, $school2)."<br>";
    $extra_form .=  "お子様連れ<br>（小学生以下）： ".Html::dropDownList('children', null, $children)."<br><br>";
    break;


  default:
    $extra_form = "";
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

<p class="Cart">人数： <select name="qty">
    <?= implode("\n", $qtyOption) ?>
</select>&nbsp;人&nbsp;&nbsp;


<?php if ($model->product_id != 2609): ?>
<?= Html::submitButton("カートに入れる", ['class'=>'btn btn-warning']) ?>
<?php endif; ?>

<?php if ($magazine_flg): ?>
    <?= Html::submitButton("カートに入れて特典商品を見る", ['class'=>'btn btn-danger', 'onclick' => 'return postProduct();']) ?>
<?php endif; ?>
</p>
</form>

