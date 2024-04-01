<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/he/index.php $
 * $Id: index.php 4067 2018-11-28 08:10:14Z kawai $
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
    <p class="mainLead">豊受オーガニクスショップでは自然派基礎化粧品や日本豊受自然農の加工食品などを販売しております。
    </p>
    </div>

    <div class="col-md-12" id="company-overview">
        <h2>会社概要</h2>
        <table summary="会社概要" id="FormTable" class="table table-bordered">
            <tbody>
                <tr>
                    <th>社名</th>
                    <td>ホメオパシック・エデュケーション (株)</td>
                </tr>
                <tr>
                    <th>代表者名</th>
                    <td>代表取締役社長　小島洋子
                    </td>
                </tr>
                <tr>
                    <th>設立</th>
                    <td>2008年4月 <br>
                        創業1997年4月（RAH）2010年5月（CHhom）
                    </td>
                </tr>
                <tr>
                    <th>事業内容</th>
                    <td>
                        <ul>
                            <li>ホメオパスの教育・育成・普及・啓蒙活動</li>
                            <li>健康サービス提供をする為のセミナー・講演会の開催</li>
                            <li>健康相談会の運営</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th>主な事業所</th>
                    <td>
                        <p>〒158-0096<br>
                            東京都世田谷区玉川台2-2-3 矢藤第三ビル<br>
                            TEL 03-5797-3011 (代表)<br>
                            FAX 03-5797-3012</p>
                </tr>
                <tr>
                    <th>返品について</th>
                    <td>
                        <p>
                            ■お客様都合の返品依頼<br>
                            原則として購入商品の返品、交換はできかねます。</p>

                        <p>■商品の瑕疵(欠損・破損)に基づく返品依頼及び、商品の品違いによる返品依頼<br>
                            交換・返品をお受けいたします。代替商品が手配できない場合は、代金を返済いたします(送料当社負担)。</p>
                        <p>※注意:商品到着後2週間以内</p>
                        <p>■開封後の商品や一度ご使用になった商品の返品・交換はお受けできませんのでご了承ください。</p>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>                

</div>
