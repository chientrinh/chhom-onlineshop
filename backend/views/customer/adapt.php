<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer/adapt.php $
 * $Id: adapt.php 3037 2016-10-28 08:40:25Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Customer
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use common\models\Customer;
use common\models\Pref;

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->customer_id]];
$this->params['breadcrumbs'][] = ['label' => "親子組み"];

$searchModel = new Customer();
$searchModel->load(Yii::$app->request->get());

$query = Customer::find()->active()
                         ->child(false);

foreach($searchModel->getAttributes() as $attr => $val)
{
    if($val)
        $query->andWhere(['like', $attr, $val]);
}

?>
<div class="customer-update">

    <h1><?= Html::encode($model->name) ?><small>さんの親を設定します</small></h1>

    <p class="help-block">
        住所・会員証NOは親会員と同一になります。
    </p>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'      => $query,
            'pagination' => ['pageSize' => 10],
        ]),
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label'  => '',
                'format' => 'raw',
                'value'  => function($data)use($model)
                {
                    return Html::a('設定',
                                   ['adapt', 'id' => $model->customer_id, 'parent_id' => $data->customer_id],
                                   ['class' => 'btn btn-xs btn-success']);
                },
            ],
            'customer_id',
            'name01',
            'name02',
            'kana01',
            'kana02',
            [
                'attribute' => 'zip01',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'zip02',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'pref_id',
                'value'     => function($data){ return ($p = $data->pref) ? $p->name : null; },
                'filter'    => ArrayHelper::map(Pref::find()->all(), 'pref_id', 'name'),
            ],
            'addr01',
            'addr02',
        ],
    ]) ?>

</div>

