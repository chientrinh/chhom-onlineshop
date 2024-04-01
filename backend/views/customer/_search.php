<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Pref;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer/_search.php $
 * $Id: _search.php 4174 2019-07-23 06:38:43Z mori $
 *
 * @var $this yii\web\View
 * @var $model \common\models\Customer
 * @var $form ActiveForm
 */

$query = Pref::find()->select(['pref_id','name']);
$prefs = ArrayHelper::map($query->all(), 'pref_id', 'name');

$query = \common\models\Subscribe::find();
$subs  = ArrayHelper::map($query->all(), 'subscribe_id', 'name');

$query = \common\models\Sex::find();
$sexes = ArrayHelper::map($query->all(), 'sex_id', 'name');

$query = \common\models\CustomerGrade::find();
$grades= ArrayHelper::map($query->all(), 'grade_id', 'name');

$query = \common\models\Membership::find()->orderBy('name');
$mships= ArrayHelper::map($query->all(), 'membership_id', 'name');
if(empty($model->is_active))
    $model->is_active = 0;
?>
<div class="customer-search">

    <?php $form = \yii\widgets\ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => ['template' => "{input}\n{hint}\n{error}"],
    ]); ?>

    <div class="col-md-3">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">都道府県</h3>
        </div>
        <div class="panel-body">
        <?= $form->field($model,'pref_id')->checkBoxList($prefs,['separator'=>'<br>']) ?>
        </div>
    </div>
    </div>

    <div class="col-md-6">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">所属</h3>
        </div>
        <div class="panel-body">
        <?= Html::checkBoxList('membership',Yii::$app->request->get('membership'),$mships,['separator'=>'<br>'])?>
        </div>
        <div class="panel-footer text-right" title="顧客の所属をAND演算で検索します">
            <?= Html::radioList('picky', Yii::$app->request->get('picky',0), ['0'=>'いずれか一致',1=>'すべて一致']) ?>
        </div>
    </div>
    </div>

    <div class="col-md-3">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">メルマガ・ＤＭ抽出</h3>
        </div>
        <div class="panel-body">
        <?php //$form->field($model,'subscribe')->checkBoxList($subs,['separator'=>'<br>'])?>
            <?= Html::radioList('mailmaga', Yii::$app->request->get('mailmaga',0), [1=>'メルマガ',2=>'ＤＭ', 0=>'全て']) ?>

        </div>
    </div>
    </div>

    <div class="col-md-3">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">会員区分</h3>
        </div>
        <div class="panel-body">
        <?= $form->field($model,'grade_id')->checkBoxList($grades,['separator'=>'<br>'])?>
        </div>
    </div>
    </div>

    <div class="col-md-3">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">性別</h3>
        </div>
        <div class="panel-body">
        <?= $form->field($model,'sex_id')->checkBoxList($sexes,['separator'=>'<br>']) ?>
        </div>
    </div>
    </div>

    <div class="col-md-3">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">有効・無効</h3>
        </div>
        <div class="panel-body">
            <?= $form->field($model,'is_active')->radioList(['0'=>'すべて',1=>'有効', 2=>'無効']) ?>
        </div>
    </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('検索', ['name'=>'format', 'value'=>'html','class' => 'btn btn-info']) ?>
        <?= Html::submitButton('CSV', ['name'=>'format', 'value'=>'csv', 'class' => 'btn btn-default']) ?>
    </div>

    <?php $form->end(); ?>

</div><!-- customer-search -->

