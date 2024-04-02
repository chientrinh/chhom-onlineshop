<?php

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/stat/view.php $
 * $Id: view.php 3851 2018-04-24 09:07:27Z mori $
 *
 * $this \yii\web\View
 * $model \common\models\statistics\SodanStatistic
 */

?>

<div class="sodan-stat-index col-md-12">

    <h2 class="text-muted">
        健康相談 集計 <?= $model->year ?>年<?= $model->month ?>月
    </h2>
    <h3><?= $target->homoeopathname ?></h3>
    <?= $this->render('detail-grid',[
        'dataProvider'=> $dataProvider,
        'model'       => $model,
    ]) ?>

    <footer>
    <div class="col-md-3">
        <div class="panel panel-success">
            <div class="panel-heading">
                任意の年月で集計
            </div>
            <div class="panel-body">
                <?php $form = \yii\bootstrap\ActiveForm::begin([
                    'action' => ['view',
                                 'id'     => Yii::$app->request->get('id'),
                                 'target' => Yii::$app->request->get('target'),
                    ],
                    'method' => 'get',
                ]); ?>
                <?= $form->field($model, 'year')->textInput(['name'=>'year']) ?>
                <?= $form->field($model, 'month')->textInput(['name'=>'month']) ?>
                <?= Html::submitbutton('表示',['class'=>'btn btn-success']) ?>
                <?php $form->end() ?>
            </div>
        </div>
    </div>
    </footer>

</div>
