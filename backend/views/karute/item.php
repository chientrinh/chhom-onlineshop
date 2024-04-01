<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/karute/item.php $
 * $Id: item.php 2664 2016-07-06 08:36:09Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\webdb20\Karute
 */

?>
<div class="karute-view">

    <p>
        <strong class="text-success">
        <?= $model->syoho_date ?>
        <?= ! $model->consultationType ? null : $model->consultationType->name ?>
        <?= ! $model->branch ? null : $model->branch->name ?>
        <?= $model->homoeopath->name ?>
        </strong>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class'=>'table table-condensed'],
        'attributes' => [
           'user_report:ntext',
           'syoho_advice:ntext',
           'syoho_coment:ntext',
        ],
    ]) ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'      => $model->getRecipeItems(),
            'sort'       => ['defaultOrder' => ['dsp_num' => SORT_ASC]],
            'pagination' => false,
        ]),
        'tableOptions' => ['class'=>'table table-condensed'],
        'layout'       => '{items}',
        'showHeader'   => false,
            'columns'  => [
            'remedy.name',
            'potency.name',
            'time.name',
            'term.name',
            'fukuyo_coment',
            'fukuyo_detail',
        ],
    ]) ?>

</div>
