<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/create/remedy.php $
 * $Id: remedy.php 3916 2018-06-01 07:13:51Z mori $
 */

use \yii\helpers\Html;
use \yii\helpers\Url;

$this->params['body_id'] = 'Mypage';

$jscode = "
$('#product-grid-view').on('click','a',function(e){
   if(! $(this).hasClass('btn'))       return true;  // do default action if class != btn
   if($(this).hasClass('btn-default')) return false; // do nothing if class == btn-default

   $.ajax({

       url: $(e.target).attr('href'),
       data: {},
       success: function(data) {
           $(e.target).attr('class','btn btn-xs btn-default')
       }
   });
   // return false;
});
";
// $this->registerJs($jscode);

$flowers = ['flower', 'flower2'];

$dataProvider->sort = [
            'attributes' => [
                'remedy' => [
                    'asc' => ['mtb_remedy.abbr' => SORT_ASC ],
                    'desc'=> ['mtb_remedy.abbr' => SORT_DESC],
//                    'asc' => ['remedy.abbr' => SORT_ASC ],
//                    'desc'=> ['remedy.abbr' => SORT_DESC],
                    'label' => "レメディー",
                ],
                'potency_id',
                'vial_id' => [
                    'asc' => ['vial_id' => SORT_ASC ],
                    'desc'=> ['vial_id' => SORT_DESC],
                ],
            ],
];

$potencies = [];
$vials = [];
if('tincture' == $target) {
    $title     = '単品MT: マザーチンクチャー';
    $potencies = \yii\helpers\ArrayHelper::map(\common\models\RemedyPotency::find()->tincture()->all(), 'potency_id', 'name');
    $query = \common\models\RemedyVial::find()->tincture();
    $vials = \yii\helpers\ArrayHelper::map($query->all(), 'vial_id', 'name');
} else {
    if('nonpublic' == $target) {
        $title     = '一般処方不可（学生ホメオパスへの処方のみ）';
    $potencies = \yii\helpers\ArrayHelper::map(\common\models\RemedyPotency::find()->all(), 'potency_id', 'name');
    $query = \common\models\RemedyVial::find();
    $vials = \yii\helpers\ArrayHelper::map($query->all(), 'vial_id', 'name');


    } else if('flower' == $target) {
        $title     = 'FE: フラワーエッセンス';
    } else if('flower2' == $target) {
            $title     = 'FE2: フラワーエッセンス';
    } else if('jm' == $target) {
            $title     = 'JM: ジェモセラピー';
    } else {
            $title = '単品レメディー';

            $query = \common\models\RemedyPotency::find()
                        ->andFilterWhere(['<>','potency_id',\common\models\RemedyPotency::MT])
                        ->andFilterWhere(['not',['potency_id' => yii\helpers\ArrayHelper::getColumn(\common\models\RemedyPotency::find()->flowers()->all(), 'potency_id')]]);
            $potencies = \yii\helpers\ArrayHelper::map($query->all(),'potency_id', 'name');
            $vials = \yii\helpers\ArrayHelper::map(\common\models\RemedyVial::find()->remedy()->all(), 'vial_id', 'name');
    }

}



$this->title = sprintf("%s を追加 | 新規作成 | 適用書 | %s", $title, Yii::$app->name);

?>

<div class="cart-view col-md-12">

<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#site-navbar-collapse" aria-expanded="true"><span class="sr-only">Toggle navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span></button>

  <?= $this->render('_tab', ['model' => $recipe]) ?>


  <div class="col-md-9">

  <h2><span><?= $title ?>を追加</span></h2>

<?php if($target != 'flower') { ?>
<!--    <div id="Search">
        <div class="inner">-->
           <?php
//                \common\widgets\AtoZ::widget([
//                    'target' => Yii::$app->request->get('target'),
//                ])
           ?>
<!--      </div>
    </div>-->
<?php } ?>
<?= \yii\grid\GridView::widget([
    'id'           => 'product-grid-view',
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'tableOptions' => ['class'=>'table table-condensed table-striped'],
    'layout'       => '{items}{pager}',
    'columns'      => [
        [
            'class'     => 'yii\grid\ActionColumn',
            'template'  => '{apply}',
            'buttons'   =>[
                'apply' => function ($url, $model, $key) use($target) {
                    $stock = ($model->in_stock) ? 1 : 0;
                    return (!$stock) ? null : Html::a('✔', [
                        'add',
                        'target'=> ($target == 'all') ? 'remedy' : $target,
                        'rid'=>$model->remedy_id,
                        'pid'=>$model->potency_id,
                        'vid'=>$model->vial_id,
                        'stock' => $stock
                      ],
                      ['class'=>'btn btn-xs btn-success','title'=>'カートに追加']); },
            ],
        ],
        [
            'attribute' => 'remedy',
            'label'     => '短縮形',
            'value'     => function($data)
            {
                return $data->remedy->abbr;
            },
        ],
        [
            'attribute' => 'remedy_name',
            'label'     => '名前',
            'format'    => 'html',
            'value'     => function($data)
            {
                $name = $data->remedy->ja;
                $stock = ($data->in_stock) ? 1 : 0;
                if (!$stock) {
                    $name .= " <span style='color:red;'>(欠品中)</span>";
                }
                return $name;
            },
        ],
        [
            'attribute' => 'potency_id',
            'label'     => (in_array($target, $flowers)) ? '種別' : 'ポーテンシー',
            'visible'   => ('tincture' == $target || in_array($target, $flowers)) ? false : true,
            'value'     => function($data)
            {
                return $data->potency->name;
            },
            'filter'    => $potencies,
        ],
        [
            'attribute' => 'vial_id',
            'label'     => '容器',
            'visible'   => in_array($target, $flowers) ? false : true,
            'value'     => function($data)
            {
                return $data->vial->name;
            },
            'filter'    => in_array($target, $flowers) ? false : $vials,
        ],
    ],
])?>

  </div>

  <div class="col-md-3">
      <?= $this->render('recipe-item-grid',['model'=>$recipe]) ?>
      <?= Html::a(' ', Url::current(), ['class'=>'glyphicon glyphicon-repeat','title'=>'再読込み']) ?>
  </div>
</div>
