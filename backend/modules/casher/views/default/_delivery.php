<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_delivery.php $
 * $Id: _delivery.php 2994 2016-10-20 05:03:22Z mori $
 *
 * $searchModel  Model
 * $dataProvider ActiveDataProvider of \backend\models\Customer
 */
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

$dataProvider->sort = [
    'attributes'   => [
        'pref_id',
        'tel01' => [
            'asc'     => ['tel01' => SORT_ASC, 'tel02' => SORT_ASC, 'tel03' => SORT_ASC ],
            'desc'    => ['tel01' => SORT_DESC,'tel02' => SORT_DESC,'tel03' => SORT_DESC],
            'default' => SORT_ASC,
        ],
        'zip01' => [
            'asc'     => ['zip01' => SORT_ASC, 'zip02' => SORT_ASC, ],
            'desc'    => ['zip01' => SORT_DESC,'zip02' => SORT_DESC,],
            'default' => SORT_ASC,
        ],
        'name01' => [
            'asc'     => ['name01' => SORT_ASC, 'name02' => SORT_ASC, ],
            'desc'    => ['name01' => SORT_DESC,'name02' => SORT_DESC,],
            'default' => SORT_ASC,
        ],
        'kana01' => [
            'asc'     => ['kana01' => SORT_ASC, 'kana02' => SORT_ASC, ],
            'desc'    => ['kana01' => SORT_DESC,'kana02' => SORT_DESC,],
            'default' => SORT_ASC,
        ],
        'addr01',
    ],
];

$customer = $this->context->module->purchase->customer;

?>

<div class="col-md-12">
<h2><?= $customer ? $customer->name : null ?>さんの住所録</h2>
</div>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'tableOptions' => ['class'=>'table table-condensed table-striped'],
    'layout'       => '{items}{pager}{summary}',
    'columns'   => [
        [
            'label'     => '',
            'format'    => 'html',
            'value'     => function($data){ return Html::a('決定', ['apply', 'id'=>$data->id, 'target'=>'delivery'],['class'=>'btn btn-xs btn-success']); },
            'contentOptions' => ['class'=>'text-center'],
        ],
        [
            'attribute' => 'name01',
            'label'     => $searchModel->getAttributeLabel('name'),
            'format'    => 'html',
            'value'     => function($data){ return $data->name; },
        ],
        [
            'attribute' => 'kana01',
            'label'     => $searchModel->getAttributeLabel('kana'),
            'format'    => 'html',
            'value'     => function($data){ return $data->kana; },
        ],
        [
            'attribute' => 'zip01',
            'label'     => $searchModel->getAttributeLabel('zip'),
            'format'    => 'html',
            'value'     => function($data){ return $data->zip01 . $data->zip02; },
        ],
        [
            'attribute' => 'pref_id',
            'label'     => $searchModel->getAttributeLabel('pref'),
            'format'    => 'html',
            'value'     => function($data){ return $data->pref
                        ? Html::tag('span', $data->pref->name, ['title'=>$data->addr])
                        : ''; },
            'filter'    => ArrayHelper::map(\common\models\Pref::find()->all(), 'pref_id', 'name'),
        ],
        [
            'attribute' => 'addr01',
            'label'     => $searchModel->getAttributeLabel('addr'),
            'format'    => 'html',
            'value'     => function($data){ return $data->addr01 . $data->addr02; },
        ],
        [
            'attribute' => 'tel01',
            'label'     => $searchModel->getAttributeLabel('tel'),
            'format'    => 'html',
            'value'     => function($data){ return Html::tag('code', $data->tel); },
        ],
        [
            'label'     => '',
            'format'    => 'html',
            'value'     => function($data){ return Html::a('修正', ['/customer-addrbook/update', 'id'=>$data->id],['class'=>'btn btn-xs btn-default']); },
            'contentOptions' => ['class'=>'text-center'],
        ],
    ],
]) ?>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'action' => ['/customer-addrbook/create'],
    'method' => 'post',
    'layout' => 'inline',
]) ?>

<?= $form->field($searchModel,'customer_id')->hiddenInput() ?>

<?php if($customer): ?>
<div class="col-md-12">
<?= Html::a('お届け先を追加',['/customer-addrbook/create','id'=>$customer->customer_id],['class'=>'btn btn-success']) ?>
</div>
<?php endif ?>

<?php $form->end() ?>

    <div class="col-md-12">
        &nbsp;
    </div>

<?php if($customer): ?>
    <div class="col-md-12">
    <div class="alert alert-info">
        <h4>本人ご登録住所</h4>
        <?= \yii\widgets\DetailView::widget([
            'model' => $customer,
            'options' => ['class' => 'table table-condensed text-right',
                          'id'    => 'customer-detail',
            ],
            'attributes' => [
                [
                    'attribute' => 'fulladdress',
                    'format'    => 'html',
                ],
                [
                    'attribute' => 'name',
                    'format'    => 'html',
                ],
                [
                    'attribute' => 'tel',
                ],
            ]
        ]) ?>        
    <?= Html::a('お届け先に指定',['apply','target'=>'customer','id'=>$customer->id],['class'=>'btn btn-primary']) ?>
    </div>
    </div>
<?php endif ?>
