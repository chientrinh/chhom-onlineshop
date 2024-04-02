<?php
/**
* $URL: https://tarax.toyouke.com/svn/MALL/common/modules/member/views/default/create.php $
* $Id: create.php 3082 2016-11-16 05:31:11Z mori $
* @var $dataProvider \yii\data\ActiveDataProvider
* @var $this         \yii\web\View
*/

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use \yii\widgets\ActiveField;
use \common\models\Membership;
use \common\models\Payment;

$title = \yii\helpers\ArrayHelper::getValue($this->context->title, "会員");

$csscode = '
#createform-subscribe label:after {
  content: "";
  color: red;
}
';
$this->registerCss($csscode);

$sexes = \common\models\Sex::find()->where('sex_id in (0,1,2)')->all();
$sexes = ArrayHelper::map($sexes,'sex_id','name');
$sexes[0] = '';

$prefs = \common\models\Pref::find()->all();
$prefs = ArrayHelper::map($prefs,'pref_id','name');
$prefs[0] = '';
ksort($prefs);

$subscribes = \common\models\Subscribe::find()->all();
$subscribes = ArrayHelper::map($subscribes,'subscribe_id','name');

$memberships = [
    (object)[
        'model' => Membership::findOne(Membership::PKEY_TORANOKO_GENERIC),
        'html'  => Html::tag('p','年会費 &yen;2,000',['class'=>'col-md-offset-2'])
    ],
    (object)[
        'model' => Membership::findOne(Membership::PKEY_TORANOKO_NETWORK),
        'html'  => Html::tag('p','年会費 &yen;1,000',['class'=>'col-md-offset-2'])
    ],
];

if(date('m') < 5)
    $expire_year = date('Y');
else
    $expire_year = date('Y') + 1;
?>

<div class="customer-default-create col-md-12">
    <h1>
    <?= $title ?>
    </h1>

<?php $form = \yii\widgets\ActiveForm::begin([
    'id' => 'member-form',
    'validateOnBlur'   => false,
    'validateOnChange' => false,
    'validateOnSubmit' => false,
    'fieldConfig' => [
        'template' => '<div class="col-md-6">{input}{hint}{error}</div>',
        'inputOptions' => ['class' => 'form-control col-md-4'],
    ],
]) ?>

<?php if('toranoko' === $this->context->id): ?>
<div class="alert alert-warning">
    <p class="lead">
    <?= $model->getAttributeLabel('membership_id') ?>
    </p>
    <?php foreach($memberships as $mship): ?>
        <?= Html::activeRadio($model, 'membership_id', ['value'=> $mship->model->membership_id, 'label' => $mship->model->name . $mship->html, 'uncheck'=>null]) ?>
        <br>
    <?php endforeach ?>
    <p>
        ご入金が確認できた日に入金日を入会日として起算します。
        いまご入会いただくと<?= $expire_year ?>年５月４日まで有効です。
    </p>
    <?php if($model->hasErrors('membership_id')): ?>
        <p class="alert alert-danger">
            とらのこ会員種別を指定していください
        </p>
    <?php endif ?>
</div>

<div class="alert">
    <div class="required">
        <label>
            お支払い方法
        </label>
    </div>

    <?php if(Yii::$app->user->identity instanceof \backend\models\Staff): ?>
        <?= Html::tag('label',
                      Html::input('radio','payment_id',Payment::PKEY_BANK_TRANSFER,['checked'=>true])
                      . "銀行振込") ?>
    <?php else: ?>
        <?= Html::tag('label',
                      Html::input('radio','payment_id',Payment::PKEY_CASH,         ['checked'=>true])
                      . "現金") ?>
    <?php endif ?>
</div>
<?php endif ?>

<caption>
    <span style="color:red"> * </span>印は必須記入項目です。
</caption>

<?= $form->errorSummary($model) ?>
<?= \yii\widgets\DetailView::widget([
    'model'      => $model,
    'template'   => '<tr><th class="col-md-2 col-sm-2">{label}</th><td>{value}</td></tr>',
    'attributes' => [
        [
            'label'    => Html::tag('div',Html::tag('label',$model->getAttributeLabel('code'))),
            'format'   => 'raw',
            'value'    => '<div class="row"><div style="width:100%">'
                       . $form->field($mcode,'code')->textInput(['placeholder'=>$mcode->getAttributeLabel('code')])
                       . '</div><div style="width:100%">'
                       . $form->field($mcode,'pw')->textInput(['placeholder'=>'仮パスワード','style'=>'width:100%'])
                       . '</div></div>',
        ],
        [
            'label'    => Html::tag('div',Html::tag('label',$model->getAttributeLabel('name')),['class'=>"required"]),
            'format'   => 'raw',
            'value'    => '<div class="row"><div style="width:100%">'
                       . $form->field($model,'name01')->textInput(['placeholder'=>$model->getAttributeLabel('name01')])
                       . '</div><div style="width:100%">'
                       . $form->field($model,'name02')->textInput(['placeholder'=>$model->getAttributeLabel('name02'),'style'=>'width:100%'])
                       . '</div></div>',
        ],
        [
            'label'    => Html::tag('div',Html::tag('label',$model->getAttributeLabel('kana')),['class'=>"required"]),
            'format'   => 'raw',
            'value'    => '<div class="row"><div style="width:100%">'
                       . $form->field($model,'kana01')->textInput(['placeholder'=>$model->getAttributeLabel('kana01')])
                       . '</div><div style="width:100%">'
                       . $form->field($model,'kana02')->textInput(['placeholder'=>$model->getAttributeLabel('kana02')])
                       . '</div></div>',
        ],
        [
            'label'    => Html::tag('div',Html::tag('label',$model->getAttributeLabel('sex')),['class'=>"required"]),
            'format'   => 'raw',
            'value'    => $form->field($model,'sex_id')->dropDownList($sexes,['style'=>'width:40%']),
        ],
        [
            'label'    => Html::tag('div',Html::tag('label',$model->getAttributeLabel('birth')),['class'=>"required"]),
            'format'   => 'raw',
            'value'    => $form->field($model, 'birth')->textInput([
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $model,
                    'attribute'=>'birth',
                    'language' => 'ja',
                    'dateFormat' => 'yyyy-MM-dd',
                    'clientOptions' =>[
                        'dateFormat'    => 'yyyy-mm-dd 00:00:00',
                        'language'      => Yii::$app->language,
                        'country'       => 'JP',
                        'showAnim'      => 'fold',
                        'yearRange'     => '1900:c',
                        'changeMonth'   => true,
                        'changeYear'    => true,
                        'autoSize'      => true,
                        'showOn'        => "focus",
                        'htmlOptions'=>[
                            'style'=>'width:80px;',
                            'font-weight'=>'x-small',
                        ],],
                ]),
            ]),
        ],
        [
            'label'    => Html::tag('div',Html::tag('label',$model->getAttributeLabel('zip')),['class'=>$model->isAttributeRequired('zip01') ? "required" : null]),
            'format'   => 'raw',
            'value'    => '<div class="col-md-3 col-sm-4">'
            . implode('</div><div class="col-md-3 col-sm-4">', [
                $form->field($model,'zip01',['template'=>'{input}{hint}{error}'])->textInput(['class'=>'form-control js-zenkaku-to-hankaku']),
                $form->field($model,'zip02',['template'=>'{input}{hint}{error}'])->textInput(['class'=>'form-control js-zenkaku-to-hankaku']),
                Html::submitButton('住所を検索',['name'=>'scenario','value'=>'zip2addr','class'=>'btn btn-primary'])
            ])
                .'</div>',
        ],
        [
            'label'    => Html::tag('div',Html::tag('label',$model->getAttributeLabel('addr')),['class'=>"required"]),
            'format'   => 'raw',
            'value'    => $form->field($model,'pref_id')->dropDownList($prefs, ['style'=>'width:50%'])
                        . $form->field($model,'addr01',['template'=>'<div class="col-md-12 col-sm-8">{input}{hint}{error}</div>'])->textInput(['placeholder'=>$model->getAttributeLabel('addr01')])
                        . $form->field($model,'addr02',['template'=>'<div class="col-md-12 col-sm-8">{input}{hint}{error}</div>'])->textInput(['placeholder'=>$model->getAttributeLabel('addr02')])
        ],
        [
            'label'    => Html::tag('div',Html::tag('label',$model->getAttributeLabel('tel')),['class'=>"required"]),
            'format'   => 'raw',
            'value'    => '<div class="col-md-2 col-sm-2">'
                        . implode('</div><div class="col-md-2 col-sm-2">',[
                $form->field($model,'tel01',['template'=>'{input}{hint}{error}'])->textInput(['placeholder'=>$model->getAttributeLabel('tel01'),'class'=>'form-control js-zenkaku-to-hankaku']),
                $form->field($model,'tel02',['template'=>'{input}{hint}{error}'])->textInput(['placeholder'=>$model->getAttributeLabel('tel02'),'class'=>'form-control js-zenkaku-to-hankaku']),
                $form->field($model,'tel03',['template'=>'{input}{hint}{error}'])->textInput(['placeholder'=>$model->getAttributeLabel('tel03'),'class'=>'form-control js-zenkaku-to-hankaku'])
                        ])
                      . '</div>',
        ],
        [
            'label'    => Html::tag('div',Html::tag('label',$model->getAttributeLabel('email')),[]),
            'format'   => 'raw',
            'value'    => $form->field($model,'email'),
        ],
        [
            'label'    => Html::tag('div',Html::tag('label',$model->getAttributeLabel('subscribe')),['class'=>"required"]),
            'format'   => 'raw',
            'value'    => $form->field($model,'subscribe')->radioList($subscribes,['separator'=>'<br>']),
        ],
    ]
]) ?>

<?= Html::submitButton('申し込む',['name'=>'scenario','value'=>'default','class'=>'btn btn-warning','onclick'=>"this.form.action='create?force=0'"]) ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
<?= Html::submitButton('強制登録',['name'=>'scenario','value'=>'default','class'=>'btn btn-danger','onclick'=>"this.form.action='create?force=1'"]) ?>
<?php endif ?>
<?php $form->end() ?>

</div>
