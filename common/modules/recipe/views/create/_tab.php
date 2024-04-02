<?php
use \yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/create/_tab.php $
 * $Id: _tab.php 3916 2018-06-01 07:13:51Z mori $
 *
 * $carts array of Cart
 */

$target = Yii::$app->request->get('target');
$action = $this->context->action->id;
?>
<?php \yii\bootstrap\NavBar::begin([
    'id'         => 'recipe-create-nav',
    'brandLabel' => '適用書',
    'brandUrl'   => ['index'],
                'options'    => [
                    'class' => 'navbar-inverse navbar-fixed',
                ],
]) ?>
<?= \yii\bootstrap\Nav::widget([
    'encodeLabels' => false,
    'options' => [
        'class' => 'nav navbar-nav navbar-right',
        'title'=>'追加するレメディーの種類を選んでください'
    ],
    'items' => [
        ['label'=>'※適用書の作成方法はこちら　　　','url'=>['/site/howtomk_tekiyosyo'], 'options' => ['title' => '適用書の作成方法はこちらで案内しています']],
        ['label'=>'オリジナル作成','url'=>['compose'],              'active'=> ('compose' == $action)],
        ['label'=>'単品レメディー','url'=>['search','target'=>'remedy'], 'active'=> ('remedy' == $target)],
        ['label'=>'単品MT','url'=>['search','target'=>'tincture'],       'active'=> ('tincture' == $target)],
        ['label'=>'FE2','url'=>['search','target'=>'flower2'],       'active'=> ('flower2' == $target)],
        ['label'=>'FE','url'=>['search','target'=>'flower'],       'active'=> ('flower' == $target)],
        ['label' => 'ジェモ','url' => ['search', 'target' => 'jm'], 'active' => ('jm' == $target)],
        ['label'=>'キット','url'=>['search','target'=>'product'],'active'=> ('product' == $target)],
        ['label'=>'特別レメディー','url'=>['machine'],                   'active'=> ('machine' == $action)],
        ['label'=>'一般処方不可','url'=>['search', 'target'=>'nonpublic'],  'active'=> ('nonpublic' == $target)],
        ['label'=>'履歴に戻る', 'url'=>['/recipe']],
        ['label'=>'　　　','options' => ['title' => ''],  'url' => 'javascript:void(0);'] // ヘッダーのはみ出し対策（一番最期にすること）
    ],
]) ?>
<?php \yii\bootstrap\NavBar::end() ?>
<?php
    if (isset($model) && $model->client) {
        echo \yii\grid\GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $model->getClient()]),
            'layout'  => '{items}',
            'columns'   => [
                    [
                        'attribute' => 'name',
                        'format'    => 'html',
                        'value'     => function($data){ return  Html::tag('span', "{$data->name}（{$data->kana}）", ['style'=>'font-size:30px;']); },
                    ],
                    [
                        'attribute' => 'sex',
                        'format'    => 'html',
                        'value'     => function($data){ return $data->sex->name; },
                    ],
                    [
                        'attribute' => 'age',
                        'format'    => 'html',
                        'value'     => function($data){ return $data->age; },
                    ],
                    [
                        'attribute' => 'birth',
                        'format'    => 'html',
                        'value'     => function($data){ return preg_match('/0000/', $data->birth) ? null : Yii::$app->formatter->asDate($data->birth, 'full'); },
                    ],
                ]
        ]);
    }
 ?>
