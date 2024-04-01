<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/trose/index.php $
 * $Id: index.php 3816 2018-01-11 07:14:07Z naito $
 */

use \yii\helpers\Html;

$this->params['breadcrumbs'] = [
    ['label' => $company->name, 'url' => ['/'.$company->key]],
    ['label' => "会社概要", 'url' => [$this->context->action->id] ],
];

$this->params['body_id']       = 'Company';
$this->title = implode(' | ',\yii\helpers\ArrayHelper::getColumn(array_reverse($this->params['breadcrumbs']),'label')) . ' | ' . Yii::$app->name;

?>

<div class="site-about">

    <p class="pull-right"><?= Html::a("商品一覧",['product']) ?></p>

    <h1 class="mainTitle">会社概要</h1>
    <p class="mainLead">お肌に優しいオーガニックコットンを使用した布ナプキンやふんどしパンツを販売しております。</p>

    <div class="col-md-12" id="company-message">
        <h2>布ナプキンやふんどしパンツの通販【Tommy Rose】</h2>
        <p>
            ふんどしパンツは、ふんどしの機能そのままにおしゃれなデザインに進化した新しいタイプのショーツです。 肌ストレスを軽減する布ナプキンを試してみませんか。 布ナプキンを使用して作ったケーキデコレーション「ケーキナプキン」はギフトに最適です。 ニーズやご予算に合わせて作成致しますので、お気軽にお問合せください。
        </p>

    </div>
    <div class="col-md-12" id="company-overview">
        <h2>会社概要</h2>
        <table summary="会社概要" id="FormTable" class="table table-bordered">
            <tbody>
                <tr>
                    <th>社名</th>
                    <td>Ｔｏｍｍｙ　Ｒｏｓｅ</td>
                </tr>
                <tr>
                    <th>代表者名</th>
                    <td>店舗運営責任者:上田 登美恵(ネットショップ部)</td>
                </tr>
                <tr>
                    <th>事業所</th>
                    <td>
                        〒<?= $company->zip ?> <?= $company->addr ?><br>
                        TEL:<?= $company->tel ?> <br>
                        FAX:<?= $company->tel ?> <br>
                </tr>
                <tr>
                    <th>営業時間</th>
                    <td>
                        <p>ご注文は２４時間受け付けております。</p>
                        <p>店舗へのお問合せは、下記の時間帯にお願いいたします。</p>
                        <p>平日　11:00－18:00</p>
                        <p>※土日祝祭日はお休みをいただいております。</p>
                    </td>
                </tr>
                <tr>
                    <th>送料について</th>
                    <td>
                        ご注文はすべてゆうメール代引きでお送りします。 <br>
                       １番お得な代引き発送です。<br>
                        時間指定なし：関東で２〜３日掛かります。<br>
                        <p>
                            ●商品合計（税抜）が１万円未満で７００円 <br>
                            ●商品合計（税抜）が１万円以上で送料無料 <br>
                             <br>
                           ★お急ぎの方は、【ゆうぱっく代引き】も対応いたします<br>
                           時間指定あり、関東で翌日１４時以降の配達となります<br>
                            <br>
                           ご希望の方は、「ゆうぱっく代引き：希望」と備考欄にご記載下さい。<br>
                           送料金額訂正後、ショップよりメールでご連絡いたします。<br>

                           送料の目安：ゆうぱっく料金＋代引き手数料３９０円<br>
                           北海道　１２００円＋３９０円＝１５９０円<br>
                           東北　　９００円＋３９０円＝１２９０円<br>
                           関東　　６１０円＋３９０円＝１０００円<br>
                           東海　　５６０円＋３９０円＝９５０円<br>
                           関西・四国・中国・九州＝５１０円＋３９０円＝９００円<br><br>
                           
                           ★【ゆうちょ振込】も対応致します。<br>
                           ご希望の方は備考欄に、「ゆうちょ振込」と記載下さい。<br>
                           金額訂正をして別途ご連絡致します。<br><br>

                           【ゆうちょ口座】<br>
                            [記号]１５５９０  [番号]１１５９９４６１<br>
                            [口座名義] ウエダ　トミエ<br>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>返品について</th>
                    <td>
                        <p>
                            ●返品を受け付ける条件<br>
                            未開封・未使用のもので、商品ご到着後3日以内に電話連絡いただいたもののみお受けいたします。
                        </p>
                        <p>
                            ●返品の送料・手数料の負担について<br>
                            初期不良の場合は当社が負担いたします。
                            お客様都合の場合はお客様にご負担いただきます。</p>

                        <p>
                            ●返金について<br>
                            返品商品到着確認後7日以内にご指定口座にお振込いたします。
                        </p>
                        <p>
                            ●返品連絡先<br>
                            　電話番号：<?= $company->tel ?> <br>
                            　メールアドレス：<?= $company->email ?>  <br>
                            　返送先住所：〒<?= $company->zip ?> <?= $company->addr ?><br>
                            　担当者　　：上田 <br>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
