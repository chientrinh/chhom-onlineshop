<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/site/static/sitemap.php $
 * $Id: sitemap.php 4258 2020-04-28 11:45:01Z mori $
 */
use \common\models\Company;

use \yii\helpers\Html;

$title = "サイトマップ";
$this->title = sprintf("%s | %s", $title, Yii::$app->name);

$this->params['breadcrumbs'][] = ['label'=>$title,'url'=>\yii\helpers\Url::current()];

$companies = \common\models\Company::find()->all();
$companies = \yii\helpers\ArrayHelper::map($companies, 'company_id', 'key');
$mships    = \common\models\Membership::find()->all();
$mships    = \yii\helpers\ArrayHelper::map($mships,'membership_id','name','company_id');
$mshipItems = [];
foreach($companies as $company_id => $key)
{
    $mshipItems[$company_id] = [
        'label' => strtoupper($key),
        'url'   => ['/customer','index','company'=>$company_id],
        ];

    $child = [];
    if(isset($mships[$company_id]))
    foreach($mships[$company_id] as $mship_id => $name)
        $child[] = [
            'url'   => ['/customer','membership'=>$mship_id ],
            'label' => $name];

    $mshipItems[$company_id]['items'] = $child;
}
?>
<?php
$menuItems = [
    '実店舗'=> [
        'items'=>[
            ['url'=>['/casher/default/create'],'label'=>'レジ'],
            ['url'=>['/casher/default/index'],'label'=>'本日の売上'],
            ['url'=>['/casher/default/stat'],'label'=>'集計'],
//            ['url'=>['/casher/transfer/index'],'label'=>'店間移動'],
            ['url'=>['/casher/inventory/index'],'label'=>'棚卸'],
            ['url'=>['/casher/default/setup'],'label'=>'設定'],
        ]],
    '熱海'=>[
        'items'=>[
            ['url'=>['/casher/atami/create'],'label'=>'受注'],
            ['url'=>['/casher/atami/index'],'label'=>'本日の注文'],
            ['url'=>['/casher/atami/stat'],'label'=>'集計'],
//            ['url'=>['/casher/transfer/index'],'label'=>'店間移動'],
            ['url'=>['/casher/inventory/index'],'label'=>'棚卸'],
        ]],
    '六本松'=>[
        'items'=>[
            ['url'=>['/casher/ropponmatsu/create'],'label'=>'受注'],
            ['url'=>['/casher/ropponmatsu/index'],'label'=>'本日の注文'],
            ['url'=>['/casher/ropponmatsu/stat'],'label'=>'集計'],
//            ['url'=>['/casher/transfer/index'],'label'=>'店間移動'],
            ['url'=>['/casher/inventory/index'],'label'=>'棚卸'],
            ['url'=>['/casher/stock/index'],'label'=>'在庫'],
        ]],
    'イベント'=> [
        'items'=>[
            ['url'=>['/event/admin/index'],'label'=>'イベント一覧'],
        ]],
    'とらのこ会'=> [
        'items'=>[
            ['url'=>['/member/toranoko/index'],'label'=>'会員の一覧'],
            ['url'=>['/member/toranoko/create'],'label'=>'入会手続き','items'=>[
                    ['url'=>['/site/file','name'=>'./toranokoapplication.doc'],'label'=>'申込書']
            ]],
            ['url'=>['/member/oasis/index'],'label'=>'会報誌「オアシス」'],
        ]],
    '健康相談'=>[
        'items'=>[
            ['label'=>'相談会', 'items'=> [
                ['url'=>['/sodan/interview/index','time'=>-1],'label'=>'履歴'],
                ['url'=>['/sodan/interview/index','time'=> 0],'label'=>'本日の相談会'],
                ['url'=>['/sodan/interview/index','time'=> 1],'label'=>'予定'],
                ['url'=>['/sodan/interview/create'],          'label'=>'予定を追加'],
            ]],
            ['label'=>'クライアント', 'items'=> [
                ['label' => '本部相談会クライアント', 'url' => ['/sodan/client/index']],
                ['label' => 'とらのこ・全員', 'url' => ['/member/toranoko/index']],
                ['label' => '豊受モール・全員', 'url' => ['/member/default/index']],
            ]],
            ['label'=>'ホメオパス', 'items'=> [
                ['label' => '本部ホメオパス',     'url' => ['/sodan/homoeopath/index']],
            ]],
            ['label'=>'予約', 'items'=> [
                ['label' => 'カレンダー', 'url' => ['/sodan/calendar/index']],
                ['url'=>['/sodan/wait-list/index'],'label'=>'キャンセル待ち'],
            ]],
            ['label'=>'お会計', 'url'=>['/sodan/purchase/index']],
            ['label'=>'その他', 'items'=> [
                ['url'=>['/sodan/stat/index'],'label'=>'月次集計'],
                ['url'=>['/recipe'],'label'=>'適用書'],
                ['url'=>['/karute'],'label'=>'カルテ(webdb20)'],
            ]],

        ]],

    '商品'=>[
        'items'=>[
            ['url'=>['/product/index','company'=>Company::PKEY_HE],'label'=>'HE'],
            ['url'=>['/product/index', 'company' => Company::PKEY_HJ],'label'=>'HJ',  'items'=>[
                ['url'=>['/remedy'],'label'=>'レメディー'],
                ['url'=>['/remedy-stock'],'label'=>'既製品'],
                ['url'=>['/remedy-vial'],'label'=>'容器'],
                ['url'=>['/remedy-potency'],'label'=>'ポーテンシー'],
                ['url'=>['/remedy-category-description'],'label'=>'レメディー共通補足説明'],
                ['url'=>['/remedy-price-range-item'],'label'=>'価格設定'],
                ['url'=>['/remedy-price-range'],'label'=>'（価格帯）'],
                ['url'=>['stock/default/index'],'label'=>'レメディー在庫'],
            ]],
            ['url'=>['/product/index','company'=>Company::PKEY_HP],'label'=>'HP', 'items' => [
                ['url'=>['book/index'],'label'=>'書誌'],
            ]],
            ['url'=>['/product/index','company'=>Company::PKEY_TY],'label'=>'TY', 'items' => [
                ['url' => ['/vegetable/index'], 'label' => '野菜'],
            ]],
            ['url'=>['/subcategory/map'],'label'=>'サブカテゴリ'],
            ['url'=>['/product-pickcode/index'],'label'=>'ピックコード'],
            ['url'=>['/jancode/index'],'label'=>'JANコード'],
            ['url'=>['/product-master/index'],'label'=>'商品 表示名＆表示順 管理'],
//            ['url'=>['/offer-seasonal/index'],'label'=>'ご優待' , 'items'=>[
//                ['url'=>['/offer/index'],'label'=>'初期値'],
//            ]],
            ['url'=>['/inventory/index'],'label'=>'棚卸'],
            ['url'=>['/information/index'],'label'=>'お知らせ'],
            ['url'=>['/product/recommend'],'label'=>'おすすめ商品'],
        ],
    ],

    '顧客'=>[
        [
        'label'=>'会員区分', 'url'=>['/customer-grade'],
        'items'=> [
            ['url'=>['/customer','grade'=>1],'label'=>'会員：(あ)スタンダード'],
            ['url'=>['/customer','grade'=>2],'label'=>'会員：(か)スペシャル'],
            ['url'=>['/customer','grade'=>3],'label'=>'会員：(さ)スペシャルプラス'],
            ['url'=>['/customer','grade'=>4],'label'=>'会員：(た)プレミアム'],
            ['url'=>['/customer','grade'=>5],'label'=>'会員：(な)プレミアムプラス'],
        ]],
        [
            'label' => "所属", 'url'=>['/membership'], 'items' => $mshipItems,
        ],
        ['url'=>['/webdb/customer/index','db'=>'webdb18'],'label'=>'webdb18'],
        ['url'=>['/webdb/customer/index','db'=>'webdb20'],'label'=>'webdb20'],

        ['url'=>['/membercode'],'label'=>'会員証'],
        ['url'=>['/facility'],  'label'=>'提携施設'],
        ['url'=>['/agency-office'],'label'=>'代理店・請求先'],
        ['url'=>['/agency-rating'],'label'=>'代理店・割引率'],
        ['url'=>['/customer-info'],'label'=>'付記'],
        ['url'=>['/customer-campaign'], 'label'=>'スペシャルキャンペーン'],
    ],

    '経理'=>[
        'items' => [
            ['label'=>'請求書の発行','url' => ['/invoice/admin']],
            ['label'=>'入金確認',   'url' => ['/invoice/finance']],
            ['label'=>'振替結果',   'url' => ['ysd/trs/index']],
        ],
    ],

    '統計'=>[
        'items' => [
            ['label'=>'売上','url' => ['/purchase']],
            ['label'=>'売上分析','url' => ['/purchase-survey']],
            ['label'=>'適用書','url' => ['/recipe']],
            ['label'=>'ポイント付与','url' => ['/pointing']],
            ['label'=>'webdb18','items' => [
                ['label'=>'顧客', 'url' => ['/webdb/customer/index','db'=>'webdb18']],
                ['label'=>'酒量の集計','url' => ['/webdb/liquor']],
                ['label'=>'販売履歴',  'url' => ['/webdb/history']],
            ]],
        ]
    ],

    '保守'=>[ 'items'=>[
        ['url'=>['/staff'], 'label'=>'従業員', 'items' => [['label' => '役割', 'url' => ['/role/index']]]],
        ['url'=>['/branch'], 'label'=>'拠点'],
        ['url'=>['/company'], 'label'=>'会社'],
        ['url'=>['/category'], 'label'=>'カテゴリー'],
        ['url'=>['/payment'], 'label'=>'支払い区分'],
        ['url'=>['/customer-grade'], 'label'=>'会員区分'],
        ['url'=>['/offer'],'label'=>'ご優待(初期値)'],
        ['url'=>['/offer-seasonal'],'label'=>'ご優待(拠点ごと)'],
        ['url'=>['/membership'], 'label'=>'所属'],
        ['url'=>['/commission'], 'label'=>'手数料'],
        ['url'=>['/mail-log'], 'label'=>'メール送信履歴'],
        ['url'=>['/change-log'], 'label'=>'DB操作履歴'],
        ['url'=>['/barcode'], 'label'=>'バーコード'],
        ['label'=>'口座振替','url' => ['ysd/default/index'], 'items' => [
            ['label'=>'登録依頼',   'url' => ['ysd/rrq/index']],
            ['label'=>'登録結果',   'url' => ['ysd/rrs/index']],
            ['label'=>'振替依頼',   'url' => ['ysd/trqgenerate/index']],
            ['label'=>'振替結果',   'url' => ['ysd/trs/index']],
            [], [], []
        ]],
    ]],

    'キャンペーン'=>[
        'items' => [
            ['label'=>'キャンペーン','url' => ['/campaign']],
            ['label'=>'イベント参加者限定キャンペーン','url' => ['/event-campaign']],
        ],
    ],
    'ライブ配信'=>[
        'items' => [
            ['label'=>'ライブ配信管理','url' => ['/streaming']],
            ['label'=>'会員ランク別商品価格','url' => ['/product-grade']],
            ['label'=>'ライブ配信購入情報','url' => ['/streaming-buy']],
        ],
    ],
    'ライブ配信追加情報'=>[
        'items' => [
            ['label'=>'ライブ配信追加情報','url' => ['/live-info']],
            ['label'=>'ライブ配信追加情報_商品リンク','url' => ['/live-item-info']],
        ],
    ],    

];
$headerUrl = [
    '実店舗' => ['/casher/default/index'],
    '熱海'   => ['/casher/atami/index'],
    '六本松' => ['/casher/ropponmatsu/index'],
    '健康相談' => ['/sodan/admin/index'],
    '商品'   => ['/product' ],
    '顧客'   => ['/customer'],
];
?>

<div class="site-sitemap row col-md-12">

  <?php foreach($menuItems as $key => $items): ?>
      <?php
      if(isset($headerUrl[$key]))
          $h = Html::a($key, $headerUrl[$key]);
      else
          $h = $key;
      ?>

  <div class="list-group col-md-4 col-sm-6">
    <div class="panel panel-default">
      <div class="panel-heading"><?= $h ?></div>
      <div class="panel-body">

        <?= \yii\widgets\Menu::widget([
            'items'           => isset($items['items']) ? $items['items'] : $items,
            'activateItems'   => true,
            'activateParents' => true,
        ]);
        ?>

      </div>
    </div>
  </div>

  <?php endforeach ?>

</div>
