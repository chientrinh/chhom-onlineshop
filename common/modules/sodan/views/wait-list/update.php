<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/wait-list/update.php $
 * @version $Id: update.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  common\models\sodan\Room
 */

$title = '編集';
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ["update?id={$model->wait_id}"]];
$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;
?>
<div class="wait-list-create">

    <p>キャンセル待ちを編集します。</p>

    <?= $this->render('_form', [
        'model' => $model,
        'staff_branch' => $model->branch_id
    ]) ?>

</div>
