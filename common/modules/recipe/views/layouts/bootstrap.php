<?php
namespace common\modules\recipe\views\layouts;

/**
 * $URL: https://localhost:44344/svn/MALL/frontend/views/layouts/bootstrap.php $
 * $Id: bootstrap.php 1725 2015-10-29 09:53:17Z mori $
 *
 * @var $this \yii\web\View
 * @var $content string
 */

use Yii;
use yii\helpers\Html;

BootstrapAsset::register($this);

$this->registerCss("
h2 {
    margin: 0 0 20px;
    padding: 0;
    background-color: #F0F0F0;
    border: 1px solid #DDD;
    border-radius: 4px;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px; }

h2 span {
    display: block;
    margin: 0 1px;
    padding: 12px 8px 10px;
    line-height: 1.2em;
    font-size: 16px;
    font-weight: bold;
    border-top: 1px solid #FFF; }

.inner {
  padding: 0;
  border: 1px solid #C0C0C0;
}

.inner .initial {
    margin: 0 0 0 2px !important;
    padding: 0 !important; }

.inner .initial > li {
  display: inline;
  list-style: none;
  height: 30px;
}

.inner .initial li a {
    position: relative;
    float: left;
    padding: 6px 10px;
    margin-left: -1px;
    margin-bottom: -1px;
    width: 30px;
    color: #337AB7;
    line-height: 1.42857;
    text-decoration: none;
    background-color: #FFF;
    border: 1px solid #DDD; }

.inner .initial a,
.inner .initial a:hover,
.inner .initial a:focus {
    z-index: 2;
    color: #337AB7;
    cursor: pointer;
    background-color: #FFF;
    border-color: #DDD; }

ul, menu, dir {
  display: block;
  list-style-type: disc;
  -webkit-margin-before: 1em;
  -webkit-margin-after: 1em;
  -webkit-margin-start: 0px;
  -webkit-margin-end: 0px;
  -webkit-padding-start: 40px;
}

.product-search {
    margin-bottom: 25px;
    border: 5px solid #CCC;
    border-radius: 4px;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px; }

");

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
