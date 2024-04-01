<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer/unite-customer.php $
 *
 * @var $this yii\web\View
 * @var $model common\models\Customer
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$title = "顧客統合";
$this->params['breadcrumbs'][] = ['label' => $title];

$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels) . ' | ' . Yii::$app->name;
?>

<div class="unite-customer">

    <h1>顧客統合</h1>
    
    <p class="help-block">顧客を別の顧客に統合します。ポイントは統合先の顧客に加算されます。</p>
    
    <?php if (isset($old_customer) && isset($new_customer) && !$error_flg): ?>
        <h3 class="text-danger" style="text-align: center;">この内容で顧客を統合してよろしいですか？<br>※統合元の顧客は無効になります</h3>
    <?php endif; ?>

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{input}\n{hint}\n{error}",
            'horizontalCssClasses' => [
                'label'   => 'col-sm-4',
                'offset'  => 'col-sm-offset-4',
                'wrapper' => 'col-sm-8'
            ],
        ],
        'validateOnBlur'  => false,
        'validateOnChange'=> false,
        'validateOnSubmit'=> false
    ]); ?>

        <div class="col-md-12" style="margin-bottom: 10px">
            <div class="col-md-5">
                <label class="control-label">統合する顧客ID</label>
                <?= Html::textInput("old_customer_id", Yii::$app->request->post('old_customer_id'), ['class' => 'form-control']) ?>
                <?php if (isset($old_customer) && !$error_flg): ?>
                    <?= \yii\widgets\DetailView::widget([
                        'model' => $old_customer,
                        'options' => ['class' => 'table table-condensed table-striped table-bordered', 'style' => 'margin-top:15px;'],
                        'attributes' => [
                            'name',
                            'kana',
                            [
                                'attribute'=> 'grade',
                                'format'   => 'html',
                                'value'    => Html::tag('strong',$old_customer->grade->longname),
                            ],
                            'point:integer',
                            [
                                'attribute'=>'code',
                                'format'   =>'html',
                                'value'    => $old_customer->code
                                            . ((($c = $old_customer->membercode) && ($c->code == $old_customer->code) && $c->isVirtual()) ? Html::tag('strong','&nbsp;会員証は未発行です',['class'=>'text-danger']) : null) ,
                            ],
                            'fulladdress',
                            'tel',
                            [
                                'attribute'=>'birth',
                                'value'=> preg_match('/0000/', $old_customer->birth)
                                ? null
                                : Yii::$app->formatter->asDate($old_customer->birth, 'full'),
                            ],
                            'email',
                        ],
                    ]) ?>
                <?php endif; ?>
            </div>
            <div class="col-md-1">
                <?= Html::tag('i', '', ['style' => 'font-size:3.0em;margin:22px 0 0 30%;', 'class' => 'glyphicon glyphicon-arrow-right']) ?>
            </div>
            <div class="col-md-5">
                <label class="control-label">統合先の顧客ID</label>
                <?= Html::textInput("new_customer_id", Yii::$app->request->post('new_customer_id'), ['class' => 'form-control']) ?>
                <?php if (isset($new_customer) && !$error_flg): ?>
                    <?= \yii\widgets\DetailView::widget([
                        'model' => $new_customer,
                        'options' => ['class' => 'table table-condensed table-striped table-bordered', 'style' => 'margin-top:15px;'],
                        'attributes' => [
                            'name',
                            'kana',
                            [
                                'attribute'=> 'grade',
                                'format'   => 'html',
                                'value'    => Html::tag('strong',$new_customer->grade->longname),
                            ],
                            'point:integer',
                            [
                                'attribute'=>'code',
                                'format'   =>'html',
                                'value'    => $new_customer->code
                                            . ((($c = $new_customer->membercode) && ($c->code == $new_customer->code) && $c->isVirtual()) ? Html::tag('strong','&nbsp;会員証は未発行です',['class'=>'text-danger']) : null) ,
                            ],
                            'fulladdress',
                            'tel',
                            [
                                'attribute'=>'birth',
                                'value'=> preg_match('/0000/', $new_customer->birth)
                                ? null
                                : Yii::$app->formatter->asDate($new_customer->birth, 'full'),
                            ],
                            'email',
                        ],
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-12 form-group">
            <?php if (isset($old_customer) && isset($new_customer) && !$error_flg): ?>
                <?= Html::hiddenInput('unite_flg', '1') ?>
                <?= Html::submitButton('統合する', ['class' => 'btn btn-success']) ?>
                <?= Html::a('戻る', ['unite-customer'], ['class' => 'btn btn-danger']) ?>
            <?php else: ?>
                <?= Html::hiddenInput('unite_flg', '0') ?>
                <?= Html::submitButton('確認', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('戻る', ['index'], ['class' => 'btn btn-danger']) ?>
            <?php endif; ?>            
        </div>        
    <?php $form->end() ?>

    <?php if (isset($old_customer) && $error_flg): ?>
        <?= Html::errorSummary($old_customer, ['class' => 'alert alert-danger']) ?>
    <?php endif; ?>

</div>
