<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/member/views/default/attach-membercode.php $
 * $Id: attach-membercode.php 3039 2016-10-28 09:12:39Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Customer
 */

use yii\helpers\Html;
use yii\helpers\Url;

$title = "会員証NOを更新";

$this->params['breadcrumbs'][] = ['label' => $customer->name, 'url' => ['view', 'id' => $customer->customer_id]];
$this->params['breadcrumbs'][] = ['label' => $title];

$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels) . ' | ' . Yii::$app->name;

?>
<div class="customer-update">

    <h1><?= Html::encode($customer->name) ?></h1>

    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'action' => Url::current(['mcode'=>null]),
        'method' => 'get',
        'layout' => 'inline',
        'validateOnBlur'  => false,
        'validateOnChange'=> false,
        'validateOnSubmit'=> false,
    ]); ?>

    <?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label' => '古い会員証NO',
            'value' => $customer->code,
        ],
        [
            'label' => '新しい会員証NO',
            'format'=> 'raw',
            'value' => $form->field($model,'code')->textInput(['name'=>'mcode'])
        ],
        [
            'attribute' => 'pw',
            'format'=> 'raw',
            'value' => $form->field($model,'pw')->textInput(['name'=>'pw'])
                     . '&nbsp;'
                     . Html::submitButton('更新',['class'=>'btn btn-primary']),
        ],
    ],
    ])?>

    <?php $form->end() ?>

    <p class="help-block">
        注意：会員証NOを更新するには、通常、お客様本人より更新手数料をいただきます。お支払いは完了していますか？
    </p>

    <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>

</div>
