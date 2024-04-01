<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/agency-rank/_search-subcategory.php $
 * $Id: $
 *
 * $model \common\models\AgencyRankDetail
 */
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\helpers\ArrayHelper;
use \yii\bootstrap\ActiveForm;
use \common\models\Subcategory;
use \common\models\AgencyRank;
use \common\models\AgencyRankDetail;

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
$subcategories = ArrayHelper::map($subcategories, 'subcategory_id', 'fullname');
$this->params['breadcrumbs'][] = ['label' => $rank->name, 'url' => ['view', 'id' => $rank->rank_id]];
?>

<?= yii\widgets\DetailView::widget([
    'model' => $rank,
    'attributes' => [
            'rank_id',
            [
                'attribute' => 'name',
                'label'     => 'ランク名',
                'value'     => $rank->name,
                'headerOptions' =>['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'liquor_rate',
                'headerOptions' =>['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'remedy_rate',
                'headerOptions' =>['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'goods_rate',
                'headerOptions' =>['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'other_rate',
                'headerOptions' =>['class'=>'col-md-2'],
            ],
            'create_date',
            'update_date',
    ],
]) ?>

<h3>　サブカテゴリー<?= $agencyRankDetails->isNewRecord ? "登録" : "更新"; ?></h3>

<div class="col-md-7">

    <?php $form = ActiveForm::begin(); ?>
    <div class="form-group">
        <?= Html::hiddenInput('rank_id', $rank->rank_id); ?>
        <?= $form->field($agencyRankDetails, 'subcategory_id')
                    ->dropDownList($subcategories, ['id'=>'add-categories', /*'style'=>'width:inherit'*/]); ?>

        <?= $form->field($agencyRankDetails, 'discount_rate')
                    ->textInput($subcategories, ['maxlangth'=>true, 'class'=>"form-control js-zenkaku-to-hankaku", 'style'=>'width:inherit']); ?>

    </div>

    <div class="form-group pull-left">
        <?= Html::submitButton($agencyRankDetails->isNewRecord ? "登録" : "更新", ['class' => $rank->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <div class="pull-right">
        <?= Html::a('戻る', ['view', 'id' => $rank->rank_id, 'target' => 'viewSubCategory', ], ['class' => 'btn btn-danger update']
                ) ?>
    </div>

    <?php $form->end(); ?>

</div>
