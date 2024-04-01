<?php 
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/views/echom-top.php $
 * $Id: jumbotron.php 2574 2016-06-09 06:50:25Z mori $
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$model   = new \frontend\models\SearchProductMaster(['customer' => Yii::$app->user->identity]);

// $categories = \common\models\Category::find()->join('JOIN',\common\models\ProductMaster::tableName(),'mtb_category.category_id = mvtb_product_master.category_id')->where(['restrict_id'=>0])->all();
// $categories = \yii\helpers\ArrayHelper::map($categories, 'name', 'category_id');
// $categories[''] = '';
// $categories = array_flip($categories);
// ksort($categories);
$categories['24'] = "ライブ配信";
?>
    <div class="jumbotron">

    <h1 style="color:#003F74">ようこそ<br>CHhomオンラインショップへ！</h1>
    <p class="lead" style="color:#003F74">
        ライブ配信チケットはこちらでお申し込みいただけます。豊受モール会員の方は割引特典もございます。会員でないお客様も是非この機会に豊受モールにご登録ください<br/>
        
    </p>

<?php $form = ActiveForm::begin(['id' => 'product-search-global', 'action'=>[\yii\helpers\Url::to('/product/search')], 'method'=>'get']);?>

       <div class="form-group bgr-search">
       <?= $form->field($model, 'keywords',[
           'options'  => ['class'=>'form-group field-productsearch-keywords'],
           'template' => '<div class="form-group field-searchform-keyword">{input}</div>'
                      ])->textInput(['placeholder'=>"商品名 または キーワード",'name'=>'keywords']) ?>

       <?= $form->field($model, 'category_id',[
           'options'  => ['class'=>'form-group field-productsearch-categories'],
           'template' => '<div class="form-group field-searchform-category">{input}{error}</div>'
                      ])->dropDownList($categories, [
                          'name'       => 'category',
                          'id'         => 'productsearch-categories',
                      ]) ?>
        <?= Html::submitButton("検索", ['class' => 'btn btn-success', 'name' => 'search-button']) ?>

       </div><!--form-group bgr-search-->

<?php ActiveForm::end() ?>

    </div><!--jumbotron-->
