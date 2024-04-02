<?php

use yii\helpers\Html;
use common\models\Customer;
use common\models\CustomerMembership;
use common\models\Membership;

/**
 *
 * @var $this yii\web\View
 * @var $model common\models\Recipe
 */

$filter = [
    \common\models\Recipe::STATUS_INIT    => "発行",
    \common\models\Recipe::STATUS_PREINIT    => "仮発行",
    \common\models\Recipe::STATUS_SOLD    => "購入",
    \common\models\Recipe::STATUS_EXPIRED => "無効",
    \common\models\Recipe::STATUS_CANCEL  => "キャンセル",
    \common\models\Recipe::STATUS_VOID    => "破棄",
];

$this->params['breadcrumbs'][] = ['label' => sprintf('%06d',$model->recipe_id)];
$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels) . ' | ' . Yii::$app->name;

?>
<div class="recipe-create">

    <h1>適用書を作成</h1>

<div class="recipe-form">

    <?php $form = \yii\widgets\ActiveForm::begin([
        'action' => ['create/add','target'=>'homoeopath', 'new' => true],
        'enableClientScript' => false,
    ]); ?>

    <?= \yii\grid\GridView::widget([
        'caption' => 'ホメオパス',
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => Customer::find()->active()->andWhere([
                'customer_id' => CustomerMembership::find()->select('customer_id')->andWhere([
                    'membership_id' => Membership::PKEY_CENTER_HOMOEOPATH
                ])
            ]),
            'pagination' => [
                'pagesize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'name' => [
                        'asc'  => ['name01'=>SORT_ASC, 'name02'=>SORT_ASC],
                        'desc' => ['name01'=>SORT_DESC,'name02'=>SORT_DESC],
                    ],
                    'kana' => [
                        'asc'  => ['kana01'=>SORT_ASC, 'kana02'=>SORT_ASC],
                        'desc' => ['kana01'=>SORT_DESC,'kana02'=>SORT_DESC],
                    ],
                    'email',
                    'defaultOrder' => ['kana01' => SORT_ASC,'kana02' => SORT_ASC],
                ],
            ],
        ]),
        'tableOptions' => ['class'=>'table table-condensed table-striped'],
        'layout' => '{items}{pager}',
        'columns' => [
            [
                'attribute'=>'name',
                'format'   => 'raw',
                'value'    => function($data)use($form,$model){ return $form->field($model,'homoeopath_id')->radio(['uncheck'=>null,'label'=>$data->homoeopathname,'name'=>'id','value'=>$data->customer_id]); }
            ],
            'kana',
            'email',
            ]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '次へ進む' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php $form::end(); ?>

</div>

</div>
