<?php
/**
 * $URL  $
 * $Id: form.php 3604 2017-09-24 05:08:26Z naito $
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

        <h2><span>請求先情報</span></h2>

        <p class="help-block">
            出店企業と月締めでご契約いただいている場合、以下の請求先情報が請求先の宛先に記載されます。
        </p>

        <?php $form = \yii\bootstrap\ActiveForm::begin([
            'id' => 'form-office-create',
            'layout' => 'default',
            'validateOnBlur'  => false,
            'validateOnChange'=> false,
            'validateOnSubmit'=> false,
            'fieldConfig'     => ['template'=>'{input}{error}'],
        ]) ?>

<table id="FormTable" class="table table-bordered">
<tbody>

    <tr>
    <th><div class="required"><label><?= $model->getAttributeLabel('company_name') ?></label></div></th>
    <td>
    <?= $form->field($model, 'company_name') ?>
    </tr>

    <tr>
    <th><div class="required"><label><?= $model->getAttributeLabel('person_name') ?></label></div></th>
    <td>
    <?= $form->field($model, 'person_name') ?>
    </tr>

    <tr>
    <th><div class="required">
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
    <p class="help-block help-block-error"></p>
    </div></td>
    </tr>

    <tr>
    <th><div class="required">
    <label>住所</label>
    </div></th>
    <td>
    <?= $form->field($model, 'pref_id')->dropDownList($prefs) ?>
    <label class="control-label" for="signupform-addr01">市区町村名（例：千代田区神田神保町）</label>
<?php if(isset($candidates) && (false !== $candidates)):
echo $form->field($model, 'addr01')->dropDownList($candidates)->render();
?>

<?php else: ?>
    <?= $form->field($model, 'addr01') ?>
<?php endif ?>

    <label class="control-label" for="signupform-addr02">番地・ビル名（例：1-3-5）</label>
    <?= $form->field($model, 'addr02') ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>電話番号</label>
    </div></th>
    <td>
    <?= $form->field($model, 'tel01',['options'=>['class'=>'Tel']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
    <span class="float-box">-</span>
    <?= $form->field($model, 'tel02',['options'=>['class'=>'Tel']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
    <span class="float-box">-</span>
    <?= $form->field($model, 'tel03',['options'=>['class'=>'Tel']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
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
    </td>
    </tr>

</tbody>
</table>

    <p class="pull-left">
    <?= Html::submitButton('保存',['class'=>'btn btn-success']) ?>
    </p>

    <?php if(! $model->isNewRecord): ?>
        <p class="pull-right">
            <?= Html::a('削除',['delete'],[
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
