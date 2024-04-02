<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/interview/update.php $
 * @version $Id: update.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  common\models\sodan\Room
 */

$this->params['breadcrumbs'][] = ['label' => $model->itv_id, 'url' => ['view','id'=>$model->itv_id]];
$this->params['breadcrumbs'][] = ['label' => '編集'];
$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;
?>
<div class="room-create">

  <?php if('karute' == Yii::$app->request->get('target')): ?>
    <?= $this->render('_karute', [
        'model' => $model,
    ]) ?>
  <?php else: ?>
    <?= $this->render('_form', [
        'model' => $model,
        'branch_id' => $model->branch_id
    ]) ?>
  <?php endif ?>

</div>
