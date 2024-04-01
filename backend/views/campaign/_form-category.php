<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_search-product.php $
 * $Id: $
 *
 * $model \common\models\ProductMaster
 */
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\helpers\ArrayHelper;
use \yii\bootstrap\ActiveForm;
use \common\models\Subcategory;
use \common\models\CampaignDetail;
use \common\models\Campaign;

$csscode = "
    #cart-items   { margin: 20px 0; }

    #search-parts {
        margin: 20px 0;
        // margin-bottom: 25px;
        border: 5px solid #CCC;
        border-radius: 4px;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px; }

    #search-parts .inner {
        padding: 0;
        border: 1px solid #C0C0C0; }

    #search-parts .inner h4 {
        margin: 0 0 15px;
        padding: 10px 0 10px 8px;
        background-color: #F0F0F0;
        font-size: 14px;
        font-weight: normal;
        border-top: 1px solid #FFF;
        border-left: 1px solid #FFF; }

    #search-parts .inner label { font-size: 85%; }
    #search-parts .inner ul { padding: 0 0 0 18px; }
    #search-parts .inner ul li { margin-bottom: 6px; }
    #search-parts .inner div { margin-left: 10px; margin-right: 10px; }

    h3 {
      background-color: #f0f8ff; 
     　border-radius: 10px;        /* CSS3草案 */  
      -webkit-border-radius: 10px;    /* Safari,Google Chrome用 */  
      -moz-border-radius: 10px;   /* Firefox用 */  
    }
";
$this->registerCss($csscode);

$categories = ArrayHelper::map($categories, 'category_id', 'fullname');
$grades = ArrayHelper::map($grades, 'grade_id', 'name');
$this->params['breadcrumbs'][] = ['label' => $campaign->campaign_name, 'url' => ['view', 'id' => $campaign->campaign_id]];
?>

<?= yii\widgets\DetailView::widget([
    'model' => $campaign,
    'attributes' => [
            [
                'attribute' => 'campaign_code',
                'label'     => 'キャンペーンコード',
                'value'     => $campaign->campaign_code,
                'headerOptions' =>['class'=>'col-md-1'],
            ],
            [
                'attribute' => 'campaign_type',
                'label'     => 'キャンペーン区分',
                'value'     => $campaign->campaign_type == Campaign::DISCOUNT ? "値引" : "ポイント",
                'headerOptions' =>['class'=>'col-md-1'],
            ],

            [
                'attribute' => 'campaign_name',
                'label'     => '名称',
                'value'     => $campaign->campaign_name,
                'headerOptions' =>['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'start_date',
                'label'     => '利用開始日時',
                'format'    => ['date','php:Y-m-d D H:i:s'],
                'value'     => $campaign->start_date,
                'headerOptions' =>['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'end_date',
                'label'     => '利用終了日時',
                'format'    => ['date','php:Y-m-d D H:i:s'],
                'value'     => $campaign->end_date,
                'headerOptions' =>['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'status',
                'label'     => '有効/無効',
                'format'    => 'raw',
                'value'     => $campaign->statuses[$campaign->status],
                'headerOptions' =>['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'branch_id',
                'label'     => '拠点',
                'value'     => $campaign->branch ? $campaign->branch->name : null,
                'headerOptions' =>['class'=>'col-md-2'],
            ],
            'create_date',
            'update_date',
    ],
]) ?>

<h3>　カテゴリー<?= $campaignDetails->isNewRecord ? "登録" : "更新"; ?></h3>

<div class="col-md-7">

    <?php $form = ActiveForm::begin(); ?>
    <div class="form-group">
        <?= Html::hiddenInput('campaign_id', $campaign->campaign_id); ?>
        <?= $form->field($campaignDetails, 'category_id')
                    ->dropDownList($categories, ['id'=>'add-categories', 'class' => 'form-control'/*'style'=>'width:inherit'*/]); ?>

<?php // Typeで判断する
 if($campaign->campaign_type == Campaign::DISCOUNT) : ?>
        <?= $form->field($campaignDetails, 'discount_rate')
                    ->textInput($categories, ['maxlangth'=>true, 'class'=>"form-control js-zenkaku-to-hankaku", 'style'=>'width:inherit']); ?>
        <?= $form->field($campaignDetails, 'grade_id', ['template' => '{input}'])->hiddenInput(['value' => NULL])->label(false); ?>
        <?= $form->field($campaignDetails, 'point_rate', ['template' => '{input}'])->hiddenInput(['value' => '0'])->label(false); ?>

<?php else : ?>
        <?= $form->field($campaignDetails, 'grade_id')
                    ->dropDownList($grades, ['id'=>'add-grade', 'class' => 'form-control'/*'style'=>'width:inherit'*/]); ?>

        <?= $form->field($campaignDetails, 'point_rate')
                    ->textInput($categories, ['maxlangth'=>true, 'class'=>"form-control js-zenkaku-to-hankaku", 'style'=>'width:inherit']); ?>
        <?= $form->field($campaignDetails, 'discount_rate', ['template' => '{input}'])->hiddenInput(['value' => '0'])->label(false); ?>

<?php endif; ?>
    </div>

    <div class="form-group pull-left">
        <?= Html::submitButton($campaignDetails->isNewRecord ? "登録" : "更新", ['class' => $campaign->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <div class="pull-right">
        <?= Html::a('戻る', ['view', 'id' => $campaign->campaign_id, 'target' => 'viewCategory', ], ['class' => 'btn btn-danger update']
                ) ?>
    </div>

    <?php $form->end(); ?>

</div>
