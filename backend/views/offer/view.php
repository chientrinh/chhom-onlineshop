<?php

use yii\helpers\Html;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/views/offer/view.php $
 * @version $Id: view.php 2891 2016-09-29 01:23:00Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Offer
 *
 */

$title = sprintf('%s:%s', $model->category->name, $model->grade->name);
$this->params['breadcrumbs'][] = ['label' => $title];

?>

<div class="offer-view">

    <?= Html::a('修正',['update','category_id'=>$model->category_id,'grade_id'=>$model->grade_id],['class'=>'btn btn-primary pull-right']) ?>

    <h1><?= $title ?></h1>

    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            'category.seller.name',
            'category.name',
            [
                'label'     => $model->getAttributeLabel('grade_id'),
                'attribute' => 'grade.name',
            ],
            'discount_rate',
            'point_rate',
        ],
    ]) ?>

</div>
