<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/offer-seasonal/_form.php $
 * $Id: _form.php 3852 2018-04-26 04:54:39Z mori $
 */

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

$branches = \yii\helpers\ArrayHelper::map(\common\models\Branch::find()->all(), 'name','branch_id');
$grades   = \yii\helpers\ArrayHelper::map(\common\models\CustomerGrade::find()->all(), 'name','grade_id');

$branches['全拠点'] = -1;
asort($branches);
$branches     = array_flip($branches);

$grades['全員'] = 0;
asort($grades);
$grades     = array_flip($grades);

?>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'action' => [
        $model->isNewRecord ? '/offer-seasonal/create' : '/offer-seasonal/update',
        'id' => $model->seasonal_id,
        'ean13' => $model->ean13,
    ],
    'id'     => 'offer-seasonal-form',
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{hint}\n{error}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-4',
            'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-sm-8',
            'error' => '',
            'hint' => '',
        ],
],
]) ?>

<?= $form->field($model, 'ean13')->textInput() ?>
<?= $form->field($model, 'branch_id')->dropDownList($branches) ?>
<?= $form->field($model, 'grade_id')->dropDownList($grades) ?>
<?= $form->field($model, 'discount_rate')->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
<?= $form->field($model, 'point_rate')->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
<?= $form->field($model, 'start_date')
         ->widget(\yii\jui\DatePicker::className(),
                  [
                      'language' => Yii::$app->language,
                      'clientOptions' =>[
                          'dateFormat'    => 'yy-m-d 00:00:00',
                          'language'      => Yii::$app->language,
                          'country'       => 'JP',
                          'showAnim'      => 'fold',
                          'yearRange'     => 'c-10:c+10',
                          'changeMonth'   => true,
                          'changeYear'    => true,
                          'autoSize'      => true,
                          'showOn'        => "button",
                          'htmlOptions'=>[
                              'style'=>'width:80px;',
                              'font-weight'=>'x-small',
                          ],]]) ?>
<?= $form->field($model, 'end_date')
         ->widget(\yii\jui\DatePicker::className(),
                  [
                        'language' => Yii::$app->language,
                        'clientOptions' =>[
                        'dateFormat'    => 'yy-m-d 23:59:59',
                        'language'      => Yii::$app->language,
                        'country'       => 'JP',
                        'showAnim'      => 'fold',
                        'yearRange'     => 'c-10:c+10',
                        'changeMonth'   => true,
                        'changeYear'    => true,
                        'autoSize'      => true,
                        'showOn'        => "button",
                        'htmlOptions'=>[
                            'style'=>'width:80px;',
                            'font-weight'=>'x-small',
                            'class' => 'form-group',
                        ],]]) ?>

<?= Html::submitButton($model->isNewRecord ? "追加" : "修正", ['class'=>'btn btn-success']) ?>
