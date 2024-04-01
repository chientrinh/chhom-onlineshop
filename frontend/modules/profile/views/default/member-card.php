<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/default/member-card.php $
 * $Id: member-card.php 3981 2018-08-09 06:30:05Z mori $
 */

use \yii\helpers\Html;

$title = 'モバイル会員証';
$this->title = sprintf('%s | %s | %s', $title, 'マイページ', Yii::$app->name);

$css = "
p {
    font-size: 16pt;
}
";
$this->registerCss($css);

\frontend\modules\profile\MemberCardAsset::register($this);

?>

<div class="card">

    <div class="col-md-12">
        <?= Html::img('@web/img/member-card-background.jpg',['style'=>'max-width:100%']) ?>
    </div>

    <div class="row text-center" style="max-height:50%">
        <div class="col-md-8 col-xs-8">
            <p>
                <?= Html::img(['member-card','target'=>'barcode'],['style'=>'width:80%;max-height:200px']) ?>
            </p>
            <p>
                <strong id="customer-membercode"><?= $model->code ?></strong>
            </p>

            <?php if($model->grade): ?>
                <p><strong><?= $model->grade->name ?>会員</strong></p>
            <?php endif ?>
        </div>
        <div class="col-md-2 col-xs-2">
            <?= Html::img('@web/img/member-card-logo.png',['style'=>'min-width:50%;max-width:80%']) ?>
        </div>
    </div>

</div>
