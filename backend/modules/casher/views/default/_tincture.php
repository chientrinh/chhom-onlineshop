<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_tincture.php $
 * $Id: _tincture.php 3496 2017-07-20 10:04:05Z kawai $
 *
 * $searchModel  Model
 * $dataProvider ActiveDataProvider of \backend\models\Product
 */
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use \common\models\RemedyPotency;
use \common\models\RemedyVial;
?>

<?= $this->render('__tabs',[
    'company' => $searchModel->company_id,
]) ?>

<?= $this->render('__view_remedy',[
    'target'        => $target,
    'dataProvider'  => $dataProvider,
    'searchModel'   => $searchModel,
    'potencies'     => false, //ArrayHelper::map(RemedyPotency::find()->tincture()->all(), 'potency_id', 'name'),
    'vials'         => ArrayHelper::map(RemedyVial::find()->tincture()->all(), 'vial_id', 'name'),
]) ?>