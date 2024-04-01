<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/remedy/_kit.php $
 * $Id: _kit.php 2908 2016-10-02 01:52:37Z mori $
 *
 */
use \yii\helpers\Html;
$sub_id = [
    ['id'=> 24, 'vial_id'=> 2 ],// 基本キット
    ['id'=> 25, 'vial_id'=> 2 ],// キッズキット
    ['id'=> 24, 'vial_id'=> 4 ],// 基本キット
    ['id'=> 25, 'vial_id'=> 4 ],// キッズキット
    ['id'=> 26, 'vial_id'=> 2 ],// バースキット
    ['id'=> 27, 'vial_id'=> 2 ],// YOBOキット
    ['id'=> 28, 'vial_id'=> 2 ],// 36バイタル・エレメントキット
    ['id'=> 29, 'vial_id'=> 1 ],// マイクロキット
];

$queries = [];
foreach($sub_id as $item)
{
    $id   = $item['id'];
    $vial = $item['vial_id'];

    $q = \common\models\ProductSubcategory::find()
       ->joinWith('product')
       ->with('product')
       ->with('product.stock')
       ->with('product.stock.vial')
       ->where(['subcategory_id' => $id])
       ->andWhere(['or',
                   ['mvtb_product_master.vial_id' => $vial ],
                   ['mvtb_product_master.vial_id' => null  /* 36キット本体 */]])
       ->orderBy(['mvtb_product_master.product_id' => SORT_DESC, 'mvtb_product_master.kana' => SORT_ASC]);

    $queries[] = $q;
}

?>

<?php foreach($queries as $sub_id => $query): ?>

<?php
$vial_id = $query->max('mvtb_product_master.vial_id');

if($vial_id == 4)
    $panel = 'panel-info';

elseif($vial_id == 1)
    $panel = 'panel-success';

else
    $panel = 'panel-primary';
?>

    <?php $subcategory = \common\models\Subcategory::findOne($sub_id); ?>
    <div class="col-md-3 col-sm-4 col-xs-6">
        <div class="panel <?= $panel ?>">
            <div class="panel-heading">
                <?= \yii\helpers\ArrayHelper::getValue($query->one(),'subcategory.name') ?>
                <?php if($vial_id == 4): ?>
                    (大瓶)
                <?php endif ?>
            </div>
            <div class="panel-body">
                <?= \yii\widgets\ListView::widget([
                    'dataProvider' => new \yii\data\ActiveDataProvider([
                        'query' => $query,
                        'pagination' => false,
                    ]),
                    'layout'   => '{items}',
                    'itemView' => function ($model, $key, $index, $widget)
                    {
                        if(! $p = $model->product)
                            return \yii\helpers\Json::encode($model);

                        if(! $r = $p->remedy)
                            return Html::a($p->name,['/cart/default/add','pid'=>$p->product_id],['title'=>$p->name .' ￥'. number_format($p->price) ]);

                        return Html::a($r->abbr,['/cart/remedy/add','rid'=>$r->remedy_id,'pid'=>$p->potency_id,'vid'=>$p->vial_id],['title'=> sprintf('%s %s (%s) ￥%s',$r->abbr,$p->stock->potency->name, $p->stock->vial->nickname, number_format($p->price)) ]);

                    },
                ]) ?>
           </div>
        </div>
    </div>
<?php endforeach ?>


