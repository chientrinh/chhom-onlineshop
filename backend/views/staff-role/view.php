<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/staff-role/view.php $
 * $Id: view.php 1505 2015-09-18 13:50:50Z mori $
 *
 * @var $this yii\web\View
 * @var $model app\models\Staff
 */

$this->title = sprintf("従業員 #%d: %s", $model->staff_id, $model->name);
$this->params['breadcrumbs'][] = ['label' => 'Staff', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="staff-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=> 'company_id',
                'format'   => 'raw',
                'value'    => Html::a($model->company->name, ['company/view','id'=>$model->company_id]),
            ],
            'name',
            'email',
            [
                'attribute' => 'update_date',
                'format'    => 'html',
            ],
            [
                'attribute' => 'expire_date',
                'format'    => 'html',
            ],
        ],
    ]) ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getRoles(),
        ]),
        'layout'  => '{items}',
        'columns' => [
            [
                'attribute' => 'branch_id',
                'value'     => function($data){ return $data->branch->name; },
            ],
            [
                'attribute' => 'role_id',
                'value'     => function($data){ return $data->role->description; },
            ],
             
        ],
    ]); ?>
                                                                                     
<?= $model->expired ? '<div class="well alert-danger">'."このアカウントは有効期限が切れています".'</div>' : null ?>
    <p>
        <?= Html::a("編集", ['update', 'id' => $model->staff_id], ['class' => 'btn btn-primary']) ?>
        <?=
        $model->expired
        ? Html::a("有効にする", ['activate', 'id' => $model->staff_id], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => sprintf("%sさんを有効に戻しますか", $model->name),
                'method'  => 'post',
            ],
        ]) 
        : Html::a("無効にする", ['expire', 'id' => $model->staff_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => sprintf("%sさんを無効にしていいですか", $model->name),
                'method'  => 'post',
            ],
        ]) ?>
    </p>

</div>
