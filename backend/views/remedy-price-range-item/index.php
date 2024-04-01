<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-price-range-item/index.php $
 * $Id: index.php 3282 2017-05-02 08:11:36Z kawai $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\SearchRemedyPriceRangeItem
 * @var $dataProvider yii\data\ActiveDataProvider
 */

// settings for image slider
$jscode = "
    var lockId = 'lockId';
 
    // 画面操作を有効にする
    unlockScreen(lockId);

    $('#reflection-price').on('click', function() {
        if (! confirm('現在の価格設定を反映します。よろしいですか。')){
            return false;
        }
        // 画面操作を無効する
        lockScreen(lockId);
    });
 
    /* 画面操作を無効にする　*/
    function lockScreen(id) {
 
        var divTag = $('<div />').attr('id', id);
 
        /* スタイル設定 */
        divTag.css('z-index', '999')
              .css('position', 'fixed')
              .css('top', '0px')
              .css('left', '0px')
              .css('right', '0px')
              .css('bottom', '0px')
              .css('background', 'rgba(0,0,0,0.75)')
              .css('opacity', '0.01');
 
        $('*').append(divTag);
    }
 
    /* 画面操作無効を解除する */
    function unlockScreen(id) {
 
        /* 画面を覆っているタグを削除する */
        $('#' + id).remove();
    }
";
$this->registerJs($jscode, \yii\web\View::POS_LOAD);

$pranges = \yii\helpers\ArrayHelper::map(\common\models\RemedyPriceRange::find()->all(), 'prange_id', 'name');

$dataProvider->pagination->pagesize = 50;
$dataProvider->sort->enableMultiSort = true;

$template = Yii::$app->user->identity->hasRole(['wizard']) ? '{view}{update}' : '{view}';
?>
<div class="remedy-price-range-item-index">

<?php if (Yii::$app->user->identity->hasRole(['wizard'])) : ?>
    <p class="pull-right">
        <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('価格反映', ['reflection-price'], ['class' => 'btn btn-danger', 'id'  => 'reflection-price']) ?>
    </p>
<?php endif ?>

    <h1>価格設定</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class'=>'table table-condensed table-striped table-bordered'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'prange_id',
                'format'   =>'raw',
                'value'    =>function($data){ return $data->prange->name; },
                'filter'   =>\yii\helpers\ArrayHelper::map(\common\models\RemedyPriceRange::find()->all(), 'prange_id', 'name'),
            ],
            [
                'attribute'=>'vial_id',
                'format'   =>'raw',
                'value'    =>function($data){ return $data->vial->name; },
                'filter'   =>\yii\helpers\ArrayHelper::map(\common\models\RemedyVial::find()->all(), 'vial_id', 'name'),
            ],
            [
                'attribute'=>'vial.unit_id',
                'format'   =>'raw',
                'value'    =>function($data){ return sprintf("%d %s",$data->vial->volume,$data->vial->unit->name); },
                'headerOptions' => ['class'=>'col-md-1'],
            ],
            [
                'attribute' => 'price',
                'format'=>'currency',
                'contentOptions' => ['class'=>'text-right'],
                'headerOptions' => ['class'=>'col-md-1'],
            ],
            [
                'attribute' => 'expire_date',
                'format'    => ['date', 'php:Y-m-d'],
                'value'     => function($data)
                {
                    return preg_match('/^0000/', $data->expire_date) ? null : $data->expire_date;
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $template,
                'urlCreator' => function ($action, $model, $key, $index)
                {
                    return \yii\helpers\Url::toRoute([
                        '/remedy-price-range-item/'.$action,
                        'prange_id' => $model->prange_id,
                        'vial_id'   => $model->vial_id,
                    ]);
                },
                'headerOptions' => ['class'=>'col-md-1'],
            ],
        ],
    ]); ?>

</div>
