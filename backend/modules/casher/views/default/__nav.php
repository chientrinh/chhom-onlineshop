<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/__nav.php $
 * $Id: __nav.php 4161 2019-06-07 05:51:23Z mori $
 */

use \yii\helpers\Html;

$action = $this->context->action;
$branch = $this->context->module->branch;
$target = Yii::$app->request->get('target');

?>

<?= \yii\bootstrap\Nav::widget([
    'encodeLabels' => false,
    'items'        => [
        [
        	'label'  => Html::tag('span','',['class'=>'glyphicon glyphicon-shopping-cart','title'=>"レジ"]),
        	'url'    => ['create'], 
        	'active' => ('create'  == $action->id) 
        ],
        [
        	'label'  => Html::tag('span','',['class'=>'glyphicon glyphicon-search','title'=>"商品を検索"]),
        	'url'    => ['search', 
                            'target'=> $branch->isHJForCasher() ? 'all_remedy' : 
                            ($branch->isHEForCasher() ? 'veg' : 
                            ($branch->isAtamiForCasher() ? 'popular' : 
                            ($branch->isRopponmatsuForCasher() ? 'products' :
                            ($branch->isTroseForCasher() ? 'trose' : 'popular')
                        )))], 
        	'active' => ('search' == $action->id) && ('customer' != $target)
        ],
        [
        	'label'  => Html::tag('span','',['class'=>'glyphicon glyphicon-user','title'=>"お客様を検索"]),
        	'url'    => ['search','target'=>'customer'], 
        	'active' => ('customer' == $target),
                'visible' => $action->controller->id != 'transfer' ? true : false //店間移動（transfer）のときは使用しない
        ],
    ],
    'options' => ['class' =>'nav-tabs alert-success'],
]) ?>

