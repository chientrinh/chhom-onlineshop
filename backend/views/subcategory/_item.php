<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/subcategory/_item.php $
 * $Id: _item.php 2765 2016-07-22 05:55:49Z naito $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\SearchSubcategory
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>

<div class="col-md-12">

<?= Html::a($model->fullname,['view','id'=>$model->subcategory_id]) ?>

<?php if($model->getChildren()->exists()): ?>
    <?= \yii\widgets\ListView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getChildren(),
            'pagination' => false,
        ]),
        'itemView' => '_item',
        'layout' => '{items}',
    ]); ?>
<?php endif ?>

</div>
