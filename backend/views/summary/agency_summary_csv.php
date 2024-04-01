<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/agency_summary_csv.php $
 * $Id: purchase-item-.php 3080 2020-03-11: 12:21:17Z kawai $
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;

$this->params['breadcrumbs'][] = ['label' => '代理店売上集計CSV', 'url' => ['agency-summary-csv']];
?>

<div class="dispatch-default-index">
  <div class="body-content">
    <div class="col-md-10" style="margin-bottom: 15px;">
        <h2>代理店売上集計CSV</h2>

        <?php $form = \yii\bootstrap\ActiveForm::begin([
            'method' => 'post',
            'action' => Url::current(['start_date' => null, 'end_date' => null, 'agency' => null]),
        ]) ?>

        <?= $form->field($model, 'agency')->radioList(['1'=>'HJ代理店', '2'=>'HE代理店']); ?>

        <?= $form->field($model, 'start_date')->TextInput([
            'name' => 'start_date',
            'filter' => \yii\jui\DatePicker::widget([
                'model'      => $model,
                'attribute'  => 'start_date',
                'language'   => 'ja',
                'dateFormat' => 'yyyy-MM-dd 00:00:00',
            ])
        ])?>

        <?= $form->field($model, 'end_date')->TextInput([
            'name' => 'end_date',
            'filter' => \yii\jui\DatePicker::widget([
                'model'      => $model,
                'attribute'  => 'end_date',
                'language'   => 'ja',
                'dateFormat' => 'yyyy-MM-dd 23:59:59',
            ])
        ])?>


        <div class="pull-left">
            <?= Html::submitButton('CSV出力',['class' => 'btn btn-primary']) ?>
        </div>
    </div>
</div>
<?php $form->end() ?>


