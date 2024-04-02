<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/create/_drop.php $
 * $Id: _drop.php 3411 2017-06-08 10:32:46Z kawai $
 *
 * $model    RemedyStock (an item of \common\components\cart\TailorMadeRemedyForm()->drops)
 * $key      
 * $index
 * $form     ActiveForm
 * $remedies array of remedy_id => abbr
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$remedy = $model->remedy;
?>

<div class="col-md-12">
  <h4><span>滴下 <?= $index +1 ?></span></h4>
    <div class="form group">
    <?= \yii\jui\AutoComplete::widget([
        'id' => sprintf('remedy-abbr-%d',$index),
        'attribute'     => 'abbr',
        'name'  => sprintf('Drops[%d][abbr]', $index),
        'value' => $remedy ? $remedy->abbr : '',
        'clientOptions' => ['source' => new yii\web\JsExpression('
                    function( request, response ) {
                    var tags = ' . json_encode($remedies) . ';
                    var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
                    response( $.grep( tags, function( item ){
                        return matcher.test( item );
                    }) );
                    }
                ')],
        'options'       => [
            'class' => 'form-contorl has-success',
        ],
    ]);?>
    <?= Html::submitButton("✔", ['class'=>'btn btn-sm '.($remedy ? 'btn-default' : 'btn-primary'),'title'=>"決定"]) ?>
    </div>

    <?= Html::activeHiddenInput($model, 'potency_id',[
        'name'  => sprintf('Drops[%d][potency_id]', $index),
        'id'    => sprintf('Drops[%d][potency_id]', $index),
    ])?>

  <p>
<?php
$drops =
    \common\models\RemedyStock::find()
    ->where([ 'remedy_id' => $model->remedy_id ])
    ->drops()
    ->all();

foreach($drops as $stock)
{
    $class = ($stock->potency_id == $model->potency_id) ? 'btn btn-xs btn-success' : 'btn btn-xs btn-primary';
    echo Html::submitButton($stock->potency->name,[
        'name'  => sprintf('Drops[%d][potency_id]', $index),
        'value' => $stock->potency_id,
        'class' => $class,
        'onClick' => sprintf('document.getElementById(Drop[%d][potency_id].value = %d; this.submit();', $index, $stock->potency_id),
    ]);
    echo '&nbsp;';
}
?>
  </p>

</div>

