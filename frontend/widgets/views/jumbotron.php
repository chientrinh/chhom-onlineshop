<?php 
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/views/jumbotron.php $
 * $Id: jumbotron.php 4248 2020-04-24 16:29:45Z mori $
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$model   = new \frontend\models\SearchProductMaster(['customer' => Yii::$app->user->identity]);

$categories = \common\models\Category::find()->join('JOIN',\common\models\ProductMaster::tableName(),'mtb_category.category_id = mvtb_product_master.category_id')->where(['restrict_id'=>0])->andWhere('mtb_category.category_id != 24')->all();
$categories = \yii\helpers\ArrayHelper::map($categories, 'name', 'category_id');
$categories[''] = '';
$categories = array_flip($categories);
ksort($categories);
$categories[''] = "カテゴリーを選択";
?>
    <div class="jumbotron">

    <h1 style="color:#003F74">ようこそ<br>豊受オーガニクスショッピングモールへ</h1>
    <p class="lead" style="color:#003F74">
        無農薬・無化学肥料・自家採種にこだわる農業生産法人日本豊受自然農株式会社が、皆様の健康と自然生活を応援するため、日本豊受自然農と理念を同じくする企業や団体と提携し、商品販売やサービスを提供いたします。どうぞご利用ください。
    </p>

<!--
<div style="border:2px solid #999; margin-top:5px; padding:5px; background-color:#ffffff;height:150px;">
<p style="text-align:left; font-size:100%;">
<br/>
<img src="http://toyouke.com/images/osechi2017_1.jpg" align="left" width="100" height="100"><a href="https://mall.toyouke.com/index.php/product/1079"><strong>　豊受自然農の<br/><br/>　「無添加おせち」販売中！（数量限定）<br/><br/><span style="color:red;">　12/27（水）受付締切</span></strong></a>
<br/>
</p>
</div>
-->
<!--
<div style="border:2px solid #999; margin-top:5px; padding:5px; background-color:#ffffff;">
<p style="text-align:center; font-size:100%;">
<br/>
<a href="https://toyouke.com/news/14957.html" target="_blank"><strong><span style="color:red;">システムメンテナンスのお知らせ</span><br/><br/>　10/19（土）21：00 ～ 10/20（日）9：00　</strong></a>
</div>
-->


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
