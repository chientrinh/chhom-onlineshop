<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/branch/view.php $
 * $Id: view.php 2337 2016-03-31 01:42:46Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Branch
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Branches', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="branch-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'branch_id',
            'name',
            [
                'attribute' => 'company',
                'format'    => 'raw',
                'value'     => Html::a($model->company->name, ['company/view','id'=>$model->company_id]),
            ],
            'zip',
            'addr',
            'tel',
            'email',
        ],
    ]) ?>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->branch_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <h2>従業員</h2>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => \backend\models\StaffRole::find()->where(['branch_id'=>$model->branch_id])
                                                        ->orderBy(['role_id'=>SORT_DESC])
        ]),
        'columns' => [
            [
                'attribute' => 'role_id',
                'value'     => function($data){ if($r = $data->role) return $r->name; },
            ],
            [
                'attribute' => 'staff_id',
                'format'    => 'html',
                'value'     => function($data){ if($s = $data->staff) return Html::a($s->name,['/staff/view','id'=>$data->staff_id]); },
            ],
        ],
    ]); ?>

</div>
