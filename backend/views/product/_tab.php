<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product/_tab.php $
 * $Id: _tab.php 3257 2017-04-19 06:31:32Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\Product
 */
?>

<?= \yii\bootstrap\Nav::begin([
    'id'      => 'nav-tab',
    'options' => ['class' => 'nav nav-tabs'],
    'items'   => [
        [
            'label'  => 'プレビュー',
            'url'    => '/index.php/product/view?id='.$model->product_id, ['class'=>'btn btn-default'],
            'linkOptions' => ['target' => '_blank'],
        ],
        [
            'label'  => '説明',
            'url'    => ['view','id'=>$model->product_id],['class'=>'btn btn-default'],
            'options'=> (null === Yii::$app->request->get('target')) ? ['class'=>'active'] : [],
        ],
        [
            'label'  => '売上',
            'url'    => ['view','id'=>$model->product_id,'target'=>'sales'],['class'=>'btn btn-default'],
            'options'=> ('sales' === Yii::$app->request->get('target')) ? ['class'=>'active'] : [],
        ],
        [
            'label'  => '在庫',
            'url'    => ['view','id'=>$model->product_id,'target'=>'inventory'],['class'=>'btn btn-default'],
            'options'=> ('inventory' === Yii::$app->request->get('target')) ? ['class'=>'active'] : [],
            'visible'=> !Yii::$app->user->identity->hasRole(["tenant"])
        ],
        [
            'label'  => 'ご優待',
            'url'    => ['view','id'=>$model->product_id,'target'=>'offer'],['class'=>'btn btn-default'],
            'options'=> ('offer' === Yii::$app->request->get('target')) ? ['class'=>'active'] : [],
            'visible'=> !Yii::$app->user->identity->hasRole(["tenant"])
        ],
        [
            'label'  => 'DB操作履歴',
            'url'    => ['view','id'=>$model->product_id,'target'=>'history'],['class'=>'btn btn-default'],
            'options'=> ('history' === Yii::$app->request->get('target')) ? ['class'=>'active'] : [],
            'visible'=> !Yii::$app->user->identity->hasRole(["tenant"])
        ],
    ],
])->renderItems() ?>
