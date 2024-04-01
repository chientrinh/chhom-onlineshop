<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use common\models\RemedyCategory;
use common\models\RemedyDescription;

/* @var $this yii\web\View */
/* @var $model common\models\ProductDescription */
/* @var $form yii\widgets\ActiveForm */

$array_display = ['1' => '表示', '0' => '非表示'];

// settings for image slider
$jscode = "

    // 初期表示
    var descObj = $( 'input[name=\"RemedyDescription[desc_division]\"]:radio:checked' );
    switchShowCategory(descObj.val());

    // 説明区分 選択時
    $( 'input[name=\"RemedyDescription[desc_division]\"]:radio' ).change( function() {
        switchShowCategory($(this).val());
    });

    function switchShowCategory(desc) {

        var categoryObj = $('.for-ad');

        // 説明区分が2（補足）でない場合はカテゴリー選択を表示しない
        if (desc != '".RemedyDescription::DIV_REPLETION."') {

            categoryObj.hide();
            categoryObj.attr('disabled', true);

            categoryObj.siblings().hide();

            categoryObj.parent().hide();
        } else {
            categoryObj.show();
            categoryObj.removeAttr('disabled');
            categoryObj.addClass(\"form-control\");

            categoryObj.siblings().show();

            categoryObj.parent().show();
            categoryObj.parents().find('div').addClass('required');
        }
    }
";
$this->registerJs($jscode, \yii\web\View::POS_LOAD);

?>

<div class="remedy-potency-form">

<?php
    if(! $model->isNewRecord) {
        $condition = [];

        $dataProvidor = new ActiveDataProvider([
                'query' => $model->find()
                                 ->andWhere(['remedy_desc_id' => $model->remedy_desc_id]),
//                                  ->andWhere(['remedy_category_id' => $model->remedy_category_id])
//                                  ->andWhere(['not', ['remedy_desc_id' => $model->remedy_desc_id]]),
                'sort'  => false
        ]);

        echo GridView::widget([
            'dataProvider' => $dataProvidor,
            'layout'       => '{items}',
            'columns'      => [
//                 [
//                     'label'         => 'レメディーカテゴリー',
//                     'value'         => 'remedyCategory.remedy_category_name',
//                     'headerOptions' => ['class'=>'col-md-2'],
//                 ],
                [
                    'attribute'     => 'title',
                    'label'         => '見出し',
                    'format'        => 'raw',
                    'value'         => 'title',
//                     'value'         => function($data) { return Html::a($data->title, ['/remedy-description/view', 'id'=>$data->remedy_desc_id]); },
                    'headerOptions' => ['class'=>'col-md-2'],
                ],
                [
                    'attribute'     => 'body',
                    'label'         => '本文',
                    'headerOptions' => ['class'=>'col-md-7'],
                    'options'       => ['width'=>'150px', 'style' => 'word-break:break-all'],
//                     'value'         => function($data) { return nl2br($data->body); },
                    'value'         => function($data) { return wordwrap(nl2br($data->body), 70, '<br />', true); },
                    'format'        => 'html',
                ],
                [
                    'attribute'     => 'desc_division',
                    'label'         => '説明区分',
                    'value'         => function($data) { return RemedyDescription::getDivisionForView($data->desc_division); }
                ],
//                 [
//                     'attribute'     => 'seq',
//                     'label'         => '表示順',
//                     'format'        => 'html',
//                     'headerOptions' => ['class'=>'col-md-1'],
//                 ],
//                 [
//                     'attribute'     => 'is_display',
//                     'label'         => '表示/非表示',
//                     'value'         => function($data){ return $data->displayName; },
//                     'headerOptions' => ['class'=>'col-md-1'],
//                 ],
                [
                    'label'         => '更新者',
                    'value'         => 'updator.name01',
                    'headerOptions' => ['class'=>'col-md-1'],
                ],
                [
                    'attribute'     => 'update_date',
                    'label'         => '更新日時',
                    'value'         => 'update_date',
                    'format'        => 'datetime',
                    'headerOptions' => ['class'=>'col-md-4'],
                ]
            ]
        ]);
    }

    $form = ActiveForm::begin([
            'fieldConfig' => [
                'template' => "{label}{input}\n{hint}\n{error}",
            ],
            'validateOnBlur'  => false,
            'validateOnChange'=> false,
            'validateOnSubmit'=> false,
        ]);

    echo $form->field($model, 'desc_division')->radioList(RemedyDescription::getDivisionForView(), ['class'=>'desc']);
    echo $form->field($model, 'remedy_category_id')->dropDownList(RemedyCategory::getRemedyCategoryPulldown(), ['class'=>'form-control']);
    echo $form->field($model, 'title')->textInput(['maxlength' => 255, 'class' => 'for-ad form-control']);
    echo $form->field($model, 'body')->textarea(['rows'=>15, 'class'=> 'form-control']);
    echo $form->field($model, 'seq')->dropDownList(range(0, 99), ['class'=>'form-control']);
    // echo $form->field($model, 'seq')->textInput(['placeholder' => '0', 'maxlength' => 11, 'class' => 'form-control']);
    echo $form->field($model, 'is_display')->dropDownList($model->getDisplayName(true), ['class'=>'form-control']);
?>

    <div class="pull-left">
        <?= Html::submitButton($model->isNewRecord ? '登録' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php if (! $model->isNewRecord ): ?>
    <div class="pull-right">
        <?= Html::a("削除", ['delete', 'id' => $model->remedy_desc_id], ['class' => 'btn btn-danger','data-confirm'=>'補足説明を削除します。よろしいですか。']) ?>
    </div>
    <?php endif; ?>

    <?php ActiveForm::end(); ?>

</div>


<div class="remedy-view">
    <?php
    echo \yii\widgets\DetailView::widget([
        'model' => $model->remedy,
        'attributes' => [
            'remedy_id',
            'abbr',
            'latin',
            'ja',
            'advertise:html',
            'concept',
            [
                'attribute' => 'on_sale',
                'value'     => $model->remedy->on_sale ? 'OK' : 'NG',
            ],
            [
                'attribute' => 'restrict_id',
                'value'     => $model->remedy->restriction->name,
            ],
        ],
    ]) ?>
</div>
