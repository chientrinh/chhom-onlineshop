<?php 
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/layouts/main.php $
 * $Id: main.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this \yii\web\View
 * @var $content string
 */

use yii\helpers\Html;

\common\assets\BootstrapAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>

    <div class="container">
        <?= \yii\widgets\Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= \frontend\widgets\Alert::widget() ?>

        <?= $content ?>

    </div>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
