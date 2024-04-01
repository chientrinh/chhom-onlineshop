<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_search-product.php $
 * $Id: $
 *
 * $model \common\models\ProductMaster
 */
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\helpers\ArrayHelper;
use \yii\bootstrap\ActiveForm;
use \common\models\Category;
use \common\models\Subcategory;
use \common\models\ProductMaster;
use \common\models\Campaign;
use \common\models\CampaignDetail;

$categories = ArrayHelper::merge([0=>'未選択'], ArrayHelper::map(Category::find()->all(), 'category_id', 'fullname'));
$subcategories = ArrayHelper::merge([0=>'未選択'], ArrayHelper::map(Subcategory::find()->all(), 'subcategory_id', 'fullname'));
$grades = ArrayHelper::map($grades, 'grade_id', 'name');

$products = [];
if (! $campaignDetails->isNewRecord && $campaignDetails->ean13) {
    $assignedProduct = ProductMaster::find()->andWhere(['ean13'=>$campaignDetails->ean13]);
    // var_dump($assignedProduct->count());exit;
    if ($assignedProduct->count() > 0) 
        foreach ($assignedProduct->all() as $key => $product)
            $productArray[$product->ean13] = $product->name. '&nbsp;&nbsp;('. $product->ean13. ')';
}

$csscode = "
    #cart-items   { margin: 20px 0; }

    #search-parts {
        margin: 20px 0;
        // margin-bottom: 25px;
        border: 5px solid #CCC;
        border-radius: 4px;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;   
        height: 280px;
    }


    #search-parts .inner h4 {
        margin: 0 0 15px;
        padding: 10px 0 10px 8px;
        background-color: #F0F0F0;
        font-size: 14px;
        font-weight: normal;
        border-top: 1px solid #FFF;
        border-left: 1px solid #FFF; 
    }

    #search-parts .inner label { font-size: 85%; }
    #search-parts .inner ul { padding: 0 0 0 18px; }
    #search-parts .inner ul li { margin-bottom: 6px; }
    #search-parts .inner div { margin-left: 10px; margin-right: 10px; }

    #barcode-form1 > *, 
    #barcode-form2 > * {
      display: inline-block;
    }

    h3 {
      background-color: #f0f8ff; 
     　border-radius: 10px;        /* CSS3草案 */  
      -webkit-border-radius: 10px;    /* Safari,Google Chrome用 */  
      -moz-border-radius: 10px;   /* Firefox用 */  
    }
";
$this->registerCss($csscode);


$jscode = "

$('[name=category_id]').on('change', function() {

    var category_id = $(this).val();
    
    if (category_id == 0)
        return false;

    var data = {target : 'category', category_id: category_id};

    ajax(data);
    $('[name=subcategory_id]').val(0);
});

$('[name=subcategory_id]').on('change', function() {

    var subcategory_id = $(this).val();
    
    if (subcategory_id == 0)
        return false;

    var data = {target : 'subcategory', subcategory_id: subcategory_id};

    ajax(data);
    $('[name=category_id]').val(0);
});

function ajax(data)
{
    // console.log(data);
    $.ajax({
      url:  'search',
      type: 'get',
      data: data,
      success: function (data) {
        $('#add-product > option').remove();
        //result = $.parseJSON(data);
        result = JSON.parse(data);
        $.each(result, function(key, value) {
        //JSON.parse(result, function(key, value) {
           if (key == null) return;
           $('#add-product').append($('<option>').html(value).val(key));
          // console.log(key == null);
        });
      },
      error: function(response) {
        return null;
      }
    });

}

// 
// バーコード入力欄制御
//


// 商品検索　有効、　商品直接入力　無効
var forEan13Search = function() {

    // 商品検索と商品プルダウンを選択可能にする
    $('#search-parts').find('select').prop('disabled', false);
    $('#barcode-form2').find('select').prop('disabled', false);

    // バーコード入力テキストボックスを入力不可にする
    $('#barcode-form1').find('input').prop('disabled', true);

    // 「商品検索」ボタンと「直接入力」ボタンの制御
    $('#ean13-input').prop('class', 'btn btn-default btn-sm');
    $('#ean13-search').prop('class', 'btn btn-primary btn-sm');

};

// 商品直接入力　有効、　商品検索　無効
var forEan13Input = function() {

    // 商品検索と商品プルダウンを選択不可にする
    $('#search-parts').find('select').prop('disabled', true);
    $('#barcode-form2').find('select').prop('disabled', true);

    // バーコード入力テキストボックスを入力可能にする
    $('#barcode-form1').find('input').prop('disabled', false);

    // 「商品検索」ボタンと「直接入力」ボタンの制御
    $('#ean13-input').prop('class', 'btn btn-primary btn-sm');
    $('#ean13-search').prop('class', 'btn btn-default btn-sm');



};

// 「商品検索」ボタン押下時
$('#ean13-search').on('click', function() {
    forEan13Search();
});

// 「直接入力」ボタン押下時
$('#ean13-input').on('click', function() {
    forEan13Input();
});
";
$this->registerJs($jscode);


$this->params['breadcrumbs'][] = ['label' => $campaign->campaign_name, 'url' => ['view', 'id' => $campaign->campaign_id]];
?>


<?= yii\widgets\DetailView::widget([
    'model' => $campaign,
    'attributes' => [
            [
                'attribute' => 'campaign_code',
                'label'     => 'キャンペーンコード',
                'value'     => $campaign->campaign_code,
                'headerOptions' =>['class'=>'col-md-1'],
            ],
            [
                'attribute' => 'campaign_type',
                'label'     => 'キャンペーン区分',
                'value'     => $campaign->campaign_type == Campaign::DISCOUNT ? "値引" : "ポイント",
                'headerOptions' =>['class'=>'col-md-1'],
            ],
            [
                'attribute' => 'campaign_name',
                'label'     => '名称',
                'value'     => $campaign->campaign_name,
                'headerOptions' =>['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'start_date',
                'label'     => '利用開始日時',
                'format'    => ['date','php:Y-m-d D H:i:s'],
                'value'     => $campaign->start_date,
                'headerOptions' =>['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'end_date',
                'label'     => '利用終了日時',
                'format'    => ['date','php:Y-m-d D H:i:s'],
                'value'     => $campaign->end_date,
                'headerOptions' =>['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'status',
                'label'     => '有効/無効',
                'format'    => 'raw',
                'value'     => $campaign->statuses[$campaign->status],
                'headerOptions' =>['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'branch_id',
                'label'     => '拠点',
                'value'     => $campaign->branch ? $campaign->branch->name : null,
                'headerOptions' =>['class'=>'col-md-2'],
            ],
            'create_date',
            'update_date',
    ],
]) ?>

<h3>　商品<?=$campaignDetails->isNewRecord ? "登録" : "更新"; ?></h3>

<div class="col-md-4 col-sm-8 col-xs-10">
    <div id="search-parts">
        <div class="inner">
            <h4>商品検索</h4>
            <div id="subcategory">
                <label class="control-label" for="subcategory_id">カテゴリー</label>
                <?= Html::dropDownList('category_id', null, $categories, ['id'=>'search-subcategory', 'class'=>'pull-left form-control'])?>
            </div>

            <div id="subcategory">
                <label class="control-label" for="subcategory_id">サブカテゴリー</label>
                <?= Html::dropDownList('subcategory_id', null, $subcategories, ['id'=>'search-subcategory', 'class'=>'pull-left form-control']); ?>
            </div>
        </div>
    </div>
</div>


<?php $form = ActiveForm::begin(); ?>

<div class="col-md-6 col-sm-8 col-xs-10">
    <div class="form-group" id ="form-parts">
            <?= Html::button('リスト選択',['id'=>'ean13-search','class'=>'btn btn-sm btn-primary']) ?>
            <?= Html::button('直接入力',['id'=>'ean13-input','class'=>'btn btn-default btn-sm']) ?>
            <p><p>
        <div id="barcode-form1">
            <?= $form->field($campaignDetails, 'ean13')
                         ->textInput(['maxlength' => 255, 'class' => 'form-control', 'disabled' => true])->label('直接入力（バーコード）') ?>
        </div>
        <div id="barcode-form2">
            <?= $form->field($campaignDetails, 'ean13')
                    ->dropDownList($products, ['id'=>'add-product', 'class' => 'form-control'])->label('リスト選択') ?>
        </div>
        <?php // Typeで判断する
            if($campaign->campaign_type == Campaign::DISCOUNT) : ?>
            <?= $form->field($campaignDetails, 'discount_rate')
                    ->textInput($subcategories, ['maxlangth'=>true, 'class'=>"form-control js-zenkaku-to-hankaku", 'style'=>'width:inherit']); ?>
            <?= $form->field($campaignDetails, 'grade_id', ['template' => '{input}'])->hiddenInput(['value' => NULL])->label(false); ?>
            <?= $form->field($campaignDetails, 'point_rate', ['template' => '{input}'])->hiddenInput(['value' => '0'])->label(false); ?>

        <?php else : ?>
            <?= $form->field($campaignDetails, 'grade_id')
                    ->dropDownList($grades, ['id'=>'add-grade', 'class' => 'form-control'/*'style'=>'width:inherit'*/]); ?>

            <?= $form->field($campaignDetails, 'point_rate')
                    ->textInput($products, ['maxlangth'=>true, 'class'=>"form-control js-zenkaku-to-hankaku", 'style'=>'width:inherit']); ?>
            <?= $form->field($campaignDetails, 'discount_rate', ['template' => '{input}'])->hiddenInput(['value' => '0'])->label(false); ?>

        <?php endif; ?>

    </div>

    <div class="form-group pull-left">
        <?= Html::submitButton($campaignDetails->isNewRecord ? "登録" : "更新", ['class' => $campaign->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <div class="pull-right">
        <?= Html::a('戻る', ['view', 'id' => $campaign->campaign_id, 'target' => 'viewProduct', ], ['class' => 'btn btn-danger update']
                ) ?>
    </div>
</div>

<?php $form->end(); ?>



