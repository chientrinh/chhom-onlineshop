<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/views/complex-remedy-drop.php $
 * $Id: complex-remedy-drop.php 4180 2019-09-07 02:57:15Z mori $
 *
 * $model    RemedyStock (an item of \common\components\cart\TailorMadeRemedyForm()->drops)
 * $key      
 * $index
 * $remedies array of remedy_id => abbr
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$remedy = $model->remedy;
?>
<div class="col-md-12">

    <div class="form group">

    <strong>滴下 <?= $index +1 ?></strong>

    <?= \yii\jui\AutoComplete::widget([
        'id' => sprintf('remedy-abbr-%d',$index),
        'attribute'     => 'abbr',
        'name'  => sprintf('Drops[%d][abbr]', $index),
        'value' => $remedy ? $remedy->abbr : '',
        'clientOptions' => [
		'source' => new yii\web\JsExpression('
                    function( request, response ) {
                    var tags = ' . json_encode($remedies) . ';
                    var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
                    response( $.grep( tags, function( item ){
                        return matcher.test( item );
                    }) );
                    }
                ')
	],        
        'options'       => [
            'class' => 'form-contorl has-success',
        ],
    ]);?>
    <?= Html::submitButton("✔", ['id'=>'remedy-btn-'.$index,'class'=>'btn btn-sm '.($remedy ? 'btn-default' : 'btn-primary'),'title'=>"決定"]) ?>
    </div>

    <?= Html::activeHiddenInput($model, 'potency_id',[
        'name'  => sprintf('Drops[%d][potency_id]', $index),
        'id'    => sprintf('Drops[%d][potency_id]', $index),
    ])?>

  <p>
<?php
$query = \common\models\RemedyStock::find()
    ->andWhere([
        'remedy_id' => $model->remedy_id,
        'in_stock'  => 1,
    ])
    ->drops();

if(! $user instanceof \backend\models\Staff)
    $query->forcustomer($user);

$potency_count = 0;
foreach($query->all() as $stock)
{
    $class = ($stock->potency_id == $model->potency_id) ? 'btn btn-xs btn-info' : 'btn btn-xs btn-primary';
    echo Html::submitButton($stock->potency->name,[
        'name'  => sprintf('Drops[%d][potency_id]', $index),
        'value' => $stock->potency_id,
        'class' => $class,
        'onClick' => sprintf('document.getElementById(Drop[%d][potency_id].value = %d; this.submit();', $index, $stock->potency_id),
    ]);
    $potency_count++;
    if($potency_count == 15) {
        echo '<p>';
        $potency_count = 0;
    } else {
        echo '&nbsp;';
    }
}
?>
  </p>

</div>
