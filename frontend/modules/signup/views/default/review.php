<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/signup/views/default/review.php $
 * $Id: review.php 1117 2015-06-30 16:31:16Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $info \frontend\models\SignupForm
 */

$title = "確認";
$this->params['breadcrumbs'][] = "会員登録";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Signup';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));
?>

<div class="signup-create">
    <h1 class="mainTitle"><?= Html::encode($title) ?></h1>
    <p class="mainLead">
この内容で登録します。よろしければ「登録する」をクリックしてください。</p>

  <div class="row column01">
  <div class="col-md-12">

<?php $form = ActiveForm::begin([
  'id' => 'form-signup',
  'layout' => 'default',
  'validateOnBlur'  => false,
  'validateOnChange'=> false,
  'validateOnSubmit'=> false,
  'fieldConfig'     => ['template'=>'{input}{error}'],
]);?>

<table summary="会員登録" id="FormTable" class="table table-bordered">
<tbody>

    <tr>
    <th><div class="required"><label>お名前</label></div></th>
    <td>
    <?= $model->name ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>お名前（ふりがな）</label>
    </div></th>
    <td>
    <?= $model->kana ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>郵便番号</label>
    </div></th>
    <td>
             <?= $model->zip ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>住所</label>
    </div></th>
    <td>
    <?php $pref = \common\models\Pref::find()->where(['pref_id' => $model->pref_id])->one();
      echo $pref ? $pref->name : '' ?>
    <?= $model->addr01 ?>
    <?= $model->addr02 ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>電話番号</label>
    </div></th>
    <td>
    <?= $model->tel ?>
    </td>
    </tr>

    <tr>
    <th><div>
    <label>性別</label>
    </div></th>
    <td>
    <?php $sex = \common\models\Sex::find()->where(['sex_id' => $model->sex_id])->one();
      echo $sex ? $sex->name : '' ?>
    </td>
    </tr>

    <tr>
    <th><div>
    <label>誕生日</label>
    </div></th>
    <td>
    <?= $model->birth ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>メールアドレス</label>
    </div></th>
    <td>
    <?= $model->email ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>パスワード</label>
    </div></th>
    <td>
    ******
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>メルマガ送付について</label>
    </div></th>
    <td>
     <?= $model->subscribe ? "受け取る" : "受け取らない" ?>
    </td>
    </tr>

</tbody>
</table>

<?php if($model->isNewRecord): ?>
    <div class="form-group">

    <?= Html::submitButton("登録する", [
        'class' => 'btn btn-primary',
        'name'  => 'scenario',
        'value' => 'default',
    ]) ?>

    </div><!--form-group-->
<?php endif ?>

    <?php ActiveForm::end(); ?>

  </div><!--col-md-12-->
  </div><!--row column01-->

</div><!--site-signup-->


</div><!--signup-create-->
