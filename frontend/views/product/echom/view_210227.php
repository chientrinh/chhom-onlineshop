<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\ProductMaster;
use common\models\Stock;
use common\models\ProductGrade;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/product/view.php $
 * $Id: view.php 3045 2016-10-29 04:05:16Z mori $
 */

$master = ProductMaster::findOne(['product_id'=>$model->product_id]);
$this->params['breadcrumbs'] = [];
$this->params['breadcrumbs'][] = ['label' => $model->category->name, 'url' => ['/category/24']];
$this->params['breadcrumbs'][] = ['label' => $model->name];

// prepare images
$slider = [];
$pager  = [];
if(! $model->images)
{
    $img_src  = Url::to('@web/img/default.jpg');
    $slider[] = sprintf('<li><img src="%s" alt="%s" style="max-width:270px"></li>', $img_src, $model->name);
    $pager[]  = sprintf('<span><a data-slide-index="%d" href=""><img src="%s" alt="%s" ></a></span>', count($pager), $img_src, $model->name);
}
else foreach($model->images as $image)
{
    $px   = 300;
    $path = Yii::getAlias("@webroot/assets/images/{$px}/{$image->basename}");
    $image->exportContent($path, $px, false);

    $path = Yii::getAlias("@web/assets/images/{$px}/{$image->basename}");

    $slider[] = sprintf('<li><a href="%s"><img src="%s" alt="%s" class="btn" style="max-width:270px"></a></li>', Url::to(['view-image','id'=>$model->product_id,'top'=>$image->basename]), $path, $model->name);
    $pager[]  = sprintf('<span><a data-slide-index="%d" href=""><img src="%s" alt="%s" ></a></span>', count($pager), $path, $model->name);
}

$formatter = new \yii\i18n\Formatter();

$customer = \common\models\Customer::findOne(Yii::$app->user->id);
// $grade_id = isset(Yii::$app->user->id) ? \common\models\Customer::currentGrade(Yii::$app->user->id) : null;
$grade = ProductGrade::getGrade($model->product_id,$customer);

if($grade) {
    $price = $grade->price;
    $tax = $grade->tax;
} else {
    $price = $model->price;
    $tax = $model->tax;
}
?>

<div class="col-md-4 product-photo">

    <ul class="bxslider">
    <?= implode('', $slider) ?>
    </ul>
    <div id="bx-pager">
    <?= implode('', $pager) ?>
    </div>

</div>
<div class="col-md-8 product-detail">
<br />
    <h3>
        <span class="Mame"><?= $model->name ?></span>
    </h3>
    <p class="Price">価格： <em><?= $formatter->asCurrency($price) ?></em>（税別）
    <?= $formatter->asCurrency($price + $tax) ?> (税込)<br>
    消費税： <?= $formatter->asCurrency($tax) ?><br>

<?php if($pointRate): ?>
    <!-- <span class="text-info">ポイント： <?= $formatter->asInteger($pointRate) ?>%<br></span> -->
<?php endif ?>
<?php if($discountRate): ?>
    <span class="text-info">ご優待： <?= $formatter->asInteger($discountRate) ?>%<br></span>
<?php endif ?>
    </p>
<p><strong><font size="4" color="red">自然を感じて学ぶ「ナチュラルオーガニクスライフ」シリーズ</font></strong><br/>
<strong><font color="red">【WHAW世界ホメオパシー認識週間】特別企画</font></strong><br/><br/>
■特別講演　講師：由井寅子ホメオパシー博士<br/>

■配信日時<br/>
<strong><font size="4" color="blue">2021/4/10（土）10:00～15:00</font></strong><br/><br/>
■午前の部　10:00～13:00<br/>
菊田雄介　JPHMA認定ホメオパス「日本のグリーンメディスンはZENホメオパシー」<br/>
寺澤カンナ　JPHMA認定ホメオパス「日本の自然民間療法」<br/>
豊受自然農　洞爺からのグリーン・メディスン<br/>
高野弘之 先生（豊受クリニック院長）「コロちゃんワクチンについて語る」<br/>
<strong><font size="4" color="green">WHAW特別講演　とらこ先生「コロちゃん問題を自然な"医"と"食"で乗り越える」</font></strong><br/><br/>

■午後の部　14:00～15:00<br/>
日本の植物発酵食品「御古菌入り豊受大豆と豊受糀を使った味噌づくり体験」（ライブ会場では味噌づくり体験に参加できます）<br/>
・静岡函南の麦畑からライブ中継。畑から豊受農民のお話をお楽しみください。<br/>
・豊受自然農「黒大豆みそ」のお話<br/>
・手作りみそ実践者のお話<br/><br/>

■参加費（税込）<br/>
一般：2000円<br/>
豊受会員：1500円<br/>
CHhom学生・JPHMA会員：1000円<br/><br/>

■参加会場<br/>
・CHhom東京校（ライブ会場）<br/>
・CHhom大阪校（中継会場）<br/>
・自宅受講（オンライン配信）<br/><br/>

■注意事項<br/>
視聴映像の録画・録音はご遠慮いただいております。<br/><br/>
※視聴方法：CHhomオンラインショップ（マイページ）から視聴が可能です。https://ec.homoeopathy.ac/profile<br/>
　　　　　　購入時のID/PWでログイン後、マイページをご確認ください。<br/>

※推奨環境：スマートフォン、タブレットiOS 11.0以降(Google Chrome最新バージョン)/Android OS 5.0以降(Google Chrome最新バージョン)<br/>
　　　　　　パソコンWindows 10以上(Google Chrome最新バージョン)/ MacOS 10.9以上(Google Chrome最新バージョン)<br/>
　　　　　　また、端末やソフトウェアの故障や損傷がないこともご確認ください。推奨環境以外は一切のサポートを致しかねます。<br/>

※通信環境：動画視聴には高速で安定したインターネット回線が必要です。<br/>
　　　　　　圏外や電波が弱い場所ではないか、パケット残容量はあるかを必ず事前にご確認ください。<br/>
　　　　　　お客様の通信状況により視聴できない場合でも、一切の責任を負いません。<br/>

※安全対策：ログインID/PWは、お客様の責任において厳重に管理し、他人には絶対に教えないでください<br/><br/>

▼ライブ配信に関するお問い合わせ<br/>
E-mail: ec-chhom@homoeopathy.ac<br/><br/>

<strong><font size="5" color="red">↓下記、『参加会場』を選択してください。</font></strong><br/>
■東京校　　TEL：03-5797-3250<br/>
■大阪校　　TEL：06-6368-5355<br/>
■自宅受講（オンライン配信）<br/></p>


           <?php if($model->isLiquor()): ?>
               <p>※この商品はお酒です。20歳以上の年齢であることを確認できない場合には酒類を販売いたしません。</p>
           <?php endif ?>

<?php if((1 == $model->in_stock) && isset($stockQty) && ($stockQty <> 0) && ($stockQty < Stock::ALERT_QTY)): ?>
<!-- <p class="alert alert-danger" >あと <em><font size="5"><?= $stockQty ?></font></em>点 在庫があります</p> -->
<?php endif ?>

<div class="detail-form-content">
<?php if(0 == $model->in_stock): ?>
<p class="Cart">
  <span class="btn alert-danger">完売御礼（入荷次第、再開します）</span>
</p>
<?php else: ?>

  <?= $this->render('_form',['model'=>$model, 'stockQty' => $stockQty, 'isFavorite' => $isFavorite]) ?>

<?php endif ?>

</div>


<?php
if($model->descriptions)
  echo \yii\widgets\ListView::begin([
    'dataProvider'  => new \yii\data\ArrayDataProvider([
        'allModels'  => $model->descriptions,
            'pagination' => false,
        ]),
    'itemView'     => function ($model, $key, $index, $widget){return sprintf('<p><strong>%s</strong><br>%s</p><hr>', $model->title, $model->body); },
])->renderItems();
?>

<table class="table table-striped table-bordered">

<?php

if($model->descriptions)
    $items = $model->descriptions;
 else
    $items = [];


// JANコードがセットされている場合は表示する
if($j = $model->productJan)
        // JANコード（45 or 49で始まるコード）は表示するが、内部コード（24 or 25 で始まるコード）は非表示とする。
        if ( substr($j->jan, 0, strlen("4")) == "4")
            $items[] = (object)['title'=>"JANコード", 'body'=>$j->jan];

echo \yii\widgets\ListView::begin([
    'dataProvider'  => new \yii\data\ArrayDataProvider([
        'allModels'  => $items,
            'pagination' => false,
        ]),
    'itemView'     => function ($model, $key, $index, $widget){return sprintf('<tr data-key="%d"><th>%s</th><td>%s</td></tr>', $key, preg_replace('/\(.*\)/','<span class="mini">${0}</span>',$model->title), $model->body); },
])->renderItems();
 ?>

</table>


</div>
