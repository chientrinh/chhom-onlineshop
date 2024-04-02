<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer/attach-membercode.php $
 * $Id: attach-membercode.php 3041 2016-10-29 01:15:53Z mori $
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

if($p = $customer->parent)
{
    Yii::$app->session->addFlash('error',"{$customer->name} さんは {$p->name} さんの家族会員です。 会員証NOの更新はできません。");
    return;
}
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
                     . Html::submitButton('更新',['class' => 'btn btn-primary']),
        ],
    ],
    ])?>

    <?= Html::a('更新せずに戻る', ['/recipe/create/search?target=client&mcode=' . $customer->membercode->code], ['class' => 'btn btn-danger']) ?>

    <?php $form->end() ?>

    <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>

</div>
