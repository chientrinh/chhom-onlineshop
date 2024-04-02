<?php
   /**
   * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/sodan/views/how-to-use-remedy.php $
   * $Id: how-to-use-remedy.php 3905 2018-05-31 04:51:15Z mori $
   */
    use common\models\RemedyVial;
   ?>
<style>
body, p {
  font-size: 10pt;
}
img {
  width: 30mm;
  height: 30mm;
}
h5 {
  font-size: 16 pt;
  margin:    10 pt;
  padding:    0;
}
.red {
  color:red;
}
.yellow {
  color:black;
  background:yellow;
}
ul li {
    list-style-type: none;
}
</style>
<page>
  <h3 style="text-align:center">レメディーの効果的なとり方の目安</h3>
  <h4 style="text-align:right">ハーネマン「オーガノン」より参考</h4>

  <table>
    <tr>
      <th class="remedy_name" width="60%" style="font-size:10pt">品名</th>
      <th class="remedy_info" width="5%"  style="font-size:10pt">数量</th>
      <th class="remedy_info" width="12%" style="font-size:10pt">目安</th>
      <th class="remedy_info" width="7%" style="font-size:10pt">取り方</th>
      <th class="remedy_info" width="15%" style="font-size:10pt">メモ</th>
    </tr>

  <?php foreach($model->parentItems as $item): ?>
    <?php
        // 取り方の条件
        $take = '';
        switch ($item->vial_id) {
            case RemedyVial::MICRO_BOTTLE:
            case RemedyVial::SMALL_BOTTLE:
            case RemedyVial::MIDDLE_BOTTLE:
            case RemedyVial::LARGE_BOTTLE:
                $take = 'C';
                break;
            case RemedyVial::GLASS_5ML:
            case RemedyVial::ALP_20ML:
                $take = 'A';
                break;
            case RemedyVial::GLASS_20ML:
            case RemedyVial::PLASTIC_SPRAY_20ML:
            case RemedyVial::ALP_100ML:
            case RemedyVial::ORIGINAL_20ML:
            case RemedyVial::ORIGINAL_150ML:
                $take = 'B';
                break;
        }
    ?>
    <tr>
      <td class="remedy_name">
      <?= $item->fullname ?>
      </td>
      <td class="remedy_info"><?= $item->quantity ?></td>
      <td class="remedy_info"><?= $item->instruction ? $item->instruction->name : null ?></td>
      <td class="remedy_info"><?= $take ?></td>
      <td class="remedy_info"><?= $item->memo ?></td>
    </tr>

  <?php endforeach ?>
  </table>
  <br>
  <p>ホメオパスからのとり方の指示を目安におとりください。</p>

  <h5>
      Ａ.＜液体レメディー＞
      <div class="red" style="font-size:9pt">※５ ml ガラス小瓶</div>
  </h5>

  <div style="width:100%;">
  <div style="width:20%;float:left;">
    <img src="<?= $imgA ?>">
  </div>
  <div style="width:80%;">
    <ol>
      <li>
          ビンの底をトントンと2回程度叩く。
      </li>
      <li>
          蓋をあけておいをかぐ（ビンに直接鼻をつけない。子ども、動物、においがわからない方は不要です）。
      </li>
      <li>
          コップに約2cmの水に<span class="red">2</span>滴程度滴下し、<span class="yellow">スプーンで20回かき混ぜて（スプーンは木、竹、陶器の素材をお勧めします）</span>飲む。
      </li>
    </ol>
  <ul>
      <li>
          ※
         子どもはお茶や酵素ジュースなど、とりやすい飲み物に入れておとりいただいてもよいです。
      </li>
      <li>
          ※
        他のマザーチンクチャーやレメディーなどとは混ぜずにおとりください。
      </li>
      <li>
          ※
        希釈用のお水は同じ銘柄の水をご使用いただくことをおすすめします。
      </li>
    </ul>
  </div>
  </div>

  <h5>
      B. ＜液体レメディー＞
      <div class="red" style="font-size:9pt">※20ml ガラス瓶</div>
  </h5>

  <h5>
  </h5>

  <div style="width:100%">
    <div style="width:20%;float:left;">
    <img src="<?= $imgB ?>">
    </div>
    <div style="width:80%;">
      <ol>
        <li>
          ビンの底をトントンと2回程度叩く。
        </li><li>
            ペットボトル500mlに<span class="red">10</span>滴程（子供は<span class="red">5</span>滴）滴下し、よく振って、1日かけてチビチビとる。
        </li>
      </ol>
      <ul>
        <li>
          ※
          希釈用のお水は同じ銘柄の水をご使用いただくことをおすすめします。
        </li>
      </ul>
    </div>
  </div>

  <h5>
      C. ＜砂糖玉レメディー＞
    <div class="red" style="font-size:9pt">※プラスチック瓶</div>
  </h5>

  <div style="width:100%">
  <div style="width:20%;float:left;">
    <img src="<?= $imgC ?>">
  </div>
  <div style="width:80%;">
    <ol>
      <li>
        本人以外はなるべく触れないように1粒を蓋に取り出し、舌下でゆっくりなめ溶かすようにします。
    </li></ol>
      <ul>
        <li>
          ※
            子供や動物に与える場合など、砂糖玉に直に触れるときは、なるべくすばやく。
        </li>
      </ul>
  </div>
  </div>

  <p>&nbsp;</p>

  <p>
  <strong>
    レメディーの取り扱い
  </strong>
  </p>

  <p>&nbsp;</p>

  <p>
      直射日光・香りの強いもの・電磁波の強い電気機器等の付近は避け、キャップをしっかりしめて冷暗所にて保管してください。
  </p>
  <ul>
      <li>
          ×冷蔵庫の中
      </li>
      <li>
          ×パソコン・テレビ・携帯電話等のすぐそば
      </li>
      <li>
          ×香水、エッセンシャルオイルなどと一緒
      </li>
  </ul>
  <p>
  レメディー使用中はコーヒー、ミントなど刺激の強いものはなるべくお避けください。
  </p>
  <p>
      また、前後20分間はなるべく飲食・喫煙などを避けることをお勧めいたします。
  </p>


  <div style="position:absolute; bottom:8mm; width:100%">
    <p>&copy;<?= date('Y') ?> College of Holistic Homoeopathy (CHhom) All Rights Reserved JPHMA 認定校 CHhom  http://www.homoeopathy.ac</p>
  </div>

</page>
