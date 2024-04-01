<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Customer;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/change-log/view.php $
 * $Id: view.php 2831 2016-08-11 02:59:25Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\ChangeLog
 */

$this->params['breadcrumbs'][] = ['label' => $model->create_date];
?>
<div class="change-log-view">

    <h1><?= $model->create_date ?></h1>

    <?= \yii\widgets\DetailView::widget([
        'model'      => $model,
        'attributes' => [
            'create_date',
            [
                'attribute' => 'user_id',
                'format'    => 'html',
                'value'     => (($user = $model->user) && $user instanceof Customer)
                            ? Html::a($user->name, ['/customer/view','id'=>$model->user_id])
                            : Html::a(ArrayHelper::getValue($user,'name'), ['/staff/view','id'=>$model->user_id]),
            ],
            'tbl',
            'pkey',
            'route',
            'action',
            'before',
            'after',
            'expire:datetime',
        ],
    ]) ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $model->getDiff(),
            'pagination' => false,
        ]),
        'caption' => '差分',
        'layout'  => '{items}',
    ]) ?>
</div>
