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
use \common\models\Subcategory;

$jscode = "
$('#search-parts').on('change', function(){
  var form = $('#search-product');
  form.submit();
});
";
$this->registerJs($jscode);

$csscode = "
    #cart-items   { margin: 20px 0; }

    #search-parts {
        margin: 20px 0;
        // margin-bottom: 25px;
        border: 5px solid #CCC;
        border-radius: 4px;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px; }

    #search-parts .inner {
        padding: 0;
        border: 1px solid #C0C0C0; }

    #search-parts .inner h4 {
        margin: 0 0 15px;
        padding: 10px 0 10px 8px;
        background-color: #F0F0F0;
        font-size: 14px;
        font-weight: normal;
        border-top: 1px solid #FFF;
        border-left: 1px solid #FFF; }

    #search-parts .inner label { font-size: 85%; }
    #search-parts .inner ul { padding: 0 0 0 18px; }
    #search-parts .inner ul li { margin-bottom: 6px; }
    #search-parts .inner div { margin-left: 10px; margin-right: 10px; }
";
$this->registerCss($csscode);

$searchForm = ActiveForm::begin([
                  'action' => ['search'/*$this->context->action->id*/, 'target' => $target],
                  'method' => 'get',
                  'fieldConfig' => [
                      'enableLabel' => false,
                  ],
                  'id' => 'search-product',
]); 
if ($searchModel->canGetProperty('subcategory_id'))
    $subcategories = ArrayHelper::merge([''=>'指定なし'], ArrayHelper::map(Subcategory::find()->$target()->orderby(['subcategory_id' => SORT_ASC])->all(), 'subcategory_id', 'fullname'));

?>

<!-- サブカテゴリー検索（「適用書」「オリジナル」「RXT」以外に表示） -->
<div id="search-parts">
    <div class="inner">
        <h4>商品検索</h4>

        <?php if ($searchModel->canGetProperty('subcategory_id')): ?>
        <div id="subcategory">
            <label class="control-label" for="subcategory_id">サブカテゴリー</label>
            <?= $searchForm->field($searchModel, 'subcategory_id')
                        ->dropDownList($subcategories, ['id'=>'search-subcategory']); ?>
        </div>
        <?php endif; ?>

        <!-- <div id="keywords">
            <label class="control-label" for="subcategory_id">キーワード</label>
            <?= $searchForm->field($searchModel, 'keywords')
                        ->textInput(['id'=>'search-keywords']); ?>
        </div> -->

        <?php //echo Html::submitInput('検索',['class'=>'btn btn-sm btn-success']) ?>
    </div>

  <?php $searchForm->end() ?>
</div>

