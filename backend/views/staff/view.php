<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/staff/view.php $
 * $Id: view.php 1967 2016-01-13 01:11:08Z mori $
 *
 * @var $this yii\web\View
 * @var $model app\models\Staff
 */

$this->params['breadcrumbs'][] = ['label' => '従業員', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name];
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
                'value'     => function($data){ if($data->branch) return $data->branch->name; },
            ],
            [
                'attribute' => 'role_id',
                'value'     => function($data){ return $data->role->description; },
            ],
            [
                'label'  => '',
                'format' => 'raw',
                'value'  => function($data){
                    return Html::a('削除',['staff-role/delete', 'id'=>$data->staff_id, 'ro'=>$data->role_id, 'br'=>$data->branch_id], ['class' => 'btn btn-xs btn-default']);
                },
                'header' => $model->expired ? '' : Html::a("追加", ['staff-role/create', 'id' => $model->staff_id], ['class' => 'btn btn-xs btn-primary']),
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
