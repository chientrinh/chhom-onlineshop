<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/default/index.php $
 * $Id: index.php 3946 2018-06-22 04:07:17Z mori $
 */

use \yii\helpers\Html;

$this->params['body_id'] = 'Mypage';

?>

<div class="cart-view">

  <div class="col-md-12">
	<h2><span>履歴</span></h2>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'recipe_id',
            'format'    => 'html',
            'value'     => function($data)
            {
                return Html::a(sprintf('%06d', $data->recipe_id), ['view','id'=>$data->recipe_id]);
            }
        ],
        [
            'attribute' => 'create_date',
            'format'    => ['date','php:Y-m-d D H:i'],
        ],
        [
            'attribute' => 'client.name',
            'format'    => 'html',
            'value'     => function($data)
            {
                return ($data->client && $data->client->name) 
                        ? $data->client->name 
                        : $data->manual_client_name; 
            }
        ],
        [
            'attribute' => 'status',
            'value'     => function($data)
            {
                return $data->statusName;
            }
        ],
    ],
])?>

  <?= Html::a('新規作成',['create/index', 'new' => true],['class'=>'btn btn-success']) ?>

  </div>

</div>
