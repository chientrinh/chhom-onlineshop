<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/backend/views/pointing/__customer.php $
 * $Id: __customer.php 2709 2016-07-14 02:26:14Z mori $
 *
 * @param $model Customer
 */

use \yii\helpers\Html;
use common\models\Customer;

if('atami' == $this->context->id)
    $validateAttr = ['name01','name02','kana01','kana02','zip01','zip02','pref_id','addr01','addr02','tel01','tel02','tel03'];
else
    $validateAttr = ['kana01','kana02','tel01','tel02','tel03'];

?>

<div class="well">
    <p>お客様情報</p>
    <?php if ($parent_flg):?>
        <p class="hint-block">※家族会員のため、親会員をセットしました</p>
    <?php endif;?>
    <?php if ($model): ?>
        <?= \yii\widgets\DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-condensed text-right',
                          'id'    => 'customer-detail',
            ],
            'attributes' => [
                [
                    'attribute' => 'name',
                    'format'    => 'html',
                    'value'     => $model->name
                ],
                [
                    'attribute' => 'point',
                    'format'    => 'html',
                    'value'     => number_format(Customer::findOne($model->customer_id)->currentPoint())
                ],
                [
                    'attribute' => 'addr',
                    'format'    => 'text',
                    'value'     => $model->pref->name . $model->addr01 . $model->addr02
                ],
                [
                    'attribute' => 'tel',
                    'format'    => 'html',
                    'value'     => "{$model->tel01}-{$model->tel02}-{$model->tel03}"
                ],
            ]
        ]) ?>
        <?php $model->validate($validateAttr); echo \yii\bootstrap\ActiveForm::begin()->errorSummary($model,['header'=>'お客様の登録が完了していません。以下の項目を修正してください','footer'=>Html::a('修正する',['/customer/update','id'=>$model->customer_id,'scenario'=>'emergency'],['class'=>'btn btn-warning'])]); \yii\bootstrap\ActiveForm::end(); ?>
    <?php endif ?>

</div>
