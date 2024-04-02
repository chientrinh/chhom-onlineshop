<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/client/create.php $
 * @version $Id: create.php 1853 2015-12-09 11:06:24Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  common\models\sodan\Room
 */

$title = '追加';
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['create']];
$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;
?>
<div class="wait-list-create">

    <p>クライアントを追加します。</p>

    <?= $this->render('_form', [
        'model' => $model,
        'branch_id' => $branch_id
    ]) ?>

</div>
