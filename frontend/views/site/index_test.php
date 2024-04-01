<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/index.php $
 * $Id: index.php 4072 2018-11-29 07:51:10Z kawai $
 *
 * @var $this yii\web\View
 * @var $shops array of common\models\Branch
 */

$this->title = Yii::$app->name;

$current = sprintf('/%s/%s',$this->context->id, $this->context->defaultAction);
$defaultTag = 'hot';
?>
<div class="site-index">

    <div class="body-content">

    <div id="w4" class="list-view Cat-Area">

    <?= \frontend\widgets\CategoryNav::widget() ?>

    </div>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => \common\models\Information::find()->expired(false)->orderBy(['pub_date' => SORT_DESC, 'update_date' => SORT_DESC]),
            'pagination' => ['pageSize'=>5],
        ]),
        'id'      => 'w1',
        'layout'  => '{items}{pager}',
        'caption' => '<h2><span>Information</span></h2>',
        'emptyText' => 'ただいま新着情報はありません',
        'options' => ['class'=>'grid-view Info-Area'],
        'headerRowOptions' => ['style' => 'display:none'],
        'tableOptions' => ['class'=>'table'],
        'columns' => [
            [
                'attribute'     => 'pub_date',
                'format'        => 'date',
                //'value'         => function($data){ return date('Y-m-d (D)', strtotime($data['date'])); },
                'contentOptions'=> ['class'=>'Info-Date'],
            ],
            [
                'attribute' => 'content',
                'format'    => 'html',
                'value'     => function($data){ return $data->renderContent(); },
            ],
        ],
    ]); ?>

    <div class="Info-Area">
        <h2><span>オススメ</span></h2>
<div id="w99" class="list-view Item-Area">
<div class="col-md-4">

    <h3>
    <div class="center_img thum_box thum_img">
        <a href="/index.php/hj/remedy/S-Corona?vid=4#combination"><img src="/assets/images/2500173601021_5136.jpg" alt="サポートCorona" title="サポートCorona" style="width:210px; height:210px"><span class="centering"></span></a>
    </div>
    </h3>
    <p>
    <?= Html::a('ホメオパシージャパン株式会社',['/hj'],['class'=>'small','style'=>'color:#999']) ?><br>
    <a href="/index.php/hj/remedy/S-Nenma?vid=4#combination">サポートCorona</a>
    </p>

</div">
<div class="col-md-4">

    <h3>
    <div class="center_img thum_box thum_img">
        <a href="/index.php/hj/remedy/S-Nenma?vid=4#combination"><img src="/assets/images/2500173401027_5134.jpg" alt="サポートNenma" title="サポートNenma" style="width:210px; height:210px"><span class="centering"></span></a>
    </div>
    </h3>
    <p>
    <?= Html::a('ホメオパシージャパン株式会社',['/hj'],['class'=>'small','style'=>'color:#999']) ?><br>
    <a href="/index.php/hj/remedy/S-Corona?vid=4#combination">サポートNenma</a>
    </p>

</div">
<div class="col-md-4">
 
    <h3>
    <div class="center_img thum_box thum_img">
        <a href="/index.php/hj/tincture/MT%29S-Corona?vid=7#Φ"><img src="/assets/images/4589572674506_5108.jpg" alt="MT)サポートφCorona" title="MT)サポートφCorona" style="width:210px; height:210px"><span class="centering"></span></a>
    </div>
    </h3>
    <p>
    <?= Html::a('ホメオパシージャパン株式会社',['/hj'],['class'=>'small','style'=>'color:#999']) ?><br>
    <a href="/index.php/hj/tincture/MT%29S-Corona?vid=7#Φ">MT)サポートφCorona</a>
    </p>

</div>
<div class="col-md-4">
 
    <h3>
    <div class="center_img thum_box thum_img">
        <a href="/index.php/hj/remedy/Ars?vid=4#30C"><img src="/assets/images/4560320781455_5119.jpg" alt="Ars アーセニカム 30C 大" title="Ars アーセニカム 30C 大" style="width:210px; height:210px"><span class="centering"></span></a>
    </div>
    </h3>
    <p>
    <?= Html::a('ホメオパシージャパン株式会社',['/hj'],['class'=>'small','style'=>'color:#999']) ?><br>
    <a href="/index.php/hj/remedy/Ars?vid=4#30C">Ars アーセニカム 30C 大</a>
    </p>

</div>
<div class="col-md-4">
 
    <h3>
    <div class="center_img thum_box thum_img">
    <div>
        <a href="/index.php/hj/remedy/Ars?vid=2#30C"><img src="/assets/images/4560320781899_58.jpg" alt="Ars アーセニカム 30C 小" title="Ars アーセニカム 30C 小" style="width:210px; height:210px"><span class="centering"></span></a>
    </div>
    </h3>
    <p>
    <?= Html::a('ホメオパシージャパン株式会社',['/hj'],['class'=>'small','style'=>'color:#999']) ?><br>
    <a href="/index.php/hj/remedy/Ars?vid=2#30C">Ars アーセニカム 30C 小</a>
    </p>

</div>

        <?= \frontend\widgets\HotProductList::widget([
            'tag' => 'recommend',
            'options'      => ['class' => 'list-view Item-Area'],
            'layout'       => '{items}{pager}',
        ])?>
</div>
        <h2><span>売れてます</span></h2>
<div class="col-md-4">

    <h3>
    <div class="center_img thum_box thum_img">
        <a href="/index.php/hj/tincture/MT%29S-Corona?vid=7#Φ"><img src="/assets/images/4589572674506_5108.jpg" alt="MT)サポートφCorona" title="MT)サポートφCorona" style="width:210px; height:210px"><span class="centering"></span></a>
    </div>
    </h3>
    <p>
    <?= Html::a('ホメオパシージャパン株式会社',['/hj'],['class'=>'small','style'=>'color:#999']) ?><br>
    <a href="/index.php/hj/tincture/MT%29S-Corona?vid=7#Φ">MT)サポートφCorona</a>
    </p>

</div>

        <?= \frontend\widgets\HotProductList::widget([
            'tag' => 'hot',
            'options'      => ['class' => 'list-view Item-Area'],
            'layout'       => '{items}{pager}',
        ])?>
    </div>

    </div>

    <p class="hint-block text-center" style="vertical-align: middle;">
        <img alt="STOP未成年者飲酒" src="/img/stop.png" style="opacity:0.8">
        当モールでは20歳以上の年齢であることを確認できない場合には酒類を販売いたしません。
    </p>

</div>
