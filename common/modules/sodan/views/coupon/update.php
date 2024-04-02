<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/book-template/update.php $
 * @version $Id: update.php 1853 2015-12-09 11:06:24Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  common\models\sodan\BookTemplate
 */

$title = '編集';
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ["update?id={$model->product_id}"]];
$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;
?>
<div class="wait-list-create">

    <p>健康相談クーポンを編集します。</p>

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
