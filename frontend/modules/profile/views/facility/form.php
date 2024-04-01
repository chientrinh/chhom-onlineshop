<?php
/**
 * $URL  $
 * $Id: form.php 3987 2018-08-17 02:30:40Z mori $
 *
 * $model \common\models\AgencyOffice
 */
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

$prefs = \yii\helpers\ArrayHelper::map(\common\models\Pref::find()->where(['pref_id'=>range(1,47)])->all(), 'pref_id', 'name');
array_unshift($prefs, "都道府県を選択");

?>

<div class="cart-view">

    <h1 class="mainTitle">マイページ</h1>
    <p class="mainLead">このページでは出店企業・団体からのご優待や特典リンクをご案内します。</p>

    <div class="col-md-3">
        <div class="Mypage-Nav">
            <div class="inner">
                <h3>Menu</h3>
                <?= Yii::$app->controller->nav->run() ?>
            </div>
        </div>
    </div>

    <div class="col-md-9">

        <h2><span>提携施設</span></h2>

        <p class="help-block">
            豊受モール出店企業と提携いただいている場合、施設の情報を入力すると公開されます。
        </p>

        <?php $form = \yii\bootstrap\ActiveForm::begin([
            'id' => 'form-office-create',
            'layout' => 'default',
            'validateOnBlur'  => false,
            'validateOnChange'=> false,
            'validateOnSubmit'=> false,
            'fieldConfig'     => ['template'=>'{input}{hint}{error}'],
        ]) ?>

<table id="FormTable" class="table table-bordered">
<tbody>

    <tr>
    <th><div class="required"><label><?= $model->getAttributeLabel('name') ?></label></div></th>
    <td>
    <?= $form->field($model, 'name') ?>
    </tr>

    <tr>
    <th><div class="<?= $model->isAttributeRequired('email') ? 'required' : null ?>">
        <label><?= $model->getAttributeLabel('email') ?></label>
    </div></th>
    <td>
    <?= $form->field($model, 'email') ?>
    </tr>

    <tr>
    <th><div class="<?= $model->isAttributeRequired('url') ? 'required' : null ?>">
        <label><?= $model->getAttributeLabel('url') ?></label>
    </div></th>
    <td>
    <?= $form->field($model, 'url') ?>
    </tr>

    <tr>
    <th><div class="<?= $model->isAttributeRequired('title') ? 'required' : null ?>">
        <label><?= $model->getAttributeLabel('title') ?></label>
    </div></th>
    <td>
    <?= $form->field($model, 'title') ?>
    </tr>

    <tr>
    <th><div class="<?= $model->isAttributeRequired('summary') ? 'required' : null ?>">
        <label><?= $model->getAttributeLabel('summary') ?></label>
    </div></th>
    <td>
    <?= $form->field($model, 'summary')->textArea(['rows'=>7]) ?>
    </tr>

    <tr>
    <th><div>
    <label>郵便番号</label>
    </div></th>
    <td><div class="field-changeform-zip"> <span class="float-box2">〒</span>
    <?= $form->field($model, 'zip01',['options'=>['class'=>'Zip']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
    <span class="float-box">-</span>
    <?= $form->field($model, 'zip02',['options'=>['class'=>'Zip']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
    &nbsp;
<button type="submit" class="btn btn-primary" name="scenario" value="zip2addr">住所を検索</button>
    &nbsp;
<a href="http://www.post.japanpost.jp/zipcode/" class="btn btn-default pull-right" target="_blank"><span class="fs12">郵便番号検索へ</span></a>
    <p class="help-block">
        公開したくない場合は空白にしてください
    </p>
    </div></td>
    </tr>

    <tr>
    <th><div>
    <label>住所</label>
    </div></th>
    <td>
    <?= $form->field($model, 'pref_id')->dropDownList($prefs) ?>
    <label class="control-label" for="signupform-addr01">市区町村名（例：千代田区神田神保町）</label>
    <?= $form->field($model, 'addr01') ?>

    <label class="control-label" for="signupform-addr02">番地・ビル名（例：1-3-5）</label>
    <?= $form->field($model, 'addr02') ?>
    <p class="help-block">
        公開したくない部分は空白にしてください
    </p>
    </td>
    </tr>

    <tr>
    <th><div>
    <label>電話番号</label>
    </div></th>
    <td>
    <?= $form->field($model, 'tel01',['options'=>['class'=>'Tel']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
    <span class="float-box">-</span>
    <?= $form->field($model, 'tel02',['options'=>['class'=>'Tel']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
    <span class="float-box">-</span>
    <?= $form->field($model, 'tel03',['options'=>['class'=>'Tel']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
    <p class="help-block">
        公開したくない場合は空白にしてください
    </p>
    </td>
    </tr>

    <tr>
    <th><div>
    <label>FAX番号</label>
    </div></th>
    <td>
    <?= $form->field($model, 'fax01',['options'=>['class'=>'Tel']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
    <span class="float-box">-</span>
    <?= $form->field($model, 'fax02',['options'=>['class'=>'Tel']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
    <span class="float-box">-</span>
    <?= $form->field($model, 'fax03',['options'=>['class'=>'Tel']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
    <p class="help-block">
        公開したくない場合は空白にしてください
    </p>
    </td>
    </tr>

    <tr>
    <th><div class="<?= $model->isAttributeRequired('pub_date') ? 'required' : null ?>">
        <label><?= $model->getAttributeLabel('pub_date') ?></label>
    </div></th>
    <td>
        <div class="col-md-6">
            <?= $form->field($model, 'pub_date')->textInput([
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $model,
                    'attribute'=>'pub_date',
                    'language' => 'ja',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class'=>'form-control col-md-12'],
                    'clientOptions' => [
                        'country'     => 'JP',
                        'yearRange'   => 'c-1:c+1',
                        'changeYear'  => true,
                        'changeMonth' => true,
                    ],
                ])]
            ) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'private')->checkBox() ?>
        </div>
    </td>
    </tr>

</tbody>
</table>

    <p class="pull-left">
    <?= Html::submitButton('保存',['class'=>'btn btn-success']) ?>
    </p>

    <?php if(! $model->isNewRecord): ?>
        <p class="pull-right">
            <?= Html::a('削除',['delete', 'id'=> $model->facility_id],[
                'class'=>'btn btn-danger',
                'data' => [
                    'confirm' => '本当に削除していいですか？',
                    'method'  => 'post',
                ],
            ]) ?>
        </p>
    <?php endif ?>

    <?php $form->end() ?>

    </div>

</div>
