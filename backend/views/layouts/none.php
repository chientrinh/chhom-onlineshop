<?php
/**
 * none.php
 * - Very simple html view, as if none Yii layout is applied.
 * - Draw only minimum HTML elements.
 *
 * @author  Reiko Mori <mori@homoeopathy.co.jp>
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/views/layouts/none.php $
 * @version $Id: none.php 1207 2015-07-26 04:53:21Z mori $
 */
use \yii\helpers\Html;

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
