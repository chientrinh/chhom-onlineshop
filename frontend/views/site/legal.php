<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/legal.php $
 * $Id: legal.php 3879 2018-05-18 08:03:54Z naito $
 *
 * @var $this \yii\web\View
 */

$title = "特定商取引に関する法律に基づく表記";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Legal';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

$homepage = 'http://www.toyouke.com';
?>
<div class="site-about">
    <h1 class="mainTitle"><?= Html::encode($title) ?></h1>

    <p class="mainLead"></p>

			<div class="row">
				<div class="col-md-12">
					<table summary="特定商取引に関する法律に基づく表記" id="mypage-history-list" class="table table-bordered">
						<tbody>
							<tr>
								<th>販売業者</th>
								<td colspan="2">日本豊受自然農株式会社</td>
							</tr>
							<tr>
								<th>代表者</th>
								<td colspan="2">由井　寅子</td>
							</tr>
							<tr>
								<th>運営責任者</th>
								<td colspan="2">吉田　光弘</td>
							</tr>
							<tr>
								<th>住所</th>
								<td colspan="2">〒419-0107<br>
									静岡県田方郡函南町平井1741-61</td>
							</tr>
							<tr>
								<th>電話番号</th>
								<td colspan="2">055-945-0210</td>
							</tr>
							<tr>
								<th>メールアドレス</th>
								<td colspan="2">shopping@toyouke.com</td>
							</tr>
							<tr>
								<th>URL</th>
                                <td colspan="2"><?= Html::a($homepage, $homepage) ?></td>
							</tr>
							<tr>
								<th rowspan="3">商品以外の<br>必要代金</th>
								<td>消費税：</td>
								<td>8％　※2019年10月1日より、10%。飲食料品は軽減税率（8%）が適用されます。</td>
							</tr>
							<tr>
								<td>送料：</td>
								<td>
                                    <strong>商品合計１００００円（税抜）以上で送料無料</strong><br>
※2017/11/15より適用されます。<br>
北海道……………………………………９５０円＋税<br>
北東北（青森、岩手、秋田）…………７２０円＋税<br>
南東北（山形、宮城、福島）…………６００円＋税<br>
関東・信越・北陸・中部・関西………５００円＋税<br>
中国………………………………………６００円＋税<br>
四国・九州………………………………７００円＋税<br>
沖縄……………………………………１４００円＋税<br>
※クール便の場合は、２００円追加になります。<br>
</td>
							</tr>
							<tr>
								<td>代引き手数料：</td>
								<td>
〜１万円……………３００円＋税<br>
〜３万円……………４００円＋税<br>
〜１０万円…………６００円＋税<br>
〜３０万円………１０００円＋税<br>

                                    代引きの場合、商品合計に関わらず、代引き手数料がかかります</strong><br>
</td>
							</tr>
							<tr>
								<th>注文方法</th>
								<td colspan="2">インターネットでのご注文を受け付けます。</td>
							</tr>
							<tr>
								<th>支払方法</th>
								<td colspan="2">代金引換決済　ヤマト運輸　佐川急便</td>
							</tr>
							<tr>
								<th>支払期限</th>
								<td colspan="2">代金引換決済:　商品をお受け取りの際に徴収されます。</td>
							</tr>
							<tr>
								<th>引渡し時期</th>
								<td colspan="2">弊社ホームページにてご案内している発送日時となります。<br>
									(ヤマト運輸　クール宅急便利用　又は　佐川急便利用)</td>
							</tr>
							<tr>
								<th>返品・交換について</th>
								<td colspan="2">商品の欠損、破損に基づく返品依頼及び、商品の品違いによる返品依頼は交換、返品を受け付けます。代替え商品が手配出来ない場合には代金を返金いたします。(送料弊社負担)<br>
									※注意、商品到着後8日をすぎますと商品にトラブルのあった場合でも商品の返品をお受けできませんので、ご了承ください。</td>
							</tr>
							<tr>
								<th>出店企業・団体</th>
								<td colspan="2">日本豊受自然農株式会社<br>ホメオパシージャパン株式会社<br>豊受オーガニクスショップ<br>トミーローズ</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
