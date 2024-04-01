<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/facility/_map.php $
 * $Id: _map.php 3201 2017-03-01 01:43:41Z kawai $
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
?>

<div style="position:absolute;top:100px;">
    <p>
        <label>地域で探す</label>
    </p>
    <p class="help-block"><strong>検索したい地域ボタンをクリックしてください</strong></p>
    <?php $url = Url::to(['index']) ?>
    <?= Html::submitButton('全域から検索',['class'=>'btn btn-lg btn-block btn-default','onclick'=>"this.form.action='{$url}'"]) ?>
</div>

<?= Html::img('@web/img/japan.png',['alt'=>"日本地図", 'usemap'=>"#Map", 'class' => "map_image"]) ?>

<map name="Map" id="Map">

    <area alt="北海道" title="北海道" href="<?= Url::to(['view','id'=>1]) ?>" shape="rect" coords="475,4,596,105" />
    <area alt="青森" title="青森" href="<?= Url::to(['view','id'=>2]) ?>" shape="rect" coords="475,119,576,148" />
    <area alt="岩手" title="岩手" href="<?= Url::to(['view','id'=>3]) ?>" shape="rect" coords="538,152,576,181" />
    <area alt="宮城" title="宮城" href="<?= Url::to(['view','id'=>4]) ?>" shape="rect" coords="539,185,576,214" />
    <area alt="秋田" title="秋田" href="<?= Url::to(['view','id'=>5]) ?>" shape="rect" coords="475,152,535,181" />
    <area alt="山形" title="山形" href="<?= Url::to(['view','id'=>6]) ?>" shape="rect" coords="475,185,535,214" />
    <area alt="福島" title="福島" href="<?= Url::to(['view','id'=>7]) ?>" shape="rect" coords="475,218,577,247" />
    <area alt="茨城" title="茨城" href="<?= Url::to(['view','id'=>8]) ?>" shape="rect" coords="538,284,577,312" />
    <area alt="栃木" title="栃木" href="<?= Url::to(['view','id'=>9]) ?>" shape="rect" coords="539,251,577,280" />
    <area alt="群馬" title="群馬" href="<?= Url::to(['view','id'=>10]) ?>" shape="rect" coords="475,251,535,280" />
    <area alt="埼玉" title="埼玉" href="<?= Url::to(['view','id'=>11]) ?>" shape="rect" coords="476,284,535,312" />
    <area alt="千葉" title="千葉" href="<?= Url::to(['view','id'=>12]) ?>" shape="rect" coords="540,316,577,379" />
    <area alt="東京" title="東京" href="<?= Url::to(['view','id'=>13]) ?>" shape="rect" coords="475,317,535,346" />
    <area alt="神奈川" title="神奈川" href="<?= Url::to(['view','id'=>14]) ?>" shape="rect" coords="475,351,535,379" />
    <area alt="新潟" title="新潟" href="<?= Url::to(['view','id'=>15]) ?>" shape="poly" coords="415,174,452,174,453,218,471,218,471,247,434,247,434,204,415,203" />
    <area alt="富山" title="富山" href="<?= Url::to(['view','id'=>16]) ?>" shape="rect" coords="392,218,430,247" />
    <area alt="石川" title="石川" href="<?= Url::to(['view','id'=>17]) ?>" shape="rect" coords="350,218,387,247" />
    <area alt="福井" title="福井" href="<?= Url::to(['view','id'=>18]) ?>" shape="rect" coords="350,251,387,280" />
    <area alt="山梨" title="山梨" href="<?= Url::to(['view','id'=>19]) ?>" shape="rect" coords="434,316,472,346" />
    <area alt="長野" title="長野" href="<?= Url::to(['view','id'=>20]) ?>" shape="rect" coords="434,251,471,312" />
    <area alt="岐阜" title="岐阜" href="<?= Url::to(['view','id'=>21]) ?>" shape="rect" coords="392,251,430,312" />
    <area alt="静岡" title="静岡" href="<?= Url::to(['view','id'=>22]) ?>" shape="rect" coords="434,350,472,379" />
    <area alt="愛知" title="愛知" href="<?= Url::to(['view','id'=>23]) ?>" shape="rect" coords="392,317,428,346" />
    <area alt="三重" title="三重" href="<?= Url::to(['view','id'=>24]) ?>" shape="rect" coords="351,317,387,346" />
    <area alt="滋賀" title="滋賀" href="<?= Url::to(['view','id'=>25]) ?>" shape="rect" coords="350,284,387,314" />
    <area alt="京都" title="京都" href="<?= Url::to(['view','id'=>26]) ?>" shape="rect" coords="308,284,346,313" />
    <area alt="大阪" title="大阪" href="<?= Url::to(['view','id'=>27]) ?>" shape="rect" coords="266,316,304,346" />
    <area alt="兵庫" title="兵庫" href="<?= Url::to(['view','id'=>28]) ?>" shape="rect" coords="266,284,304,312" />
    <area alt="奈良" title="奈良" href="<?= Url::to(['view','id'=>29]) ?>" shape="rect" coords="307,319,345,346" />
    <area alt="和歌山" title="和歌山" href="<?= Url::to(['view','id'=>30]) ?>" shape="rect" coords="267,351,346,379" />
    <area alt="鳥取" title="鳥取" href="<?= Url::to(['view','id'=>31]) ?>" shape="rect" coords="224,284,261,313" />
    <area alt="島根" title="島根" href="<?= Url::to(['view','id'=>32]) ?>" shape="rect" coords="182,284,220,313" />
    <area alt="岡山" title="岡山" href="<?= Url::to(['view','id'=>33]) ?>" shape="rect" coords="224,317,262,346" />
    <area alt="広島" title="広島" href="<?= Url::to(['view','id'=>34]) ?>" shape="rect" coords="182,317,220,346" />
    <area alt="山口" title="山口" href="<?= Url::to(['view','id'=>35]) ?>" shape="rect" coords="141,284,177,346" />
    <area alt="徳島" title="徳島" href="<?= Url::to(['view','id'=>36]) ?>" shape="rect" coords="212,391,250,420" />
    <area alt="香川" title="香川" href="<?= Url::to(['view','id'=>37]) ?>" shape="rect" coords="212,358,250,386" />
    <area alt="愛媛" title="愛媛" href="<?= Url::to(['view','id'=>38]) ?>" shape="rect" coords="170,358,207,386" />
    <area alt="高知" title="高知" href="<?= Url::to(['view','id'=>39]) ?>" shape="rect" coords="170,391,208,420" />
    <area alt="福岡" title="福岡" href="<?= Url::to(['view','id'=>40]) ?>" shape="rect" coords="47,314,85,346" />
    <area alt="佐賀" title="佐賀" href="<?= Url::to(['view','id'=>41]) ?>" shape="rect" coords="5,314,42,346" />
    <area alt="長崎" title="長崎" href="<?= Url::to(['view','id'=>42]) ?>" shape="rect" coords="5,347,42,377" />
    <area alt="熊本" title="熊本" href="<?= Url::to(['view','id'=>43]) ?>" shape="rect" coords="46,347,84,377" />
    <area alt="大分" title="大分" href="<?= Url::to(['view','id'=>44]) ?>" shape="rect" coords="89,314,126,346" />
    <area alt="宮崎" title="宮崎" href="<?= Url::to(['view','id'=>45]) ?>" shape="rect" coords="89,347,127,409" />
    <area alt="鹿児島" title="鹿児島" href="<?= Url::to(['view','id'=>46]) ?>" shape="rect" coords="5,382,84,409" />
    <area alt="沖縄" title="沖縄" href="<?= Url::to(['view','id'=>47]) ?>" shape="rect" coords="5,425,42,455" />

</map>
    <p>&nbsp;</p>
    <?php $url = Url::to(['view','id'=>48]) ?>
    <?= Html::submitButton('海外',['id' => 'pref_48', 'class'=>'btn btn-sm btn-info','onclick'=>"this.form.action='{$url}'"]) ?>

