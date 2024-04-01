<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/signup/views/default/thankyou.php $
 * $Id: thankyou.php 1523 2015-09-21 11:54:28Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $info \frontend\models\SignupForm
 */

$title = "完了";
$this->params['breadcrumbs'][] = "会員登録";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Signup';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));
?>

<div class="signup-create">
    <h1 class="mainTitle"><?= Html::encode($title) ?></h1>
    <p class="mainLead">ありがとうございます。以下の内容で登録しました。</p>

  <div class="row column01">
  <div class="col-md-12">

<table summary="会員登録" id="FormTable" class="table table-bordered">
<tbody>

    <tr>
    <th>
    <label>お名前（ふりがな）</label>
    </div></th>
    <td>
    <?= $model->name ?> (<?= $model->kana ?>)
    </td>
    </tr>

    <tr>
    <th>
    <label>会員区分</label>
    </div></th>
    <td>
    <?= $model->grade->name ?>
    </td>
    </tr>

    <?php if($model->memberships) : ?>
    <tr>
    <th>
    <label>出店企業／団体の区分</label>
    </div></th>
    <td>
             <?= \yii\widgets\ListView::widget([
                 'dataProvider' => new \yii\data\ArrayDataProvider([
                     'allModels' => $model->memberships,
                 ]),
                 'layout'=>'{items}',
                 'itemView' => function ($model, $key, $index, $widget)
                 {
                     return Html::tag('p',Html::tag('small', $model->company->name).'<br>'.$model->name); 
                 },
             ]) ?>
    </td>
    </tr>
    <?php endif ?>

    <tr>
    <th>
    <label>住所</label>
    </div></th>
    <td>
    〒<?= $model->zip ?>
    <br>
    <?php $pref = \common\models\Pref::find()->where(['pref_id' => $model->pref_id])->one();
      echo $pref ? $pref->name : '' ?>
    <?= $model->addr01 ?>
    <?= $model->addr02 ?>
    </td>
    </tr>

    <tr>
    <th>
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
    <label><?= $model->getAttributeLabel('birth') ?></label>
    </div></th>
    <td>
    <?= preg_match('/^0000/',$model->birth) ? '' : Yii::$app->formatter->asDate($model->birth, 'php:Y年m月d日') ?>
    </td>
    </tr>

    <tr>
    <th>
    <label>メールアドレス</label>
    </div></th>
    <td>
    <?= $model->email ?>
    </td>
    </tr>

    <tr>
    <th>
    <label>パスワード</label>
    </div></th>
    <td>
    ******
    </td>
    </tr>

    <tr>
    <th>
    <label><?= $model->getAttributeLabel('subscribe') ?></label>
    </div></th>
    <td>
     <?= ($subscribe = \common\models\Subscribe::findOne($model->subscribe)) ? $subscribe->name : "未指定" ?>
    </td>
    </tr>

</tbody>
</table>

<p class="mainLead">
  修正する場合は
  <?= Html::a("マイページ", ['/profile/default/update']) ?>
  にて編集できます</p>
  </div><!--col-md-12-->
  </div><!--row column01-->

</div><!--site-signup-->


</div><!--signup-create-->
