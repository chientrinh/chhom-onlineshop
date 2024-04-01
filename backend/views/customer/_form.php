<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer/_form.php $
 * $Id: _form.php 4122 2019-03-13 07:20:35Z kawai $
 *
 * @var $this     yii\web\View
 * @var $model    common\models\Customer
 * @var $scenario string
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use common\models\Customer;

$genders   = \yii\helpers\ArrayHelper::map(\common\models\Sex::find()->all(), 'sex_id', 'name');
$prefs     = \yii\helpers\ArrayHelper::map(\common\models\Pref::find()->all(), 'pref_id', 'name');
$radioList = \yii\helpers\ArrayHelper::map(\common\models\Subscribe::find()->all(), 'subscribe_id','name');

if($model->parent ||
   isset($parent) ||
   ($model::SCENARIO_CHILDMEMBER == $model->scenario))
       $parent = ($model->parent ? $model->parent : $parent);
else
    $parent = null;
?>

<div class="customer-form">

    <?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{input}\n{hint}\n{error}",
        'horizontalCssClasses' => [
            'label'   => 'col-sm-4',
            'offset'  => 'col-sm-offset-4',
            'wrapper' => 'col-sm-8',
            'error'   => '',
            'hint'    => '',
        ],
    ],
    'validateOnBlur'  => false,
    'validateOnChange'=> false,
    'validateOnSubmit'=> false,
    ]); ?>

    <?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'attribute' => 'grade',
            'format'    => 'raw',
            'value'     => $model->grade->name . Html::tag('span','&nbsp;'.$model->getAttributeHint('grade'),['class'=>'help-block']),
        ],
        [
            'attribute' => 'code',
            'format'    => 'raw',
            'value'     => $model->code . ' ' . Html::a(Html::tag('small','更新'),['attach-membercode','id'=>$model->customer_id],['class'=>'btn btn-xs btn-default']).Html::tag('span',$model->getAttributeHint('code'),['class'=>'help-block']),
            'visible'   => ! $model->isNewRecord,
        ],
        [
            'attribute' => 'name',
            'format'=> 'raw',
            'value' => Html::tag('div', $form->field($model, 'name01')->textInput(['maxlength' => 255]), ['class'=>'col-md-6','placeholder'=>'姓'])
                     . Html::tag('div', $form->field($model, 'name02')->textInput(['maxlength' => 255]), ['class'=>'col-md-6']),
            'visible' => (Customer::SCENARIO_EMERGENCY != $model->scenario),
        ],
        [
            'attribute' => 'kana',
            'format'=> 'raw',
            'value' => Html::tag('div', $form->field($model, 'kana01')->textInput(['maxlength' => 255]), ['class'=>'col-md-6'])
                     . Html::tag('div', $form->field($model, 'kana02')->textInput(['maxlength' => 255]), ['class'=>'col-md-6'])
        ],
        [
            'attribute' => 'homoeopath_name',
            'format'=> 'raw',
            'value' => Html::tag('div', $form->field($model, 'homoeopath_name')->textInput(['maxlength' => 255]), ['class'=>'col-md-6'])
        ],
        [
            'attribute' => 'sex',
            'format'=> 'raw',
            'value' => Html::tag('div',$form->field($model, 'sex_id')->dropDownList($genders),['class'=>'col-md-3']),
            'visible' => (Customer::SCENARIO_EMERGENCY != $model->scenario),
        ],
        [
            'attribute' => 'birth',
            'format'=> 'raw',
            'value' => Html::tag('div',
                    $form->field($model, 'birth')->widget(DatePicker::className(),[
                        'language' => Yii::$app->language,
                        'clientOptions' =>[
                        'dateFormat'    => 'd-m-yy',
                        'language'      => Yii::$app->language,
                        'country'       => 'JP',
                        'showAnim'      => 'fold',
                        'yearRange'     => 'c-100:c+25',
                        'changeMonth'   => true,
                        'changeYear'    => true,
                        'autoSize'      => true,
                        'showOn'        => "button",
                        'htmlOptions'=>[
                            'class'=>'form-control',
                            'style'=>'width:80px;',
                            'font-weight'=>'x-small',
                        ],
                    ],
                    ]),
                                 ['class'=>'col-md-4']),
            'visible' => (Customer::SCENARIO_EMERGENCY != $model->scenario),
        ],
        [
            'attribute' => 'email',
            'format'=> 'raw',
            'value' => $form->field($model, 'email',['options'=>['class'=>'md-col-6']])->textInput(['maxlength' => 255]),
            'visible' => (Customer::SCENARIO_EMERGENCY != $model->scenario),
        ],
        [
            'attribute' => 'addr',
            'format'    => 'raw',
            'value'     => Html::tag('div', $form->field($model, 'zip01')->textInput(['placeholder'=>'〒 上3桁','class'=>'form-control js-zenkaku-to-hankaku']), ['class'=>'col-md-3'])
                        . Html::tag('div', $form->field($model, 'zip02')->textInput(['placeholder'=>'〒 下4桁','class'=>'form-control js-zenkaku-to-hankaku']), ['class'=>'col-md-3'])
                        . Html::tag('div', Html::submitButton('住所検索',['name'=>'scenario','value'=>'zip2addr','class'=>'btn btn-primary'],['class'=>'col-md-3']))
                        . '<div class="col-md-12">'
            . $form->field($model, 'pref_id')->dropDownList($prefs)
            . $form->field($model, 'addr01')->textInput(['maxlength' => 255])
            . $form->field($model, 'addr02')->textInput(['maxlength' => 255])
            . '</div>',
            'visible' => ! $parent && (Customer::SCENARIO_EMERGENCY != $model->scenario),
        ],
        [
            'attribute' => 'tel',
            'format'    => 'raw',
            'value'     => Html::tag('div', $form->field($model, 'tel01')->textInput(['class'=>'form-control js-zenkaku-to-hankaku']), ['class'=>'col-md-3'])
                        . Html::tag('div', $form->field($model, 'tel02')->textInput(['class'=>'form-control js-zenkaku-to-hankaku']), ['class'=>'col-md-3'])
                        . Html::tag('div', $form->field($model, 'tel03')->textInput(['class'=>'form-control js-zenkaku-to-hankaku']), ['class'=>'col-md-3']),
            'visible' => true,
        ],
        [
            'attribute' => 'subscribe',
            'format'    => 'raw',
            'value'     => Html::tag('div',$form->field($model, 'subscribe')->radioList($radioList),['class'=>'col-md-12']),
            'visible'   => (! $parent || $model->email) && (Customer::SCENARIO_EMERGENCY != $model->scenario),
        ],
        [
            'attribute' => 'campaign_code',
            'format'    => 'raw',
            'value'     => Html::textInput("campaign_code", Yii::$app->request->post('campaign_code'), ['class' => 'form-control', 'style' => 'width:30%;'])
        ],
    ]]);?>

<?php if($parent): ?>
    <div class="alert alert-warning">
        この人は
         <?= Html::a($parent->name, ['view','id'=>$parent->customer_id]) ?>
         さんの家族会員です（親会員と同居していることが前提）。
         このページで連絡先の修正はできません。

        <?= Html::a("親子分離", ['independent', 'id' => $model->customer_id,], [
            'class' => 'btn btn-default',
            'title' => '家族会員をやめて本会員になります',
        ]) ?>
    </div>
<?php endif ?>

    <div class="col-md-12 form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '修正', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php if (isset($mode) && $mode === 'client'): ?>
            <input type="hidden" name="target" value="child">
            <?= Html::submitButton('子会員を作成する', ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        <span class="pull-right">

            <?php if(! $model->isNewRecord): ?>
            <?php if($model->children): ?>
            <?= Html::a("親子入れ替え", ['swap', 'id' => $model->customer_id,], [
                'class' => 'btn btn-default',
                'title' => '家族に本会員の役割をゆずります',
            ]) ?>
            <?php endif ?>
                <?php if ($model->isExpired()): ?>
                    <?= Html::a("有効にする", ['activate', 'id' => $model->customer_id], [
                        'class' => 'btn btn-success',
                        'data' => [
                            'confirm' => "ほんとうにこの顧客を有効にしますか",
                        ],
                    ]) ?>
                <?php else: ?>
                    <?= Html::a("無効にする", ['expire', 'id' => $model->customer_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => "ほんとうにこの顧客を無効にしますか",
                        ],
                    ]) ?>
                <?php endif;?>
            <?php endif ?>

        </span>
    </div>
    <?php if (isset($mode) && $mode): ?>
        <input type="hidden" name="mode" value="<?php echo $mode;?>">
    <?php endif; ?>

    <?php ActiveForm::end(); ?>

</div>
