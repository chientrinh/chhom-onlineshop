<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/remedy/_mtq.php $
 * $Id: _mtq.php 3284 2017-05-09 07:11:05Z kawai $
 *
 * $searchModel  Model
 * $dataProvider ActiveDataProvider of \backend\models\Product
 */
use yii\helpers\Html;
use common\models\ProductMaster;
use common\models\ProductSubcategory;
$sub_id = 10;  // マザーチンクチャー

$queries = [
    '小瓶' => ProductSubcategory::find()
               ->with('product','product.stock')
               ->joinWith('product')
               ->joinWith([
                   'product.remedy' => function (\yii\db\ActiveQuery $query) {
                       $query->orderBy('abbr');
                   }])
               ->andWhere(['subcategory_id' => $sub_id])
               ->andWhere(['mvtb_product_master.vial_id' => 7 /* 20ml */]),

    '大瓶' => ProductSubcategory::find()
               ->with('product','product.stock')
               ->joinWith('product')
               ->joinWith([
                   'product.remedy' => function (\yii\db\ActiveQuery $query) {
                       $query->orderBy('abbr');
                   }])
               ->andWhere(['subcategory_id' => $sub_id])
               ->andWhere(['mvtb_product_master.vial_id' => 8 /* 150ml */]),

    'その他' => ProductSubcategory::find()
               ->with('product','product.stock')
               ->joinWith('product')
               ->joinWith([
                   'product.remedy' => function (\yii\db\ActiveQuery $query) {
                       $query->orderBy('abbr');
                   }])
               ->joinWith([
                   'subcategory' => function (\yii\db\ActiveQuery $query) use ($sub_id) {
                       $query->andWhere(['or',['mtb_subcategory.subcategory_id' => $sub_id],['parent_id' => $sub_id]]);
                   }])
               ->andWhere(['not',['mvtb_product_master.vial_id' => [7 /* 20ml */, 8 /* 150ml */]]]),

    'サポート Pet' => ProductSubcategory::find()
               ->with('product','product.stock')
               ->joinWith('product')
               ->joinWith([
                   'product.remedy' => function (\yii\db\ActiveQuery $query) {
                       $query->orderBy('abbr');
                   }])
               ->andWhere(['subcategory_id' => 126]),

    'サポート Can' => ProductSubcategory::find()
               ->with('product','product.stock')
               ->joinWith('product')
               ->joinWith([
                   'product.remedy' => function (\yii\db\ActiveQuery $query) {
                       $query->orderBy('abbr');
                   }])
               ->andWhere(['subcategory_id' => 125]),

    'サポート Thuj' => ProductSubcategory::find()
               ->with('product','product.stock')
               ->joinWith('product')
               ->joinWith([
                   'product.remedy' => function (\yii\db\ActiveQuery $query) {
                       $query->orderBy('abbr');
                   }])
               ->andWhere(['subcategory_id' => 127]),

    'サポートチンクチャー' => ProductSubcategory::find()
               ->with('product','product.stock')
               ->joinWith('product')
               ->joinWith([
                   'product.remedy' => function (\yii\db\ActiveQuery $query) {
                       $query->orderBy('abbr');
                   }])
               ->andWhere(['subcategory_id' => 124]),
];
?>

<?php foreach($queries as $label => $query): ?>

<?php
$subcategory = \common\models\Subcategory::findOne($sub_id);

$models = [];
foreach($query->all() as $model)
{
    if(! $p = $model->product)
        return;
    if(! $s = $p->stock)
        return;
    if(! $r = $s->remedy)
        return;

    $title = sprintf('%s:%s %s ￥%s', $r->abbr, $r->ja, $s->vial->name, number_format($s->price));

    $name = ProductMaster::find()->where([
        'remedy_id' => $s->remedy_id,
        'potency_id'=> $s->potency_id,
        'vial_id'   => $s->vial_id,
    ])
                                 ->select('name')
                                 ->scalar();

    $name = preg_replace('/Φ/u','',$name);
    $name = preg_replace('/大|小/u','',$name);
    $name = preg_replace('/特/u','特大',$name);
    $name = trim($name);

    if(! $name)
        $name = $r->abbr;

    $models[$name] = Html::a($name,['/cart/remedy/add','rid'=>$s->remedy_id,'pid'=>$s->potency_id,'vid'=>$s->vial_id],['class'=>'stock','title'=>$title]);
}
ksort($models);
?>
    <div class="col-md-4">
        <div class="panel panel-success">
            <div class="panel-heading">
                <?= $label ?>
            </div>
            <div class="panel-body">
                <?= \yii\widgets\ListView::widget([
                    'dataProvider' => new \yii\data\ArrayDataProvider([
                        'allModels' => $models,
                        'pagination' => false,
                    ]),
                    'layout'   => '{items}',
                    'itemView' => function ($data, $key, $index, $widget) { return $data; }
                ]) ?>
           </div>
        </div>
    </div>
<?php endforeach ?>

