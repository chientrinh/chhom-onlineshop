<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\RemedyPotency;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy/_form.php $
 * $Id: _form.php 3262 2017-04-20 11:50:04Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\Remedy * @var $form yii\widgets\ActiveForm
 */

$restrictions = \common\models\ProductRestriction::find()->all();
$restrictions = \yii\helpers\ArrayHelper::map($restrictions, 'restrict_id', 'name');

?>

<div class="remedy-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'abbr')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'latin')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'ja')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'advertise')->textArea() ?>

    <?= $form->field($model, 'concept')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'on_sale')->dropDownList([ 1 => "OK", 0 => "NG" ]) ?>

    <?= $form->field($model, 'restrict_id')->dropDownList($restrictions) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<div id="remedy-image">
    <h4>画像</h4>
    <?php foreach($model->images as $image): ?>
        <div class="thumbnail col-xs-6 col-md-3" style="margin-bottom:1em">
            <a href="<?=$image->url?>">
                <?= Html::img($image->url, ['alt'=> $image->basename, 'style'=>'max-width:100px;max-height:100px']) ?>
            </a>
            <?= Html::a(' ',['product-image/update','id'=>$image->img_id,'weight'=>$image->weight+1],[
                'class'=>'btn btn-xs btn-info glyphicon glyphicon-chevron-left pull-left',
                'title'=>'画像を上位に移動します',
            ]) ?>
            <?= Html::a(' ',['product-image/update','id'=>$image->img_id,'weight'=>$image->weight-1],[
                'class'=>'btn btn-xs btn-info glyphicon glyphicon-chevron-right pull-right',
                'title'=>'画像を下位に移動します',
            ]) ?>
            <p class="text-center text-muted">
                <?= $image->weight ?><br>
                <?= $image->caption ?>
            </p>
        </div>
    <?php endforeach ?>

    <p class="col-md-12 hint-block">画像の追加と削除は「品揃え＞編集 <i class="glyphicon glyphicon-pencil"></i>」にて操作できます</p>
</div>

<?php if(! $model->isNewRecord): ?>
<div id="remedy-stock">

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels'  => $model->stocks,
            'sort'       => [
                'attributes' => [
                    'price', 'in_stock', 'restrict_id', 'prange.name',
                    'vial.name' => [
                         'asc'  => ['vial.vial_id' => SORT_ASC],
                         'desc' => ['vial.vial_id' => SORT_DESC],
                     ],
                    'potency.name' => [
                         'asc'  => ['potency.weight' => SORT_ASC],
                         'desc' => ['potency.weight' => SORT_DESC],
                     ],
                ],
            ],
            'pagination' => false,
        ]),
        'layout' => '{items}',
        'caption' => "品揃え",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'potency.name',
            'prange.name',
            'vial.name',
            [
                'attribute' => 'price',
                'format'    => 'html',
                'value'     => function($data){ return '&yen;' . number_format($data->price); },
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'restrict_id',
                'value'     => function($data){ if($r = $data->restriction){ return $r->name; } },
            ],
            [
                'attribute' => 'in_stock',
                'filter'    => [1 => "OK", 0 => "NG"],
                'value'     => function($data){ return $data->in_stock ? "OK" : "NG"; },
            ],
            [
                'label' => '',
                'format'=> 'raw',
                'value' => function($data){
                    if($data->isNewRecord) return '';
                    return Html::a(Html::tag('i','',['class'=>'glyphicon glyphicon-pencil']),
                                   ['/remedy-stock/update',
                                    'remedy_id'  => $data->remedy_id,
                                    'potency_id' => $data->potency_id,
                                    'vial_id'    => $data->vial_id,
                                   ],['title'=>'編集']);
                },
            ],
        ],
    ]); ?>


<div class="well col-md-8">
<?php
$stock     = new \common\models\RemedyStock([
    'remedy_id'  => $model->remedy_id,
    'potency_id' => 15, // just a sample
    'vial_id'    => 4,  //
]);
if(count($model->stocks) && ($stock = $model->stocks[0]) && (RemedyPotency::COMBINATION == $stock->potency_id))
    $params = ['potency_id'=>RemedyPotency::COMBINATION];
elseif(count($model->stocks))
    $params = ['not',['potency_id'=>RemedyPotency::COMBINATION]];
else
    $params = [];

$remedies  = \yii\helpers\ArrayHelper::map(\common\models\Remedy::find()->all(),        'remedy_id', 'abbr');
$potencies = \yii\helpers\ArrayHelper::map(RemedyPotency::find()->where($params)->all(), 'potency_id', 'name');
$vials     = \yii\helpers\ArrayHelper::map(\common\models\RemedyVial::find()->all(),    'vial_id',    'name');

$form = ActiveForm::begin([
    'id'     => 'remedy-stock-create',
    'method' => 'get',
    'action' => ['remedy-stock/create'],
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{input}\n{error}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-4',
            'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-sm-8',
            'error' => '',
            'hint' => '',
        ],
    ],
]);

?>

    <?= $form->field($stock, 'remedy_id')->hiddenInput(['name'=>'remedy_id']) ?>
      <?= $form->field($stock, 'potency_id')->dropDownList($potencies,['name'=>'potency_id']) ?>
      <?= $form->field($stock, 'vial_id')->dropDownList($vials,['name'=>'vial_id']) ?>

    <?= Html::submitbutton("追加",['class' => 'btn btn-success',]) ?>

   <?php ActiveForm::end(); ?>

</div><!-- remedy-stock -->
<?php endif ?>

</div>
