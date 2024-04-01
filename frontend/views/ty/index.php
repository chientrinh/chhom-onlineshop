<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/ty/index.php $
 * $Id: index.php 2376 2016-04-06 08:55:32Z mori $
 */

use \yii\helpers\Html;
use \yii\helpers\Url;

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
			<p class="mainLead">人・作物・環境、すべてが喜ぶ自然農の確立と、人の真の健康を「食」の面からサポートします</p>

				<div class="col-md-12" id="company-message">
					<h2>ごあいさつ　日本豊受自然農からのメッセージ</h2>
					<p>食こそがすべての源。心と体をすこやかに保つには、食養生がいちばんです。古来より、我々日本人は食物の栄養を病気の予防や治療、健康維持に用いて来ました。そうした先人の知恵をひもとき、日本農業の原点に立ち返るために、農薬や化学肥料、遺伝子組み換えの種を排除した独自の自然農法を確立しました。野菜やハーブの持つ偉大な力を最大限に引き出す、日本豊受自然農です。<br>
				    自然、動植物、菌類など万物と共生しながら行う農業は、大いなるものへの感謝の念も沸き起こします。日本農業の復興は、日本人が失ってしまった信仰心を取り戻すことにも通じます。すべての日本人が真の健康を手にし、誇りをもって生きることを願って、私は今日も畑に立ちます。</p>
		            <p align="right"> <span>日本豊受自然農代表 由井寅子</span></p>
					</div>
			<div class="col-md-12" id="company-overview">
					<h2>会社概要</h2>
					<table summary="会社概要" id="FormTable" class="table table-bordered">
						<tbody>
							<tr>
								<th>社名</th>
								<td>農業生産法人 日本豊受自然農株式会社</td>
							</tr>
							<tr>
								<th>代表者名</th>
								<td>由井 寅子</td>
							</tr>
							<tr>
								<th>事業所</th>
								<td>函南本社：<br>
									〒419-0107 静岡県田方郡函南町平井1741-61<br>
									Tel. 055-945-0210（代表）<br>
								洞爺支社：<br>
								〒052-0105 北海道有珠郡壮瞥町仲洞爺60-1<br>
								Tel. 0142-66-7100（代表）</td>
							</tr>
							<tr>
								<th>設立</th>
								<td>2011年10月（創立 2002年11月）</td>
							</tr>
							<tr>
								<th>事業内容</th>
								<td>・農薬と化学肥料をまったく使わず、作物と土壌の生命力を最大限に引き出す「ホメオパシー自然農法」の実践。<br>
								  ・自社で生産した、安全・安心な野菜・穀類・ハーブを使用して、健康増進に役立つ美味しい加工食品の製造。<br>
								  ・主要原料に、自社で生産した農薬・化学肥料不使用のハーブ・野菜を使用し、石油由来原料を完全に排除した「木の花の咲くや生草花」シリーズを中心に、肌や髪が本来の輝きを取り戻し、その人がその人らしくあるためのお手伝いをする化粧品の製造。<br>
							    ・日本豊受自然農が取り組んでいる、作物の生命力を最大限に引き出す自然農の技術をご家庭でも役立てていただけるように定期的に講習会を開催。など</td>
							</tr>
						</tbody>
					</table>

			</div>				

		</div>
	</div>
</div>

