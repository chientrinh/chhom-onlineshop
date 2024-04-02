<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/client/customer-print-item-recipe.php $
 * $Id: customer-print-item-recipe.php 2340 2016-03-31 05:06:19Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\webdb20\Karute
 */

?>
<div class="karute-view">

    <p>
        <strong class="text-success">
        <?= sprintf('%06d', $model->recipe_id) ?> /
        <?= Yii::$app->formatter->asDate($model->create_date,'php:Y-m-d H:i') ?> /
        <?= $model->homoeopath ? $model->homoeopath->homoeopathname : null ?>
        </strong>
    </p>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'      => $model->getItems()->where(['parent'=>null]) ,
            'pagination' => false,
            'sort'       => false,
        ]),
        'tableOptions' => ['class'=>'table table-condensed'],
        'layout'  => '{items}',
        'columns' => [
            ['class' => \yii\grid\SerialColumn::className()],
            'fullname',
            [
                'attribute' => 'instruction',
                'value'     => function($data){ return \yii\helpers\ArrayHelper::getValue($data,'instruction.name'); }
            ],
            'memo',
        ],
    ]) ?>

</div>
