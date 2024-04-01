<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/__tabs.php $
 * $Id: __tabs.php 4161 2019-06-07 05:51:23Z mori $
 *
 * $company integer: company_id
 */

use \yii\helpers\Html;

$items  = [];
$branch = $this->context->module->branch;
if ($branch->isHJForCasher()) // ホメオパシージャパンShop東京本店(ID:13)の場合
{
    $items = [
        [
            'label'  => '全レメディー',
            'url'    => ['search','target'=>'all_remedy'],
            'active' => ('all_remedy' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => 'キットセット',
            'url'    => ['search','target'=>'popular'],
            'active' => ('popular' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => 'キット単品',
            'url'    => ['search','target'=>'kit'],
            'active' => ('kit' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => 'セット単品',
            'url'    => ['search','target'=>'modular'],
            'active' => ('modular' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => 'レメディー単品',
            'url'    => ['search','target'=>'remedy'],
            'active' => ('remedy' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','MT',['title'=>'マザーチンクチャー']),
            'url'    => ['search','target'=>'tincture'],
            'active' => ('tincture' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','FE',['title'=>'フラワーエッセンス']),
            'url'    => ['search','target'=>'flower'],
            'active' => ('flower' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','FE2',['title'=>'フラワーエッセンス2']),
            'url'    => ['search','target'=>'flower2'],
            'active' => ('flower2' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => '適用書',
            'url'    => ['search','target'=>'recipe'],
            'active' => ('recipe' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => 'オリジナル',
            'url'    => ['compose'],
            'active' => ('compose' == $this->context->action->id),
        ],
        [
            'label'  => '特別レメディー',
            'url'    => ['machine'],
            'active' => ('machine' == $this->context->action->id),
        ],
    ];
}


if($branch->isHEForCasher())
{
    $company_id = \common\models\Company::PKEY_HE;
    $items = [
        [
            'label'  => Html::tag('span','自然化粧品・自然食品',['title'=>'自然化粧品']),
            'url'    => ['search','target'=>'cosme_food'],
            'active' => ('cosme_food' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','野菜',['title'=>'野菜']),
            'url'    => ['search','target'=>'veg', 'company'=>$company_id],
            'active' => ('veg' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','書籍・DVD',['title'=>'書籍・DVD']),
            'url'    => ['search','target'=>'book_dvd'],
            'active' => ('book_dvd' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','レストラン',['title'=>'レストラン']),
            'url'    => ['search','target'=>'restaurant'],
            'active' => ('restaurant' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','MT',['title'=>'マザーチンクチャー']),
            'url'    => ['search','target'=>'tincture', 'company'=>$company_id],
            'active' => ('tincture' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span', '販売店専用', ['title' => '販売店専用']),
            'url'    => ['search', 'target' => 'agent', 'company' => $company_id],
            'active' => ('agent' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','その他',['title'=>'その他']),
            'url'    => ['search','target'=>'other', 'company'=>$company_id],
            'active' => ('other' == Yii::$app->request->get('target')),
        ],
    ];
}

if($branch->isAtamiForCasher() || $branch->branch_id == \common\models\Branch::PKEY_EVENT)
{
    $company_id = \common\models\Company::PKEY_HJ;
    $items = [
        [
            'label'  => 'キットセット',
            'url'    => ['search','target'=>'popular'],
            'active' => ('popular' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => 'キット単品',
            'url'    => ['search','target'=>'kit'],
            'active' => ('kit' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => 'セット単品',
            'url'    => ['search','target'=>'modular'],
            'active' => ('modular' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => 'レメディー単品',
            'url'    => ['search','target'=>'remedy'],
            'active' => ('remedy' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','MT',['title'=>'マザーチンクチャー']),
            'url'    => ['search','target'=>'tincture'],
            'active' => ('tincture' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','FE',['title'=>'フラワーエッセンス']),
            'url'    => ['search','target'=>'flower'],
            'active' => ('flower' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','FE2',['title'=>'フラワーエッセンス2']),
            'url'    => ['search','target'=>'flower2'],
            'active' => ('flower2' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => '適用書',
            'url'    => ['search','target'=>'recipe'],
            'active' => ('recipe' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => 'RXT',
            'url'    => ['search','target'=>'rxt'],
            'active' => ('rxt' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => '全レメディー',
            'url'    => ['search','target'=>'all_remedy'],
            'active' => ('all_remedy' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => 'オリジナル',
            'url'    => ['compose'],
            'active' => ('compose' == $this->context->action->id),
        ],
        [
            'label'  => '特別レメディー',
            'url'    => ['machine'],
            'active' => ('machine' == $this->context->action->id),
        ],
        [
            'label'  => Html::tag('span','自然化粧品・自然食品',['title'=>'自然化粧品']),
            'url'    => ['search','target'=>'cosme_food', 'company'=>$company_id],
            'active' => ('cosme_food' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','書籍・DVD',['title'=>'書籍・DVD']),
            'url'    => ['search','target'=>'book_dvd', 'company'=>$company_id],
            'active' => ('book_dvd' == Yii::$app->request->get('target')),
        ],
    ];
}

if($branch->isRopponmatsuForCasher())
{
    $company_id = \common\models\Company::PKEY_HJ;
    $items = [
        [
            'label'  => '豊受',
            'url'    => ['search','target'=>'products'],
            'active' => ('products' == Yii::$app->request->get('target')),
        ],
    ];
}

if($branch->isTroseForCasher())
{
    $company_id = \common\models\Company::PKEY_TROSE;
    $items = [
        [
            'label'  => 'トミーローズ',
            'url'    => ['search','target'=>'products'],
            'active' => ('products' == Yii::$app->request->get('target')),
        ],
    ];
}

if($branch->branch_id == \common\models\Branch::PKEY_EVENT)
{
    $items  = [
        [
            'label'  => 'キットセット',
            'url'    => ['search','target'=>'popular'],
            'active' => ('popular' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => 'キット単品',
            'url'    => ['search','target'=>'kit'],
            'active' => ('kit' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => 'セット単品',
            'url'    => ['search','target'=>'modular'],
            'active' => ('modular' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => 'レメディー単品',
            'url'    => ['search','target'=>'remedy'],
            'active' => ('remedy' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','MT',['title'=>'マザーチンクチャー']),
            'url'    => ['search','target'=>'tincture'],
            'active' => ('tincture' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','FE',['title'=>'フラワーエッセンス']),
            'url'    => ['search','target'=>'flower'],
            'active' => ('flower' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','FE2',['title'=>'フラワーエッセンス2']),
            'url'    => ['search','target'=>'flower2'],
            'active' => ('flower2' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => 'RXT',
            'url'    => ['search','target'=>'rxt'],
            'active' => ('rxt' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => '全レメディー',
            'url'    => ['search','target'=>'all_remedy'],
            'active' => ('all_remedy' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','自然化粧品・自然食品',['title'=>'自然化粧品']),
            'url'    => ['search','target'=>'cosme_food', 'company'=>$company_id],
            'active' => ('cosme_food' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => Html::tag('span','書籍・DVD',['title'=>'書籍・DVD']),
            'url'    => ['search','target'=>'book_dvd', 'company'=>$company_id],
            'active' => ('book_dvd' == Yii::$app->request->get('target')),
        ],
        [
            'label'  => '豊受',
            'url'    => ['search','target'=>'products'],
            'active' => ('products' == Yii::$app->request->get('target')),
        ]
    ];
}

/*
$items[] = [
    'label'  => Html::tag('span','TY',['title'=>'豊受自然農']),
    'url'    => ['search','target'=>'product','company'=>\common\models\Company::PKEY_TY],
    'active' => (\common\models\Company::PKEY_TY == $company),
];

$items[] = [
    'label'  => 'すべて',
    'url'    => ['search','target'=>'product','company'=>0],
    'active' => (0 == $company) && ('product' == Yii::$app->request->get('target')),
];
*/

$items[] = [
    'label'  => Html::tag('span', '', ['class'=>'glyphicon glyphicon-repeat', 'title'=>"検索条件を無効にして再読込み"]),
    'url'    => ['search',
                 'target'  => Yii::$app->request->get('target'),
                 'company' => Yii::$app->request->get('company'),],
    'headerOptions' => ['class'=>'pull-right'],
    'active' => false,
];

?>

<?= \yii\bootstrap\Tabs::widget([
    'encodeLabels' => false,
    'items' => $items,
])?>
