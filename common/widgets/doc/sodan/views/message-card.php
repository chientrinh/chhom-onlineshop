<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/sodan/views/message-card.php $
 * $Id: message-card.php 3962 2018-07-12 05:03:57Z mori $
 *
 * $model \common\models\sodan\Interview
 */

$csscode = '
.well {
  font-size:11pt;
  min-height: 20px;
  padding: 19px;
  margin-bottom: 20px;
  background-color: @well-bg;
  border: 1px solid @well-border;
  border-radius: @border-radius-base;
  .box-shadow(inset 0 1px 1px rgba(0,0,0,.05));
  blockquote {
    border-color: #ddd;
    border-color: rgba(0,0,0,.15);
  }
}
';
$this->registerCss($csscode);

$today = strtotime($model->itv_date);
$month = (60 * 60 * 24) * 30;

$next1 = date('m月d日', $today + $month);
$next2 = date('m月d日', $today + $month + $month);
$hpath = \common\models\sodan\Homoeopath::findOne($model->homoeopath_id);

$font_size = "font-size:1.5em;";
?>

<page>

    <p style="text-align:right; <?= $font_size ?>">
        健康相談会 <?= Yii::$app->formatter->asDate($model->itv_date,'php:Y年m月d日(D)') ?>
    </p>

    <p style="<?= $font_size ?>">
        <strong><?= $model->client->name ?></strong> 様
    </p>
    <p>
        &nbsp;
    </p>

    <?php if($model->advice): ?>
        <h4>
            ホメオパスからのメッセージ
        </h4>
        <div class="well">
            <?= Yii::$app->formatter->asNtext($model->advice) ?>
        </div>
    <?php endif ?>

    <h4 style="margin-bottom:5px;<?= $font_size ?>">
        担当ホメオパス
    </h4>
    <span style="<?= $font_size ?>">
        <strong><?= $model->homoeopath->homoeopathname ?></strong> 先生 <?= $hpath ? sprintf('【%s】',$hpath->schedule) : null ?>
    </span>

    <h4 style="margin-bottom:5px;<?= $font_size ?>">
        次回のご相談
    </h4>
    <span style="<?= $font_size ?>">
        次回は、<strong><?= $next1 ?>　～　<?= $next2 ?></strong>　が理想的なご相談のタイミングです。
    </span>

    <div style="width:50%;margin:10px 0 10px 5%;<?= $font_size ?>">
        <img src="<?= $img ?>">
    </div>

    <?php if($branch = $model->branch): ?>
        <p style="<?= $font_size ?>">
            相談会ご予約は、センター受付又は、下記お電話番号にて承っています。
        </p>
        <div style="<?= $font_size ?>">
            <h4 style="margin-bottom: 5px;"><?= $branch->name ?> </h4>
            <span style="margin-bottom: 5px;">〒<?= $branch->zip?> <?= $branch->addr ?> </span><br>
            <span style="margin-bottom: 5px;">電話： <?= $branch->tel ?> </span>
        </div>
    <?php endif ?>

</page>

