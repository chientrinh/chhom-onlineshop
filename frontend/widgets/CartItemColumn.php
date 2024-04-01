<?php

namespace frontend\widgets;

use Yii;
use \yii\helpers\Html;
use \common\components\cart\CartItem;
use \common\models\PurchaseItem;
use common\models\ProductGrade;


/**
 * @link many thanks to https://github.com/RezaSR/yii2-ButtonDropdownSorter/blob/master/ButtonDropdownSorter.php
 * @link $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/CartItemColumn.php $
 * @version $Id: CartItemColumn.php 4248 2020-04-24 16:29:45Z mori $
 */
class CartItemColumn extends \yii\base\Widget
{
    public $cart_idx;
    public $purchase;
    public $items;
    public $recipes;

    public function init()
    {
        parent::init();
        
$jscode = "
$('.qty-txt').click(function(){
    $('#btn-' + $(this).attr('id')).hide();
    $('.input-group').hide();
    $('#ipt-' + $(this).attr('id')).show();
    return false;
});
";
         $this->view->registerJs($jscode);
    }
    
    public function renderImageColumn($idx)
    {
        $item = $this->items[$idx];
        if(! method_exists($item,'getImg') || ! method_exists($item,'getUrl'))
            return null;
            
        $img = $item->getImg();
        $url = $item->getUrl();

        if($img && $url)
            return $item->category->category_id != \common\models\Category::LIVE_OPTION ? Html::a($img, $url) : $img;
    }

    // // 購入履歴画面であるフラグをパラメータに追加
    public function renderLabelColumn($idx, $on_history = false)
    {
        $item = $this->items[$idx];
        $com = Html::tag('small', $item->company->name,['style'=>'color:#999']) . '<br>';
        // ライブ配信チケットはマイページ購入履歴画面時にはリンクを無効にする 2020/04/24 kawai 2021/04/15 改修 kawai
        $url = $on_history == true ? !in_array($item->category->category_id ,[\common\models\Category::LIVE, \common\models\Category::LIVE_OPTION]) ? $item->url : null : $item->category->category_id != \common\models\Category::LIVE_OPTION ? $item->url : null;
        $name= $url ? Html::a(nl2br($item->name), $url) : nl2br($item->name);
        $btn = '';
        $dsc = '';
        $pt  = '';
        $liquor = '';
        $recipe = '';
        $campaign = '';
        
        if($item->isLiquor())
            $liquor = Html::tag('span','お酒',['class'=>'btn-xs alert-warning pull-right','title'=>'この商品はお酒です']);
        
        if(isset($item->recipe_id) && in_array($item->recipe_id, $this->recipes))
            $recipe = Html::tag('span',sprintf('適用書 %06d',$item->recipe_id),['class'=>'btn-xs alert-info pull-right','title'=>'この商品は適用書により追加されたものです']);

        if(isset($item->campaign_id))
            $campaign = Html::tag('span',sprintf(' %s',$this->purchase->campaign->campaign_name),['class'=>'btn-xs alert-success pull-right','title'=>'この商品にはキャンペーンが適用されています']);


        if(null !== $this->cart_idx)
            $btn = sprintf('<p class="pull-right"><small>%s</small></p>',
                           Html::a("削除", ['del','cart_idx'=>$this->cart_idx,'item_idx'=>$idx],['class'=>''])
            );

        if($item->discountRate)
            if($this->purchase->payment_id === \common\models\Payment::PKEY_DROP_SHIPPING)
                $pt = sprintf('<p>代理店手数料 %d%%</p>', $item->discountRate);
            else
                $pt = sprintf('<p>ご優待 -%d%%</p>', $item->discountRate);

        if($item->pointRate)
            $pt .= sprintf('<p>ポイント %d%%</p>', $item->pointRate);
        elseif($item->pointAmount)
            $pt .= sprintf('<p>ポイント %d pt</p>', $item->pointAmount);


        if('echom-frontend' == Yii::$app->id)
            return $btn . Html::tag('p', $name) . $dsc;

        return $btn . Html::tag('p', $com . $liquor .$name) . $pt . $recipe . $campaign . $dsc;
    }

    public function renderPriceColumn($idx)
    {
        $item  = $this->items[$idx];

        if('echom-frontend' == Yii::$app->id) {
            // $grade_id = isset(Yii::$app->user->id) ? \common\models\Customer::currentGrade(Yii::$app->user->id) : null;
            // $grade = ProductGrade::findOne(['product_id'=>$item->model->product_id,'grade_id'=>$grade_id]);
            $customer = \common\models\Customer::findOne(Yii::$app->user->id);
            // $grade_id = isset(Yii::$app->user->id) ? \common\models\Customer::currentGrade(Yii::$app->user->id) : null;
            $grade = ProductGrade::getGrade($item->model->product_id,$customer);
            $price = $grade ? Yii::$app->formatter->asCurrency($grade->price) : Yii::$app->formatter->asCurrency($item->price);
            if(Yii::$app->session->get('companion_item') == $item->model->product_id && Yii::$app->session->get('companion_price'))
                $price = Yii::$app->formatter->asCurrency(Yii::$app->session->get('companion_price'));

        } else {
            $price = Yii::$app->formatter->asCurrency($item->price);
        }
        $dsc   = '';
        $pt    = '';
        $addpt = '';

        

        if($item->discountAmount && ($this->purchase->payment_id !== \common\models\Payment::PKEY_DROP_SHIPPING))
            $dsc = '<br>' . Yii::$app->formatter->asCurrency(0 - $item->discountAmount);

        if($item->pointAmount && ($this->purchase->payment_id !== \common\models\Payment::PKEY_DROP_SHIPPING))
            $addpt = '<br>pt ' . $item->pointAmount;

        return $price . $pt . $dsc . $addpt;
    }

    public function renderQtyColumn($idx)
    {
        $item = $this->items[$idx];
        if(null === $this->cart_idx)
            return Html::tag('p', $item->qty, ['class'=>'text-center']);

        $out = '';
        if(($m = $item->model) && $m instanceof \yii\db\ActiveRecord)
        {
            if($m->hasAttribute('in_stock') && ! $m->in_stock)
                $out = Html::tag('span','在庫なし',['class'=>'not-set btn alert-danger']);
        }

        return $this->render('cart-item-qty',['item'=>$item,'cart_idx'=>$this->cart_idx,'idx'=>$idx,'out'=>$out]);
    }

    public function renderChargeColumn($idx)
    {
        $item   = $this->items[$idx];
        $charge = $item->charge;
        $dsc    = '';
        $pt     = '';

        if($item->discountTotal)
            $dsc = Html::tag('p','&nbsp;');

        if($item->pointTotal)
            $pt = Html::tag('p', 'pt ' . number_format($item->pointTotal));

        if(! $item->company)
            Yii::warning(['PurchaseItem::company_id is not set', $item->attributes], self::className().'::'.__FUNCTION__);

        $purchase = $this->purchase;
        if(($purchase->payment_id === \common\models\Payment::PKEY_DROP_SHIPPING) &&
            $purchase->customer->isAgency() &&
            $purchase->customer->isAgencyOf($item->company->company_id))
        {
            $charge = $item->basePrice;
        }

        if('echom-frontend' == Yii::$app->id) {
            $customer = \common\models\Customer::findOne(Yii::$app->user->id);
            // $grade_id = isset(Yii::$app->user->id) ? \common\models\Customer::currentGrade(Yii::$app->user->id) : null;
            $grade = ProductGrade::getGrade($item->model->product_id,$customer);
            $price = $grade ? $grade->price : $item->charge;
            if(Yii::$app->session->get('companion_item') == $item->model->product_id && Yii::$app->session->get('companion_price'))
                $price = Yii::$app->session->get('companion_price');

            $charge = Yii::$app->formatter->asCurrency($price);

        } else {
            $charge = Yii::$app->formatter->asCurrency($item->charge);
        }

        // $charge =  Yii::$app->formatter->asCurrency($charge);

        return $charge . $pt . $dsc;
    }
}
