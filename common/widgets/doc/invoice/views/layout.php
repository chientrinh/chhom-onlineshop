<?php
/**
 * none.php
 * - Very simple html view, as if none Yii layout is applied.
 * - Draw only minimum HTML elements.
 *
 * @author  Reiko Mori <mori@homoeopathy.co.jp>
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/invoice/views/layout.php $
 * @version $Id: layout.php 1674 2015-10-16 20:19:06Z mori $
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
