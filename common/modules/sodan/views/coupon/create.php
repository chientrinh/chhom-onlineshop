<?php

use yii\helpers\ArrayHelper;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/coupon/create.php $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  common\models\sodan\BookTemplate
 */

$title = '追加';
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['create']];
$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;
?>
<div class="wait-list-create">

    <p>健康相談クーポンを追加します。</p>

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
