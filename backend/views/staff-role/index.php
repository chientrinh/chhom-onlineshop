<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/staff-role/index.php $
 * $Id: index.php 2337 2016-03-31 01:42:46Z mori $
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchStaff */
/* @var $dataProvider yii\data\ActiveDataProvider */

$staffs   = \yii\helpers\ArrayHelper::map(\backend\models\Staff::find()->orderBy('email')->all(), 'staff_id', 'email');
$roles    = \yii\helpers\ArrayHelper::map(\backend\models\Role::find()->all(), 'role_id', 'name');
$branches = \yii\helpers\ArrayHelper::map(\common\models\Branch::find()->all(), 'branch_id', 'name');
?>
<div class="staff-index">

    <h1>全体を一覧</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'staff_id',
                'filter'    => $staffs,
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->staff->email, ['/staff/view','id'=>$data->staff_id],['title'=>$data->staff->name]); },
            ],
            [
                'attribute'=> 'role_id',
                'filter'   => $roles,
                'format'   => 'raw',
                'value'    => function($data){ return Html::a($data->role->name, ['role/view','id'=>$data->role_id],['title'=>$data->role->description]); },
            ],
            [
                'attribute' => 'branch_id',
                'filter'    => $branches,
                'format'    => 'html',
                'value'     => function($data)
                {
                    if($data->branch)
                        return Html::a($data->branch->name, ['/branch/view','id'=>$data->branch_id]);
                },
            ],
        ],
    ]); ?>

</div>
