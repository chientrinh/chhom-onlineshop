<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/client/customer-print-item-itv.php $
 * $Id: customer-print-item-itv.php 4125 2019-03-20 10:00:56Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\webdb20\Karute
 */
$only = Yii::$app->request->get('only');
?>
<?php if ($only !== 'recipe'): ?>
<div class="karute-view">

    <p>
        <strong class="text-success">
        <?= $model->itv_date ? date('Y-m-d', strtotime($model->itv_date)) : null ?>
        <?= $model->itv_time ? date('H:i', strtotime($model->itv_time)) : null ?>
        <?= $model->product ? $model->product->name  : null ?>
        <?= $model->homoeopath ? $model->homoeopath->homoeopathname : null ?>
        </strong>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class'=>'table table-condensed'],
        'attributes' => [
            'presence:ntext',
            'impression:ntext',
            'summary:ntext',
            'advice:ntext',
            'progress:ntext',
        ],
    ]) ?>
</div>
<?php endif; ?>

<?php if ($model->recipes): ?>
    <?php foreach($model->recipes as $recipe): ?>
        <div class="karute-view">
            <p>
                <strong class="text-success">
                <?= sprintf('%06d', $recipe->recipe_id) ?> /
                <?= Yii::$app->formatter->asDate($recipe->create_date,'php:Y-m-d H:i') ?> /
                <?= $recipe->homoeopath ? $recipe->homoeopath->homoeopathname : null ?>
                </strong>
                &nbsp;
                <strong>
                (クライアント：<?= $recipe->client ? $recipe->client->name : null ?>)
                </strong>
            </p>
            <?= \yii\grid\GridView::widget([
                'dataProvider' => new \yii\data\ActiveDataProvider([
                    'query'      => $recipe->getItems()->where(['parent'=>null]) ,
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
    <?php endforeach; ?>
<?php endif; ?>
