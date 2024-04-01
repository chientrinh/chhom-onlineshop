<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use \yii\bootstrap\ActiveForm;
use \common\models\Branch;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/common/backend/views/pointing/create.php $
 * @version $Id: create.php 1853 2018-01-19 sado $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->params['breadcrumbs'] = array();
$this->params['breadcrumbs'][] = ['label' => '実店舗', 'url' => ['/casher/default']];
if ($branch_id) {
    $branch = Branch::findOne($branch_id);
    $this->params['breadcrumbs'][] = ['label' => $branch->name, 'url' => ['/casher/default']];
}
$this->params['breadcrumbs'][] = ['label' => 'ポイント付与', 'url' => ["create?branch_id={$branch_id}"]];
$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;

$query = \common\models\Company::find();
$company =  ArrayHelper::merge(['' => null], ArrayHelper::map($query->all(),'company_id','name'));

$jscode = "
    document.getElementById('barcode').focus();

    // 最後に取得したKeyup のkeyCode
    var keyUpCode = '';
    // keyUpイベント発火したかどうかのフラグ
    var keyUpFlg = false;
    // ime入力中のフラグ
    var imeFlg = false;
    // タイマー
    var timer;
    // submit中フラグ
    var set = false;

    $('#barcode').keyup(function(e) {
        keyUpCode = e.keyCode;
        keyUpFlg = true;
        if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
            if($('#barcode').val().length === 0 || (12 > $('#barcode').val().length && !isNaN($('#barcode').val()))) {
                return false;
            } else {
                if(imeFlg) {
                    if(!set){
                        set = true;
                        $('#barcode').val(zen2han($('#barcode').val()));
                        $(this).submit();
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
    });

    $('#barcode').keypress(function(e) {
       imeFlg = false;
    });

    $('#barcode').keydown(function(e) {
        // 設定していたタイマーのリセット
        clearTimeout(timer);
        // IME入力中フラグのリセット
        imeFlg = false;

        if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
            if($('#barcode').val().length === 0) {
                return false;
            } else {
                if(!set) {
                    set = true;
                    $('#barcode').val(zen2han($('#barcode').val()));
                    $(this).submit();
                    return true;
                } else {
                    return false;
                }
            }
        }
        if ((e.which && e.which === 229) || (e.keyCode && e.keyCode === 229)) {
            // IME入力中とみなす
            imeFlg = true;

            // 直前のキーコードをチェックして、入力確定とみなすか判定する
            if(keyUpCode !== 240 && keyUpCode !== 241 && keyUpCode !== 242 && keyUpCode !== 243 && keyUpCode !== 244 && !e.altKey && !e.metaKey && !e.ctrlKey) {
                // キー入力から500ミリ秒後にkeyupイベントの無い場合は入力確定とする
                timer = setTimeout(function () {
                    if (!keyUpFlg || keyUpFlg && imeFlg){
                        if(!(12 <= $('#barcode').val().length) && !isNaN($('#barcode').val())){
                            return false;
                        }
                        // submitさせる
                        if(!set) {
                            set = true;
                            $('#barcode').val(zen2han($('#barcode').val()));
                            $('#barcode-form').submit();
                            var evt = $.Event('keydown');
                            evt.keyCode = 27;    // エンターキー入力時のテスト
                            $('#barcode-form').trigger(evt);
                            return true;
                        } else {
                            return false;
                        }
                    }
                }, 800);
                // keyUpFlgのリセット
                keyUpFlg = false;
            }
        }
    });

    function zen2han (str) {
        str = str.replace(/[０-９]/g, function (s) {
            return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
        })
        return str;
    }
";
$this->registerJs($jscode);
?>
<div class="col-md-12">
    <div class="wait-list-form">
        <div class="col-md-12" style="margin-bottom: 20px;">
            <div class="col-md-5">
                <h2><span>ポイント付与</span></h2>
                <p>顧客にポイントを付与します</p>
                <?php $barcode_form = ActiveForm::begin([
                    'id'     => 'barcode-form',
                    'action' => ['apply','target' => 'barcode', 'branch_id' => $branch_id],
                    'method' => 'get',
                    'fieldConfig' => [
                        'enableLabel' => false,
                    ],
                ]) ?>
                <?= Html::textInput('barcode', null, [
                    'id'          => 'barcode',
                    'class'       => 'input-lg',
                    'placeholder' => 'BARCODE',
                    'size'        => 36,
                    'style'       => 'width:100%;',
                    'tabindex'    => 1,
                ]) ?>
                <?php $barcode_form->end() ?>
            </div>
            <div class="col-md-7">
            <?= $this->render('search-client',[
                'keyword'  => Yii::$app->request->post('keyword'),
            ]) ?>
            </div>
        </div>

        <div class="col-md-12">
            <?php $form = ActiveForm::begin(); ?>
                <div class="col-md-5">
                    <div class="col-md-5"><?= $form->field($model, 'point_given')->textInput(['maxlength' => 8, 'tabindex' => 4]) ?></div>
                    <div class="col-md-9"><?= $form->field($model, 'note')->textArea(['maxlength' => true, 'placeholder' => 'キャンペーン名を入力してください', 'tabindex' => 5]) ?></div>

                    <div class="col-md-7"><?= $form->field($model, 'company_id')->dropDownList($company, ['tabindex' => 6]) ?></div>
                    <div class="col-md-5" style="margin-top:30px;"><?= Html::submitButton('確定', ['class' => 'btn btn-danger', 'tabindex' => 7]) ?></div>
                </div>
            <?php $form->end(); ?>
            <div class="col-md-7"><?= $this->render('__customer', ['model' => $model->customer, 'parent_flg' => $parent_flg]) ?></div>
        </div>
        <div class="col-md-2 pull-right text-right" style="margin-bottom: 20px;">
            <?= Html::a('ポイント付与一覧',['index'], ['class'=>"pull-right btn btn-success", 'target' => '_brank']) ?>
        </div>

        <?php if($model->hasErrors()): ?>
            <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>
        <?php endif ?>

    </div>

</div>
