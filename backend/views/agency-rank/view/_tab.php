<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/campaign/_tab.php $
 * $Id: _tab.php $
 */

use \yii\helpers\Html;
//$actionId = Yii::$app->controller->action->id;
?>

<?= \yii\bootstrap\Nav::widget([
    'encodeLabels' => false,
    'items'        => [
            [
                'label'  => Html::tag('b','サブカテゴリー',['title'=>"サブカテゴリー"]),
                'url'    => [
                                'view', 
                                'id'=>$model->rank_id, 
                                'target' => 'viewSubCategory'

                            ], 
                'options' => ['id' => 'subcategory', 'class' => 'col-md-3'],
                'active' => (in_array(Yii::$app->request->get('target'), ['viewSubCategory', 'updateSubCategory'])),
            ],
            [
                'label'  => Html::tag('b','商品',['title'=>"商品"]),
                'url'    => [
                                'view', 
                                'id'=>$model->rank_id, 
                                'target' => 'viewProduct'
                            ], 
                'options' => ['id' => 'product', 'class' => 'col-md-3'],
                'active' => (in_array(Yii::$app->request->get('target'), ['viewProduct', 'updateProduct'])),
            ],
    ],
    'options' => ['class' =>'nav-tabs'],
]) ?>

