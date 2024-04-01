<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/role/view.php $
 * $Id: view.php 2337 2016-03-31 01:42:46Z mori $
 *
 * @var $this yii\web\View
 * @var $model backend\models\Role
 */

$this->params['breadcrumbs'][] = ['label' => $model->name];

?>
<div class="role-view">

    <h1>
        <?= Html::encode($model->name) ?>
        <small><?= Html::encode($model->description) ?></small>
    </h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getStaffs(),
        ]),
        'columns' => [
            [
                'attribute' => 'branch_id',
                'value'     => function($data){ if($b = $data->branch) return $b->name; },
            ],
            [
                'attribute' => 'staff_id',
                'format'    => 'html',
                'value'     => function($data){ if($s = $data->staff) return Html::a($s->email, ['/staff/view','id'=>$data->staff_id],['title'=> $s->name ]); },
            ],
        ],
    ]) ?>
</div>
