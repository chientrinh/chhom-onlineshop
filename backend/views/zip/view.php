<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/zip/view.php $
 * $Id: view.php 2667 2016-07-07 08:26:14Z mori $
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Zip */

$this->title = $model->region;
$this->params['breadcrumbs'][] = ['label' => 'Zips', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zip-view">

    <p class="pull-right">
        <?= Html::a('修正', ['update', 'region' => $model->region, 'zipcode' => $model->zipcode, 'pref_id' => $model->pref_id, 'city' => $model->city, 'town' => $model->town], ['class' => 'btn btn-primary']) ?>
    </p>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'region',
            'zipcode',
            [
                'attribute' => 'pref_id',
                'value'     => ($p = $model->pref) ? $p->name : null,
            ],
            'city',
            'town',
            'yamato_22',
            'sagawa_22',
            [
                'attribute' => 'spat',
                'value'     => ($p = $model->spat) ? '可' : '不可',
            ],
        ],
    ]) ?>

</div>
