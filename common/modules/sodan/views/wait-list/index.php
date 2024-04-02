<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/wait-list/index.php $
 * $Id: index.php 4141 2019-03-28 08:28:38Z kawai $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="wait-list-index">

    <p class="pull-right">
        <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <p class="pull-right" style="margin-right: 10px;">
        <?php if (Yii::$app->request->get('expire') === 'all'): ?>
            <?= Html::a('期限内のみ表示する', ['index'], ['class' => 'btn btn-default']) ?>
        <?php else: ?>
            <?= Html::a('すべて表示する', ['index?expire=all'], ['class' => 'btn btn-default']) ?>
        <?php endif; ?>
    </p>

    <h1>キャンセル待ち</h1>
    <?= $this->render('wait-list-grid',[
        'dataProvider' => $dataProvider,
        'searchModel'  => $searchModel,
    ]) ?>
</div>
