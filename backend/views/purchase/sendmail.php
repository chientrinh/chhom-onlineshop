<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/purchase/sendmail.php $
 * $Id: sendmail.php 2830 2016-08-10 04:46:50Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Purchase
 */

use \Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use common\models\Branch;
use common\models\Company;

// generate $senders for DropDownList
$user = Yii::$app->user->identity;
$q1 = Company::find()->select('email')
                     ->where(['company_id'=>$user->getCompany()->select('company_id')]);
$q2 = Branch::find()->select('email')
                    ->where( ['branch_id' =>$user->getBranches()->select('branch_id')]);
$q1->union($q2)->distinct()->indexBy('email')->orderBy('email');

$senders = ArrayHelper::getColumn($q1->all(), 'email');

$senders[$user->email] = $user->email;

?>

<div class="purchase-sendmail">

    <h1>メール送信</h1>

    <?= \yii\widgets\DetailView::widget([
            'model'   => $purchase,
            'options' => ['class' => 'table table-condensed table-bordered detail-view'],
            'attributes' => [
                [
                    'attribute'=> 'purchase_id',
                    'format' => 'html',
                    'value'  => Html::a($purchase->purchase_id, ['view','id'=>$purchase->purchase_id]),
                ],
                [
                    'label'  => '注文者',
                    'value'  => ($c = $purchase->customer) ? $c->name : null,
                ],
            ],
        ]) ?>

    <?php $form = \yii\bootstrap\ActiveForm::begin() ?>

    <label class="control-label"><?= $model->getAttributeLabel('recipient') ?></label>
    <p>
        <?= $model->recipient ?>
    </p>
    
    <?= $form->field($model, 'subject') ?>

    <?= $form->field($model, 'content')->textArea(['rows'=> 12 ]) ?>

    <?= $form->field($model, 'sender')->dropDownList($senders) ?>

    <?= Html::submitbutton('送信',['class'=>'btn btn-danger']) ?>

    <?php $form->end() ?>

    <?= $form->errorSummary($model) ?>

</div>
