<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/views/customer/create-child.php $
 * $Id: child.php 3109 2016-11-25 04:20:50Z mori $
 *
 * $title string
 */

use \yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;

$title = '家族会員を追加';
$this->params['breadcrumbs'][] = ['label' => $title];

$sexes = \yii\helpers\ArrayHelper::map(\common\models\Sex::find()->where(['sex_id' => [0,1,2]])->all(), 'sex_id', 'name');
$sexes[0] = "";
?>
<script>
    function create_continue() {
        // hiddenタグを作成
        $("<input>", {
            type: 'hidden',
            name: 'continue_flg',
            value: '1'
        }).appendTo('form#form-create-child');
        $('form#form-create-child').submit();
    }
</script>

<div class="cart-view">
  <div class="col-md-9">
	<h2><span><?= $title ?></span></h2>
	<p class="windowtext">
      下記項目にご入力ください。「※」印は入力必須項目です。<br>
    </p>

    <?php $form = ActiveForm::begin([
      'id' => 'form-create-child',
      'layout' => 'default',
      'enableClientValidation' => false,
      'validateOnBlur'   => false,
      'validateOnChange' => false,
      'validateOnSubmit' => false,
      'fieldConfig'      => ['template' => '{input}{error}'],
    ]);?>

    <table summary="<?= $title ?>" id="FormTable" class="table table-bordered">
        <tbody>
            <tr>
                <th>
                    <div class="required">
                        <label>お名前</label>
                    </div>
                </th>
                <td>
                    <?= Html::tag('div', $form->field($model, 'name01')->textInput(['maxlength' => 255, 'placeholder' => '姓']), ['class'=>'col-md-6']) ?>
                    <?= Html::tag('div', $form->field($model, 'name02')->textInput(['maxlength' => 255, 'placeholder'=>'名']), ['class'=>'col-md-6']) ?>
                </td>
            </tr>
            <tr>
                <th>
                    <div class="required">
                        <label>お名前（ふりがな）</label>
                    </div>
                </th>
                <td>
                    <?= Html::tag('div', $form->field($model, 'kana01')->textInput(['maxlength' => 255, 'placeholder' => 'せい']), ['class'=>'col-md-6']) ?>
                    <?= Html::tag('div', $form->field($model, 'kana02')->textInput(['maxlength' => 255, 'placeholder'=>'めい']), ['class'=>'col-md-6']) ?>
                </td>
            </tr>
            <tr>
                <th>
                    <div class="required">
                        <label>性別</label>
                    </div>
                </th>
                <td>
                    <?= $form->field($model, 'sex_id')->dropDownList($sexes, ['style'=>'width:40%']) ?>
                </td>
            </tr>
            <tr>
                <th>
                    <div>
                        <label><?= $model->getAttributeLabel('birth') ?></label>
                    </div>
                </th>
                <td>
                    <?= Html::tag('div',
                        $form->field($model, 'birth')->widget(DatePicker::className(),[
                            'language' => Yii::$app->language,
                            'clientOptions' => [
                            'dateFormat'    => 'd-m-yy',
                            'language'      => Yii::$app->language,
                            'country'       => 'JP',
                            'showAnim'      => 'fold',
                            'yearRange'     => 'c-100:c+25',
                            'changeMonth'   => true,
                            'changeYear'    => true,
                            'autoSize'      => true,
                            'showOn'        => "button",
                            'htmlOptions' => [
                                'class' => 'form-control',
                                'font-weight' => 'x-small'
                            ]
                        ]]),
                        ['class' => 'col-md-4']) ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="form-group">
        <?php if($model->isExpired()): ?>
            <p class="alert alert-danger">無効な顧客です</p>
        <?php else: ?>

            <?= Html::submitButton("追加して戻る", [
                'class' => 'btn btn-primary',
                'name'  => 'scenario',
                'value' => 'default',
            ]) ?>
            <?= Html::submitButton("続けて追加する", [
                'class' => 'btn btn-default',
                'name'  => 'scenario',
                'value' => 'default',
                'onclick' => 'return create_continue();'
            ]) ?>
        <?php endif ?>
        <?= Html::a('戻る', ['/sodan/client/create', 'client_id' => $parent_id], ['class' => 'btn btn-danger']) ?>
    </div><!--form-group-->
    <?php $form->end(); ?>
  </div><!--col-md-12-->
  </div><!--row column01-->
</div>
