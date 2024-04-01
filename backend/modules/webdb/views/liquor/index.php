<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/webdb/views/liquor/index.php $
 * $Id: index.php 1499 2015-09-18 03:15:26Z mori $
 */

use yii\helpers\Html;
$this->title = '酒量の集計';
$this->params['breadcrumbs'][] = $this->title;
$month1 = strtotime('-1 month');

$model = new \common\models\DateForm();
?>

<div class="webdb-liquor-index">

    <div class="col-md-12">
    <h1><?= $this->title ?></h1>

    <p>
        <?= Html::a('今月の集計を表示', ['view'], ['class' => 'btn btn-success']) ?>
    </p>
    <p>
        <?= Html::a('先月の集計を表示', ['view', 'month'=>date('m', $month1), 'year'=>date('Y', $month1)], ['class' => 'btn btn-success']) ?>
    </p>
    </div>

    <div class="col-md-3">
        <div class="panel panel-success">
            <div class="panel-heading">
                任意の年月で集計
            </div>
            <div class="panel-body">
                <?php $form = \yii\bootstrap\ActiveForm::begin([
                    'action' => ['view'],
                    'method' => 'get',
                ]); ?>
                <?= $form->field($model, 'year')->textInput(['name'=>'year']) ?>
                <?= $form->field($model, 'month')->textInput(['name'=>'month']) ?>
                <?= Html::submitbutton('表示',['class'=>'btn btn-success']) ?>
                <?php $form->end() ?>
            </div>
        </div>
    </div>

</div>

