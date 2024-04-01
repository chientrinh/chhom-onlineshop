<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/facility/index.php $
 * $Id: index.php 3987 2018-08-17 02:30:40Z mori $
 *
 * $provider: DataProvider
 */

use \yii\helpers\Html;
use \common\models\Pref;

$prefs = Pref::find()->select(['name','pref_id'])
                     ->asArray()
                     ->indexBy('pref_id')
                     ->column();

?>
<div class="facility-default-index">

    <h1>提携施設</h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $provider,
        'filterModel'  => $model,
        'columns'   => [
            [
                'attribute' => 'facility_id',
                'label' => 'ID',
                'format'    => 'html',
                'value'     => function($data){
                    return Html::a($data->facility_id, ['view','id'=>$data->facility_id]);
                },
            ],
            'customer_id',
            'name',
            'title',
            [
                'attribute' => 'pref_id',
                'value'     => function($data){
                    return ($p = $data->pref) ? $p->name : null;
                },
                'filter'    => $prefs,
            ],
            'customer.name',
            [
                'attribute' => 'private',
                'format'    => 'boolean',
                'filter'    => [false => 'いいえ', true => 'はい'],
            ],
        ],
    ]) ?>

</div>
