<?php 

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/invoice/views/admin/index.php $
 * $Id: index.php 1674 2015-10-16 20:19:06Z mori $
 */

use \yii\helpers\Html;
use \common\models\Invoice;

if($year && $month)
    $searchModel->target_date = sprintf('%04d-%02d', $year, $month);

$months = range(1,12);
$months = array_combine($months, $months);
?>

<div class="invoice-default-index">

    <h1>請求書 <small>入金確認</small> </h1>

    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'layout' => 'inline',
        'method' => 'get',
    ]); ?>

    <div class="row pull-right">

    <?= $form->field($searchModel, 'year')->textInput(['name'=>'year']) ?>
    <?= $form->field($searchModel, 'month')->dropDownList($months,['name'=>'month']) ?>
    <?= Html::submitButton('検索',['class'=>'btn btn-default']) ?>
    <p>&nbsp;</p>
    </div>

    <?php $form->end(); ?>

    <?= $this->render('_grid', [
        'dataProvider'=> $dataProvider,
        'searchModel' => $searchModel,
    ]) ?>

</div>
