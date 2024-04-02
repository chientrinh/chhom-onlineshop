<?php

use \yii\helpers\Html;
use \common\models\Branch;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/admin/index.php $
 * $Id: index.php 4145 2019-03-29 06:20:34Z kawai $
 *
 * $this \yii\web\View
 * $dataProvider
 * $searchModel
 */

$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels); $labels[] = Yii::$app->name;

$this->title = implode(' | ', $labels);

$csscode = '
.panel-body {
  min-height: 120px;
}
';
$this->registerCss($csscode);
$backend_flg = (Yii::$app->id === 'app-backend') ? true : false;

$param_branch_id = ($branch_id) ? "?branch_id={$branch_id}" : '';
if($backend_flg)
{
    // ログインした従業員の役割からセンターの拠点IDを取り出し、複数あれば最初のIDをセット
    $roles = Yii::$app->user->identity->roles;
    $center_id = 0;
    $centers = \yii\helpers\ArrayHelper::getColumn(Branch::find()->center(true)->all(), 'branch_id');

    foreach($roles as $role) {
        if(in_array($role->branch_id, $centers)) {
            $center_id = $role->branch_id;
            break;
        }
    }

    $menuItems = [
        '1.予約' => [
            ['label' => '予約', 'url' => ['client/index']],
            ['label' => '新規クライアント作成', 'url' => ['client/create']],
            ['label' => 'キャンセル待ち登録', 'url' => ['wait-list/index']],
            ['label' => '相談枠 作成', 'url' => ['interview/create']],
            ['label' => '相談枠 作成（一括）', 'url' => ['interview/create-regular' . $param_branch_id]]
        ],
        '2.相談会' => [
            ['label' => '本日の相談会', 'url' => ['interview/index', 'time' => 0]],
            ['label' => '今後の相談会', 'url' => ['interview/index', 'time' => 1]],
            ['label' => '過去の相談会', 'url' => ['interview/index', 'time' => -1]],
            ['label' => '休業日設定', 'url' => ['calendar/holiday-setting']]
        ],
    ];

    $menuItems['3.お会計'] = [
        ['label' => 'お会計', 'url' => ['interview/index', 'time' => 0, 'bill' => 'on']],
        ['label' => 'ポイント付与', 'url' => ['/pointing/create']],
        ['label' => '売上一覧', 'url' => ['/casher/default/setup', 'id' => $center_id, 'create_date' => date('Y-m-d')],'template'=> '<a href="{url}" target="_blank">{label}</a>',],
        ['label' => '月次集計（健康相談）', 'url' => ['stat/index']]
    ];
} else {
    $menuItems = [
        'クライアント' => [
            ['label' => 'クライアント', 'url' => ['client/index']]
        ],
        '相談会' => [
            ['label' => '本日の相談会', 'url' => ['interview/index', 'time' => 0]],
            ['label' => '今後の相談会', 'url' => ['interview/index', 'time' => 1]],
            ['label' => '過去の相談会', 'url' => ['interview/index', 'time' => -1]]
        ]
    ];
}

$menuItems['カレンダー'] = [
//    ['label' => 'カレンダー',     'url' => ['calendar/index' . $param_branch_id]],
    ['label' => 'カレンダー予約', 'url' => ['calendar/reserve' . $param_branch_id]],
];

if($backend_flg)
{
    $menuItems['マスタ管理'] = [
        ['label' => '顧客', 'url' => ['/customer/index']],
        ['label' => '本部クライアント', 'url' => ['client/index']],
        ['label' => '本部ホメオパス', 'url' => ['homoeopath/index']],
        ['label' => '予約票テンプレート', 'url' => ['book-template/index']],
        ['label' => '健康相談クーポン', 'url' => ['coupon/index']],
        ['label' => '健康相談チケット', 'url' => ['ticket/index']]
    ];
    $menuItems['その他'] = [
        ['label' => '適用書','url' => ['/recipe']],
        ['label' => 'カルテ(webdb)','url' => ['/karute']],
   ];
    $menuItems['集計'] = [
        ['label' => '月次集計','url' => [
            'stat/view',
            'id'    => Yii::$app->user->id,
            'year'  => date('Y'),
            'month' => date('m'),
        ]],
    ];
}
?>

<div class="sodan-admin-index">
  <h3>相談会業務メニュー</h3>
  <?php foreach($menuItems as $header => $items):
      $col_class = (!$backend_flg) ? 'col-md-5' : 'col-md-4'; ?>
      <div class="list-group <?= $col_class ?> col-xs-6">
          <div class="panel panel-default" style="height:185px;">
              <div class="panel-heading"><?= $header ?></div>
              <div class="panel-body">
                  <?= \yii\widgets\Menu::widget([
                      'items'           => $items,
                      'activateItems'   => true,
                      'activateParents' => true,
                  ]);
                  ?>
              </div>
          </div>
      </div>
    <?php endforeach ?>

</div>
