<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/search.php $
 * $Id: search.php 3926 2018-06-06 05:25:04Z mori $
 *
 * $target string
 * $searchModel   Model
 * $dataProvider  ActiveDataProvider
 */

use \yii\helpers\ArrayHelper;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\bootstrap\ActiveForm;

$this->params['breadcrumbs'][] = ['label' => 'レジ', 'url' => ['create']];
if('_customer' === $viewFile)
    $this->params['breadcrumbs'][] = 'お客様を検索';
else
    $this->params['breadcrumbs'][] = '商品を検索';

$jscode = "

function newWindow(href) {
  window.open(href,'','width=1000,height=800,top=100,left=100,status=yes,resizable=yes,scrollbars=yes');
}


$('.panel-body').on('click','a',function(e){
   $.ajax({
       url: $(e.target).attr('href'),
       data: {},
       success: function(data) {
           $(e.target).before('&#10004;');
       }
   });
   return false;
});

$('#product-grid-view').on('click','a',function(e){
   if(! $(this).hasClass('text-info')) return true;

   $.ajax({
       url: $(e.target).attr('href'),
       data: {},
       success: function(data) {
           $(e.target).attr('class','btn btn-xs btn-default')
       }
   });
   return false;
});

$('button').on('click',function(e)
{
    if(! $(this).hasClass('btn-success')) return true;
    var form = $(e.target).parents('form:first');

    $.ajax({
         url:  'apply',
         type: 'get',
         data: form.serialize(),
         success: function (data) {
           $('#cart-items').html(data);
         },
         error: function(response) {
           $('#cart-items').html(data);
         }
    });

    return false;
});

$('#cart-items').on('click','.item',function(e){
   $.ajax({
       url: $(e.target).attr('href'),
       data: {},
       success: function(data)
       {
           data = JSON.parse(data);
           qty  = data['item']['quantity'];

           if(0 < qty)
               $(e.target).siblings('strong').html(qty);
           else
               $('#cart-items').html(data['widget']); // refresh entire table
       }
   });
   return false;
});

$('input[name=\"qty\"]').on('change', function(){
  qty = toHalfWidth($(this).val());
  $(this).val(qty);
});

/**
 * 全角から半角への変革関数
 * 入力値の英数記号を半角変換して返却
 * [引数]   strVal: 入力値
 * [返却値] String(): 半角変換された文字列
 */
function toHalfWidth(strVal){
  // 半角変換
  var halfVal = strVal.replace(/[！-～]/g,
    function( tmpStr ) {
      // 文字コードをシフト
      return String.fromCharCode( tmpStr.charCodeAt(0) - 0xFEE0 );
    }
  );
  return strVal;
}
";
$this->registerJs($jscode, \yii\web\View::POS_END);

\frontend\assets\AppAsset::register($this);

$target = Yii::$app->request->get('target');
$branch = $this->context->module->branch;
$kOption = ['placeholder'=> '検索キーワードを入力してください。'];
?>

<div class="cart-default-search">
  <div class="body-content">

    <div class="col-md-12">

      <?= $this->render('__nav') ?>

      <?php $form = ActiveForm::begin([
          'action' => [$this->context->action->id, 'target' => $target, 'company' => 0],
          'method' => 'get',
          'fieldConfig' => [
              'enableLabel' => false,
          ],
      ]); ?>

      <div class="row">

        <?php if($searchModel->canGetProperty('keywords')): ?>
        <div class="col-md-6">

            <?php if('customer' == $target): ?>
                <?= $form->field($searchModel, 'keywords')->textInput($kOption) ?>
            <?php elseif('remedy' == $target): ?>
                <?= $form->field($searchModel, 'keywords')->textInput(['name'=>'startwith','value'=>Yii::$app->request->get('startwith')],$kOption) ?>
            <?php else: ?>
                <?= $form->field($searchModel, 'keywords')->textInput($kOption) ?> 
            <?php endif ?>

        </div>
        <div class="col-md-2">
          <?= Html::submitInput('検索',['class'=>'form-control btn btn-default']) ?>
        </div>
        <?php endif ?>

      </div>

      <?php $form->end() ?>
    </div>

    <!-- <?php $form = ActiveForm::begin([
        'method' => 'get',
    ]) ?> -->

    <?php if('_delivery' === $viewFile): ?>
      <?= $this->render($viewFile, ['searchModel'=>$searchModel,'dataProvider'=>$dataProvider, 'target'=> $target]) ?>
    <?php else: ?>
      <div class="col-md-12">

        <div class="col-md-3">

          <?php if((($branch->isHJForCasher() || $branch->isAtamiForCasher() || $branch->isRopponmatsuForCasher()) 
                      && ! in_array($target, ['recipe', 'compose', 'all_remedy', 'customer'])) 
                || ($branch->isHEForCasher() && ! in_array($target, ['veg', 'customer', 'other', 'agent']))): ?>
            <?= $this->render('_search-product', ['searchModel'=>$searchModel, 'target'=> $target]) ?><!-- サブカテゴリー検索 -->
          <?php endif ?>

          <div id="cart-items">
              <?php if($this->context->module->purchase->items):
              $items = $this->context->module->purchase->items; ?>
              <p>
                計 <?= $this->context->module->purchase->itemCount ?> 点
                <?= Html::a('<i class="glyphicon glyphicon-remove"></i>',['apply','target'=>'reset'],['style'=>'color:#999','title'=>'すべて初期化します']) ?>
              </p>
              <?= \backend\modules\casher\widgets\CartContentGrid::widget(['items' => array_reverse($items)]) ?> 
              <?php endif ?>
          </div>
        </div>

        <div class="col-md-9">
          <?php 
            if(! in_array($target, [])){
                echo $this->render($viewFile, ['searchModel'=>$searchModel,'dataProvider'=>$dataProvider, 'target'=> $target]);
            }

            elseif ($this->beginCache($this->context->id, [
                'dependency' => [
                    'class' => \yii\caching\DbDependency::className(),
                    'sql'   => 'SELECT (SELECT SUM(ean13) FROM mvtb_product_master) + (SELECT SUM(in_stock) FROM mtb_remedy_stock) + (SELECT COUNT(*) FROM dtb_customer)',
                ],
                'duration' => 3600 * 12, // 12 hours
                'variations' => [
                    \yii\helpers\Json::encode(Yii::$app->request->get()),
                    \yii\helpers\Json::encode(Yii::$app->request->post()),
                    $this->context->module->purchase->customer_id,
                    $this->context->module->branch->branch_id,
                ],
            ]))
            {
                // ... generate content here ...
                echo $this->render($viewFile, ['searchModel'=>$searchModel,'dataProvider'=>$dataProvider]);
                $this->endCache();
            }
          ?>
        </div>
      </div>
    <?php endif ?>

    <!-- <?php $form->end() ?> -->

  </div>
</div>
