<?php
/**
* $URL: https://tarax.toyouke.com/svn/MALL/common/modules/member/views/default/view.php $
* $Id: view.php 3056 2016-10-30 05:48:46Z mori $
* @var $dataProvider \yii\data\ActiveDataProvider
* @var $this         \yii\web\View
*/

use \yii\helpers\Html;
use \common\models\CustomerMembership;
use \common\models\Payment;

if($model->parent)
    $customer_id = $model->parent->customer_id;
else
    $customer_id = $model->customer_id;

$url = ['update','id'=>$model->customer_id];

if(Yii::$app->user->identity instanceof \backend\models\Staff)
    $url['pid'] = Payment::PKEY_BANK_TRANSFER;
?>

<div class="member-default-view">

    <?php $widget = \common\widgets\CustomerView::begin(['model'=>$model]) ?>

    <?= $widget->renderDetailView() ?>

    <?= $widget->renderMemberships() ?>

    <div class="col-md-12">
    <?php if('toranoko' == $this->context->id): ?>

        <?php if($model->isMember()): ?>
            <?php
            $query = CustomerMembership::find()
                   ->toranoko()
                   ->andWhere(['customer_id' => $customer_id])
                   ->andWhere(['>','expire_date', new \yii\db\Expression('DATE_ADD(NOW(), INTERVAL 1 YEAR)')]) // 365 日以上未来の会員権がある
                   ->orderBy(['expire_date' => SORT_DESC]);

            if($query->exists())
                $disabled = true;
            else
                $disabled = false;
            ?>
            <?= Html::a('延長する', $url, ['class'=>'btn btn-primary','disabled'=>$disabled]) ?>
        <?php elseif($model->wasMember()): ?>
            <?= Html::a('再開する', $url, ['class'=>'btn btn-success']) ?>

        <?php else: ?>
            <?= Html::a('入会する', $url, ['class'=>'btn btn-warning']) ?>

        <?php endif ?>

        <?= $widget->renderNewsletter() ?>

    <?php endif ?>
    </div>

    <?= $widget->renderPointings() ?>

</div>
