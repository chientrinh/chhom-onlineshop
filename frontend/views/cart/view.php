<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/cart/view.php $
 * $Id: view.php 1651 2015-10-12 16:03:16Z mori $
 */

$cart = [
    'subtotal'      => 18618,
    'tax'           => '00000',
    'postage'       => '00000',
    'fee'           => '00000',
    'discount'      => '00000',
    'payment_total' => '00000',
];
$customer = $model['customer'];
?>  

<div class="col-lg-2">
<p>
    <?= \yii\widgets\DetailView::widget([
        'model' => $cart,
        'attributes' => array_keys($cart),
        'attributes' => [
            ['attribute' => 'subtotal',
             'label'     => "小計",
            ],
            ['attribute' => 'postage',
             'label'     => "送料",
            ],
            ['attribute' => 'fee',
             'label'     => "手数料",
            ],
            ['attribute' => 'payment_total',
             'label'     => "お支払合計",
             'value'     => sprintf('<span style="font-weight:bold;font-size:120%%">%s</span>',$cart['payment_total']),
             'format' => 'raw',
            ],
        ],
    ]) ?>
<?php if(Yii::$app->user->isGuest): ?>
<a class="btn btn-success" href="<?= \Yii::$app->getUrlManager()->createUrl(['view']); ?>">会員登録</a>
<a class="btn btn-success" href="<?= \Yii::$app->getUrlManager()->createUrl(['view']); ?>">ログイン</a>
または

<a class="btn btn-default" href="<?= \Yii::$app->getUrlManager()->createUrl(['view']); ?>">登録しないで購入する</a>
<?php else: ?>
<a class="btn btn-danger" href="<?= \Yii::$app->getUrlManager()->createUrl(['view']); ?>">注文を確定する</a>
<?php endif ?>
</p>

<hr>
<?php if(isset($customer)): ?>
<h5>お届け先 <a class="btn btn-default" href="<?= \Yii::$app->getUrlManager()->createUrl(['view']); ?>">
変更
</a>
</h5>
<p><?= $customer->zip ?></p>
<p><?= $customer->addr ?></p>
<p><?= $customer->name ?></p>
<?php endif ?>

<hr>
<h5>お届け日時 <a class="btn btn-default" href="<?= \Yii::$app->getUrlManager()->createUrl(['view']); ?>">変更</a>
</h5>
<p>配達日: 指定なし</p>
<p>時間: 指定なし</p>

<hr>
<h5>お支払方法 <a class="btn btn-default" href="<?= \Yii::$app->getUrlManager()->createUrl(['view']); ?>">変更</a>
</h5>
<p>代金引換</p>

</div>
<div class="col-lg-7">
<h1>ご注文内容の確認</h1>

<p>
    <?= str_repeat("あいうえお ", 10) ?>
</p>
<?php
    $query = new \yii\db\Query;

    $models = [
        ['branch' => \common\models\Branch::findOne(1),
         'items' => [
             ['code' => '', 'name' => '野菜セット', 'price' => '1857'],
             ['code' => '', 'name' => '黒大豆みそ', 'price' => '1143'],
         ],
        ],
        ['branch' => \common\models\Branch::findOne(4),
         'items' => $query->from('dtb_product')->where(['IN','product_id',[8,7,6]])->all(),
        ],
        ['branch' => \common\models\Branch::findOne(3),
         'items' => $query->from('dtb_product')->where(['IN','product_id',[183,185,199]])->all(),
        ]
    ];

foreach($models as $model)
{
    $model    = (object) $model;

if(! $model->items)
{continue;}

    $subtotal = 0;
    foreach($model->items as $item)
    {
        $subtotal += $item['price'];
    }
    $tax = floor($subtotal * 0.08);

    echo '<h4>',$model->branch->name, '</h4>';
    echo \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $model->items,
            'sort' => [
                'attributes' => ['id', 'username', 'email'],
            ],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]),
        'summary' => "合計 {totalCount} 点",
        'columns' => [
            'code',
            [
                'attribute' => 'name',
            ],
            [
                'attribute' => 'price',
            ],
            [
                'label' => 'qty',
                'value' => function(){return 1;},
                'footer' => "小計<br>消費税",
            ],
            [
                'attribute' => 'price',
                'label' => 'sum',
                'footer' => sprintf("%s<br>%s",$subtotal,$tax),
            ],
        ],
        'showFooter'       => true,
        'footerRowOptions' => ['style'=>'text-align:right;font-weight:bold'],
    ]);
    if(1 == $model->branch->branch_id)
    {
echo "上記に送料 0000 円が加算されます。
以上の商品は六本松発送所からYYYY-MM-DDに発送されます。";
echo '<hr><p>&nbsp;</p>';
    }
    else
    {
        echo "上記に送料 0000 円が加算されます。
以上の商品は熱海発送所からYYYY-MM-DDに発送されます。";
    }
}
?>

</div>
