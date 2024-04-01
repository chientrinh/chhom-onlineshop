<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/category/index.php $
 * $Id: index.php 1309 2015-08-16 15:05:17Z mori $
 *
 * @var $this yii\web\View
 * @var $shops array of common\models\Branch
 */

$this->title = Yii::$app->name;
$this->params['body_id'] = 'Product';
$this->params['breadcrumbs'][] = "カテゴリー";

$csscode = '.col-md-2{width:180px;margin:4px}';
$this->registerCss($csscode);
?>
<div class="category-index">

    <div class="body-content">

    <div id="Home">
    <?= \frontend\widgets\CategoryNav::widget() ?>
    </div>

    </div>

</div>
