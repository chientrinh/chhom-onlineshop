<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/remedy/popular.php $
 * $Id: popular.php 2910 2016-10-02 02:22:42Z mori $
 *
 */

$this->params['body_id'] = 'MyPage';
$this->params['breadcrumbs'][] = ['label' => '定番レメディー'];

$labels = ArrayHelper::getColumn($this->params['breadcrumbs'], 'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | ' . Yii::$app->name;

$jscode = "
$('.tab-pane').on('click','a',function(e){
   $.ajax({
       url: $(e.target).attr('href'),
       data: {},
       success: function(data) {
           $(e.target).before('&#10004;');
       }
   });
   return false;
});
";
$this->registerJs($jscode);

if ($this->beginCache($this->context->id, [
    'dependency' => [
        'class' => \yii\caching\DbDependency::className(),
        'sql'   => 'SELECT MAX(update_date) FROM mvtb_product_master',
    ],
    'duration' => 60 * 60 * 24 * 90, // 90 days
])):
?>

<div class="container">

    <p class="alert alert-warning fade in">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        レメディーや商品の名前をクリックすると、１点ずつカートに追加されます。
    </p>

    <?= \yii\bootstrap\Nav::widget([
        'options' => ['class' => 'nav nav-tabs'],
        'items'   => [
            ['label'=>'36キット',         'url'=>'#kit',    'linkOptions'=>['data-toggle'=>'tab'], 'active'=>true],
            ['label'=>'セット',           'url'=>'#set',    'linkOptions'=>['data-toggle'=>'tab']],
            ['label'=>'マザーチンクチャー', 'url'=>'#mtq',    'linkOptions'=>['data-toggle'=>'tab']],
            ['label'=>'フラワーエッセンス', 'url'=>'#flower', 'linkOptions'=>['data-toggle'=>'tab']],
        ],
    ]) ?>

    <div class="tab-content">
        <div id="kit" class="tab-pane fade in active">
            <h3 style="border-bottom: 1px dotted #666;">
                36キット
            </h3>
            <?= $this->render('_kit') ?>
        </div>
        <div id="set" class="tab-pane fade">
            <h3 style="border-bottom: 1px dotted #666;">
                セット
            </h3>
            <?= $this->render('_set') ?>

        </div>
        <div id="mtq" class="tab-pane fade">
            <h3 style="border-bottom: 1px dotted #666;">
                マザーチンクチャー
            </h3>
            <?= $this->render('_mtq') ?>

        </div>
        <div id="flower" class="tab-pane fade">
            <h3 style="border-bottom: 1px dotted #666;">
                フラワーエッセンス
            </h3>
            <?= $this->render('_flower') ?>
        </div>
    </div>
</div>

<?php $this->endCache() ?>

<?php endif /* $this->beginCache() */ ?>
