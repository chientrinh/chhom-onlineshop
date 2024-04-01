<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/remedy/search.php $
 * $Id: search.php 3411 2017-06-08 10:32:46Z kawai $
 *
 * $model \common\components\cart\ComplexRemedyForm
 * $user  \common\models\Customer || null
 */

$this->params['body_id'] = 'MyPage';
$this->params['breadcrumbs'][] = ['label' => '単品レメディー', 'url' => ['search']];

$widget = new \frontend\widgets\SimpleRemedyView([
   'remedy'=> $remedy,
   'stock' => $stock,
   'user'  => $user,
   ]);

$csscode = 'div.required label:after {
    content: "";
}';
$this->registerCss($csscode);
?>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'layout' => 'inline',
    'fieldConfig' => [
           'template'    => '{input}',
    ],
    'method' => 'get',
    'enableClientValidation' => false,
]) ?>

<div class="col-md-4 col-sm-4">
    <?= Html::tag('label','レメディー名',['for'=>$stock->formName() .'[remedy_id]']) ?>

    <?= $form->field($remedy, 'abbr')->widget(\yii\jui\AutoComplete::className(), [
        'id'            => 'remedy-abbr',
        'options'       => ['class'  => 'form-control'],
        'clientOptions' => ['source' => new yii\web\JsExpression('
                    function( request, response ) {
                    var tags = ' . json_encode($widget->abbrs) . ';
                    var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
                    response( $.grep( tags, function( item ){
                        return matcher.test( item );
                    }) );
                    }
                ')],
    ]) ?>

</div>

<div class="col-md-4 col-sm-4">
    <?= Html::tag('label','容器',['for'=>$stock->formName() .'[vial_id]']) ?>

<?= $form->field($stock, 'vial_id')->checkboxList($widget->vials) ?>
</div>

<div class="col-md-4 col-sm-4">
    <?= Html::tag('label','ポーテンシー',['for'=>$stock->formName() .'[potency_id]']) ?>
<?= $form->field($stock, 'potency_id')->checkboxList($widget->potencies) ?>
</div>

<div class="col-md-12">
    <?= Html::submitbutton('検索',['class'=>'btn btn-success']) ?>
</div>

<?php $form->end() ?>

<div class="col-md-12">
&nbsp;
</div>

<div class="col-md-12">

<?= $widget->renderRemedies() ?>

</div>
