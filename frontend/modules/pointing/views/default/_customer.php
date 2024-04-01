<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/pointing/views/default/_customer.php $
 * $Id: _customer.php 3616 2017-09-29 06:29:26Z kawai $
 */

use \yii\helpers\Html;

$this->params['body_id'] = 'Mypage';
/*
$dataProvider->sort = [
            'attributes' => [
                'code',
                'tel' => [
                    'asc'  => ['dtb_customer.tel01' => SORT_ASC, 'dtb_customer.tel02' => SORT_ASC, 'dtb_customer.tel03' => SORT_ASC],
                    'desc' => ['dtb_customer.tel01' => SORT_DESC, 'dtb_customer.tel02' => SORT_DESC, 'dtb_customer.tel03' => SORT_DESC],
                ],
                'customer.point' => [
                    'asc'  => ['dtb_customer.point' => SORT_ASC],
                    'desc' => ['dtb_customer.point' => SORT_DESC],
                ]
            ],
];

$dataProvider->pagination->pageSize = 5;
*/
?>

<?php $form = \yii\bootstrap\ActiveForm::begin(['id'=>'tel-form']); ?>

    <p class="help-block">
        会員証NOまたは電話番号を入力してください
    </p>

    <?= $form->field($searchModel, 'tel')->label(false)->textInput([
        'name'        => 'tel',
        'style'       => 'width:50%',
        'placeholder' => '0000000000',
    ]) ?>

    <p class="pull-left">
    <?= Html::submitbutton('検索',['class'=>'btn btn-success']) ?>
    </p>

<?php $form->end() ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'layout'  => '{items}',
    'columns' => [
        [
            'class'=> 'yii\grid\ActionColumn',
            'template' => '{apply}',
            'buttons' =>[
                'apply'    => function ($url, $model, $key)
                {
                    return Html::a('決定', ['apply','target'=>'customer','id'=>$model->customer_id],['class'=>'btn btn-xs btn-success']);

                },
            ],
        ],
        [
            'attribute' => 'code',
            'label'     => '豊受会員証NO',
        ],
        [
            'attribute' => 'tel',
            'label'     => '電話番号',
            'value'     => function($data){ return $data->tel; },
        ],
        [
            'attribute' => 'name',
            'label'     => '名前',
            'value'     => function($data){ return $data->name; },
        ],
    ],
])?>
