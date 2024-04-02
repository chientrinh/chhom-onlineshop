<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/admin/index.php $
 * $Id: index.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\SearchRecipe
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels) . ' | ' . Yii::$app->name;
?>

<div class="recipe-index">

    <h1>適用書</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'recipe_id',
                'format'    => 'html',
                'value'     => function($data)
                {
                    return Html::a(sprintf('%06d', $data->recipe_id), ['view','id'=>$data->recipe_id]);
                }
            ],
            //'homoeopath.name:text:ホメオパス',
            [
                'attribute' => 'homoeopath_name',
                'label'     => 'ホメオパス',
                'format'    => 'html',
                'value'     => function($data)
                {
                    return ($data->homoeopath && $data->homoeopath->homoeopathname)
                            ? $data->homoeopath->homoeopathname
                            : "";
                }

            ],
            // 'client.name:text:クライアント',
            [
                'attribute' => 'client_name',
                'label'     => 'クライアント',
                'format'    => 'html',
                'value'     => function($data)
                {
                    return ($data->client && $data->client->name)
                            ? $data->client->name
                            : $data->manual_client_name;
                }
            ],
            [
                'attribute' => 'create_date',
                'format'    => ['date','php:Y-m-d D H:i'],
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute'=>'create_date',
                    'language' => 'ja',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class'=>'form-control col-md-12'],
                ]),
            ],
            [
                'attribute' => 'expire_date',
                'format'    => ['date','php:Y-m-d D'],
            ],
            [
                'attribute' => 'status',
                'format'    => 'html',
                'value'     => function($data)
                {
                   if(\common\models\Recipe::STATUS_SOLD == $data->status)
                   {
                       if(($sold_recipe_purchase = \common\models\LtbPurchaseRecipe::find()->where(['recipe_id'=>$data->recipe_id])->select('purchase_id')->one()) != false)
                           return $data->statusName." (伝票：".Html::a($sold_recipe_purchase->purchase_id, ['/purchase/view','id'=>$sold_recipe_purchase->purchase_id]).")";
                   }

                    return $data->statusName;

                },
                'filter' => [
                    \common\models\Recipe::STATUS_INIT    => "発行",
                    \common\models\Recipe::STATUS_PREINIT    => "仮発行",
                    \common\models\Recipe::STATUS_SOLD    => "購入",
                    \common\models\Recipe::STATUS_EXPIRED => "期限切れ",
                    \common\models\Recipe::STATUS_CANCEL  => "キャンセル",
                    \common\models\Recipe::STATUS_VOID    => "無効",
                ],
            ],
        ],
    ]); ?>

    <p>
        <?= Html::a('新規作成', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

</div>
