<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/remedy/_set.php $
 * $Id: _set.php 2908 2016-10-02 01:52:37Z mori $
 *
 */
use \yii\helpers\Html;
$sub_id = array_merge(range(32,56), [123]);

$queries = [];
foreach($sub_id as $id)
{
    $queries[] = \common\models\ProductSubcategory::find()
               ->with('product')
               ->with('product.stock')
               ->with('product.stock.vial')
               ->joinWith([
                   'product.remedy' => function (\yii\db\ActiveQuery $query) {
                       $query->orderBy('abbr');
                   }])
               ->where(['subcategory_id' => $id]);
}
?>

<?php foreach($queries as $i => $query): ?>
<?php if(0 == ($i % 4)): ?>
    <div class="col-md-12"></div>
<?php endif ?>

    <div class="col-md-3 col-sm-4 col-xs-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= \yii\helpers\ArrayHelper::getValue($query->one(),'subcategory.name') ?>
            </div>
            <div class="panel-body">
                <?= \yii\widgets\ListView::widget([
                    'dataProvider' => new \yii\data\ActiveDataProvider([
                        'query' => $query,
                        'pagination' => false,
                    ]),
                    'layout'   => '{items}',
                    'itemView' => function ($model, $key, $index, $widget) {
                        if(! $model->product)
                            return null;
                        if(!$model->product->remedy)
                            return Html::a($model->product->name,[
                                '/cart/default/add','pid'=>$model->product->product_id
                            ],[
                                'title' => $model->product->name .' ￥'. number_format($model->product->price)
                            ]);

                        $p = $model->product;
                        $r = $model->product->remedy;
                        $s = $model->product->stock;

                        $title = sprintf('%s %s (%s) ￥%s',
                                         $r->abbr,
                                         $s && $s->potency ? $s->potency->name : null,
                                         $s && $s->vial ? $s->vial->nickname : null,
                                         number_format($p->price));

                        return Html::a($r->abbr . ($s && !in_array($s->vial_id,[2,3]) ? Html::tag('strong',' '.$p->stock->vial->nickname) : ""),
                                       ['/cart/remedy/add','rid'=>$p->remedy_id,'pid'=>$p->potency_id,'vid'=>$s ? $s->vial_id : null],
                                       ['title'=>$title]);
                    },
                ]) ?>
           </div>
        </div>
    </div>
<?php endforeach ?>

