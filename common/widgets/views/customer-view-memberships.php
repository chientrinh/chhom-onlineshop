<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/views/customer-view-memberships.php $
 * $Id: customer-view-memberships.php 1753 2015-11-03 01:33:20Z mori $
 *
 * @var $this  yii/web/View
 * @var $model common/models/Customer
 */

use \yii\helpers\Html;
use \common\models\Membership;

$query = $model->getMemberships(true)
               ->andWhere(['membership_id' => [
                   Membership::PKEY_TOYOUKE,
                   Membership::PKEY_TORANOKO_GENERIC,
                   Membership::PKEY_TORANOKO_NETWORK,
                   Membership::PKEY_TORANOKO_GENERIC_UK,
                   Membership::PKEY_TORANOKO_NETWORK_UK,
                   Membership::PKEY_TORANOKO_FAMILY,
               ]])
               ->orderBy('expire_date DESC');
?>

<div class="col-md-12">
    <h3>
        <small>
            とらのこ・とようけ会員の履歴
        </small>
    </h3>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => false,
        ]),
        'layout'  => '{items}{pager}',
        'emptyText' => 'まだありません',
        'showOnEmpty' => false,
        'columns' => [
            [
                'attribute' => 'membership_id',
                'format'    => 'html',
                'value'     => function($data){ return $data->membership->name; },
            ],
            [
                'attribute' => 'start_date',
                'format'    => ['date','php:Y-m-d'],
            ],
            [
                'attribute' => 'expire_date',
                'format'    => ['date','php:Y-m-d'],
            ],
            [
                'label'     => '状態',
                'format'    => 'html',
                'value'     => function($data){ return $data->isExpired() ? "期限切れ" : "有効"; },
            ],
        ],
        'rowOptions' => function ($model, $key, $index, $grid){ return $model->isExpired() ? ['class'=>'danger'] : null; },
    ]); ?>

</div>

