<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/views/customer-view-recipes.php $
 * $Id: customer-view-recipes.php 1753 2015-11-03 01:33:20Z mori $
 *
 * @var $this  yii/web/View
 * @var $model common/models/Customer
 */

use \yii\helpers\Html;

?>

<div class="col-md-12">
    <h3>
        <small>
            適用書の履歴
        </small>
    </h3>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $model->recipes,
        ]),
        'layout'  => '{summary}{items}{pager}',
        'emptyText' => 'まだありません',
        'showOnEmpty' => false,
        'columns' => [
            [
                'attribute' => 'recipe_id',
                'format'    => 'html',
                'value'     => function($model){ return Html::a(sprintf('%06d',$model->recipe_id), ['/recipe/view', 'id'=>$model->recipe_id]); },
            ],
            [
                'attribute' => 'create_date',
                'format'    => ['date','php:Y-m-d D'],
            ],
            [
                'attribute' => 'update_date',
                'format'    => ['date','php:Y-m-d D'],
            ],
        ],
    ]); ?>

</div>

