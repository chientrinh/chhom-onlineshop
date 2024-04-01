<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/views/cart-item-qty.php $
 * $Id: cart-item-qty.php 2781 2016-07-24 08:28:19Z naito $
 */
?>
<div id="btn-qty-<?=$idx?>"> 
    <p class="text-center">
        <strong>
             <?= $item->qty ?><br><br>
             <?= Html::a('-',['update','target'=>'qty','cart_idx'=>$cart_idx,'idx'=>$idx,'qty'=>$item->qty -1],['class'=>'badge','style'=>'background-color:#999'])?>
        </strong>
    </p>
</div>
<div  id = "ipt-qty-<?=$idx?>" style="display:none">
<?php $form = \yii\bootstrap\ActiveForm::begin([
        'action' => ['update','target'=>'qty','cart_idx'=>$cart_idx],
    ]) 
?>

     <p class="text-center">
         <strong>
             <input type="text"  name="qty"  value= <?= $item->qty ?> class="col-md-12" >  
             <input type="hidden"  name="idx"  value= <?= $idx ?> >
             <?=Html::submitButton('更新',['name'=>'submit','class'=>'btn btn-warning alert-warning pull-right','title'=>'数量を更新'])?>
         </strong>
     </p>
 </form>
</div>
<?=$out?>

             
            
