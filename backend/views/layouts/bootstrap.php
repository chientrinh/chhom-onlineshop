<?php
/**
 * bootstrap.php
 * - Very simple html view, only default Bootstrap is applied.
 *
 * @author  Reiko Mori <mori@homoeopathy.co.jp>
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/views/layouts/bootstrap.php $
 * @version $Id: bootstrap.php 2048 2016-02-06 08:55:22Z mori $
 */
use \yii\helpers\Html;

\common\assets\BootstrapAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

    <?= $content ?>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
