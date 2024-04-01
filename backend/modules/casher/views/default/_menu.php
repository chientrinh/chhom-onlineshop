<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_menu.php $
 * $Id: _menu.php 3827 2018-02-02 07:47:52Z kawai $
 */
// $module_id     = $this->context->module->id;
// $controller_id = $this->context->id;

use \common\models\Branch;
use \common\models\Company;
use \yii\helpers\Html;

$branch = $this->context->module->branch;

if($branch && $branch->isWarehouse())
{
    if(Branch::PKEY_ATAMI == $branch->branch_id)
        Yii::setAlias('@default', 'atami');
    elseif(Branch::PKEY_TROSE == $branch->branch_id)
        Yii::setAlias('@default', 'trose');
    else
        Yii::setAlias('@default', 'ropponmatsu');
}
else
    Yii::setAlias('@default', 'default');

$searchItems = [];

if(! $branch)
    ;
elseif((Branch::PKEY_ATAMI == $branch->branch_id) || $branch->isRemedyShop())
{
    $searchItems = [
        [
            'label' => Html::tag('i',' 36キット補充',['class'=>"glyphicon glyphicon-th"]),
            'url'   => ['@default/search','target'=>'popular'],
        ],
        [
            'label' => Html::tag('i',' セット補充',['class'=>"glyphicon glyphicon-tasks"]),
            'url'   => ['@default/search','target'=>'modular'],
        ],
        [
            'label' => Html::tag('i',' マザーチンクチャー',['class'=>"glyphicon glyphicon-tree-conifer"]),
            'url'   => ['@default/search','target'=>'tincture'],
        ],
        [
            'label' => Html::tag('i',' フラワーエッセンス',['class'=>"glyphicon glyphicon-leaf"]),
            'url'   => ['@default/search','target'=>'flower'],
        ],
        [
            'label' => Html::tag('i',' キット／セット本体',['class'=>"glyphicon glyphicon-briefcase"]),
            'url'   => ['@default/search','target'=>'product'],
        ],
        [
            'label' => Html::tag('i',' レメディー',['class'=>"glyphicon glyphicon-baby-formula"]),
            'url'   => ['@default/search','target'=>'remedy'],
        ],
        [
            'label' => Html::tag('i',' 滴下',['class'=>"glyphicon glyphicon-tint"]),
            'url'   => ['compose'],
        ],
        [
            'label' => Html::tag('i',' 適用書',['class'=>"glyphicon glyphicon-list-alt"]),
            'url'   => ['/recipe/admin/index'],
        ],
        [
            'label' => Html::tag('i',' 雑貨',['class'=>"glyphicon glyphicon-apple"]),
            'url'   => ['@default/search','target'=>'product','category'=>9],
        ],
    ];

    if(Branch::PKEY_ATAMI == $branch->branch_id)
    {
        $searchItems[] = [
            'label' => Html::tag('i',' CHhom商品',['class'=>"glyphicon glyphicon-grain"]),
            'url'   => ['@default/search','target'=>'product','company'=>Company::PKEY_HE],
        ];
        $searchItems[] = [
            'label' => Html::tag('i',' 書籍',['class'=>"glyphicon glyphicon-book"]),
            'url'   => ['@default/search','target'=>'product','company'=>Company::PKEY_HP],
        ];
    }
}
elseif((Company::PKEY_HE == $branch->company_id) && $branch->isChhomShop())
{
    if(Branch::PKEY_HE_TOKYO == $branch->branch_id)
        $searchItems[] = [
            'label' => Html::tag('i',' ご飲食',['class'=>"glyphicon glyphicon-cutlery"]),
            'url'   => ['@default/search','target'=>'product','category'=>5],
        ];

    $searchItems[] = [
        'label' => Html::tag('i',' 商品',['class'=>"glyphicon glyphicon-grain"]),
        'url'   => ['@default/search','target'=>'product'],
    ];
    $searchItems[] = [
            'label' => Html::tag('i',' 書籍',['class'=>"glyphicon glyphicon-book"]),
            'url'   => ['@default/search','target'=>'product','company'=>Company::PKEY_HP],
    ];
}
elseif(Company::PKEY_TY == $branch->company_id)
    $searchItems = [
        [
            'label' => Html::tag('i',' 商品',['class'=>"glyphicon glyphicon-apple"]),
            'url'   => ['@default/search','target'=>'product'],
        ],
];
elseif(Branch::PKEY_HE_TORANOKO == $branch->branch_id)
    $searchItems = [
        [
            'label' => Html::tag('i',' 商品',['class'=>"glyphicon glyphicon-globe"]),
            'url'   => ['@default/search','target'=>'product','category'=>14],
        ],
];
else
    $searchItems = [
        [
            'label' => Html::tag('i',' 相談会',['class'=>"glyphicon glyphicon-paperclip"]),
            'url'   => ['@default/search','target'=>'product','SearchProduct[category_id]'=>8],
        ],
];

$searchItems[] = ['label' => '<i class="glyphicon glyphicon-user"></i> お客様',
                  'url'   => ['@default/search','target'=>'customer'],
];

$branch_id = $branch ? "?branch_id={$branch->branch_id}" : '';
?>

<div class="panel panel-default">

    <div class="panel-heading">
        <strong><?= $branch ? $branch->name : '(拠点不明)' ?></strong>
    </div>

    <div class="panel-body">

        <?= \yii\bootstrap\Nav::widget([
            'items' => [
                ['label' => '<i class="glyphicon glyphicon-phone-alt"></i> 受注','url' => ['@default/create'],
                 'visible' => $branch && $branch->isWareHouse(),
                ],
                ['label' => '<i class="glyphicon glyphicon-yen"></i> レジ','url' => ['@default/create'],
                 'visible' => $branch && ! $branch->isWareHouse(),
                ],
                ['label' => '<i class="glyphicon glyphicon-search"></i> 検索',
                 'items' => $searchItems,
                ],
                ['label' => '<i class="glyphicon glyphicon-inbox" title="注文を一覧します"></i> 注文', 'url' => ['@default/index'],
                 'visible' => $branch && $branch->isWareHouse(),
                ],
                ['label' => '<i class="glyphicon glyphicon-inbox" title="本日の売上を一覧します"></i> 売上', 'url' => ['@default/index'],
                 'visible' => $branch && ! $branch->isWareHouse(),
                ],
                ['label' => '<i class="glyphicon glyphicon-equalizer"></i> 集計',      'url' => ['@default/stat']],
                ['label' => '<i class="glyphicon glyphicon-transfer"></i> 店間移動',      'url' => ['transfer/index']],
                ['label' => '<i class="glyphicon glyphicon-file"></i> 棚卸', 'url' => ['inventory/index']],
                ['label' => '<i class="glyphicon glyphicon-wrench"></i> 設定', 'url' => ['default/setup'],
                 'visible' => $branch && ! $branch->isWareHouse(),
                ],
                ['label' => '<i class="glyphicon glyphicon-wrench"></i> 在庫', 'url' => ['stock/index'],
                 'visible' => $branch && $branch->isDelivery(),
                ],
                ['label' => '<i class="glyphicon glyphicon-plus"></i> ポイント付与', 'url' => ['/pointing/create' . $branch_id]],
            ],
            'encodeLabels'=>false,
        ])?>

    </div>

</div>
