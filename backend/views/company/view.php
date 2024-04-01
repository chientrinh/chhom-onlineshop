<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/company/view.php $
 * $Id: view.php 2337 2016-03-31 01:42:46Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Company
 */

$this->params['breadcrumbs'][] = ['label' => $model->name ];
?>
<div class="company-view">

    <h1><?= Html::encode($model->name) ?></h1>

    <?= yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            'key',
            'company_id',
            'name',
            'manager',
            'email',
            'zip',
            'addr',
            'tel',
        ],
    ]) ?>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->company_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <h2><?= $model->getAttributeLabel('branch') ?></h2>

 <?= yii\widgets\ListView::widget([
        'dataProvider' => new yii\data\ArrayDataProvider([
            'allModels' => $model->branches,
            'sort' => [
                'attributes'   => array_keys($model->branches[0]->attributes),
                'defaultOrder' => ['branch_id'=> SORT_ASC],
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]),
    'layout'       => '{items}',
    'itemView'     => '_branch',
 ])?>

    <h2>従業員</h2>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => \backend\models\Staff::find()->active()
                                                    ->where(['company_id' => $model->company_id])
        ]),
        'columns' => [
            [
                'attribute' => 'staff_id',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->name, ['/staff/view','id'=>$data->staff_id]); },
            ],
            'email',
        ],
    ]) ?>

</div>
