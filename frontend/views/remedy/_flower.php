<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/remedy/_flower.php $
 * $Id: _flower.php 2908 2016-10-02 01:52:37Z mori $
 *
 */
use \yii\helpers\Html;

$matrix = \common\models\Subcategory::find()->andWhere(['parent_id' => 9 /* フラワーエッセンス */ ])->column();
$queries = [];
foreach($matrix as $sub_id)
    $queries[$sub_id] = \common\models\ProductSubcategory::find()
               ->with('product','product.stock')
               ->joinWith([
                   'product.remedy' => function (\yii\db\ActiveQuery $query) {
                       $query->orderBy('abbr');
                   }])
               ->andWhere(['subcategory_id' => $sub_id]);
?>

<?php foreach($queries as $sub_id => $query): ?>

    <?php $subcategory = \common\models\Subcategory::findOne($sub_id); ?>
    <div class="col-md-3">
        <div class="panel panel-<?= (128 /* FE2 */== $sub_id) ? 'danger' : 'warning' ?>">
            <div class="panel-heading">
                <?= $subcategory->name ?>
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
                            return null;

                        $r = $p->remedy;
                        $s = $p->stock;
                        if(! $r || ! $s)
                            return "エラー ean13=".$p->ean13;

                        $title = sprintf('%s:%s ￥%s',$r->abbr,$r->ja, number_format($s->price));
                        return Html::a($r->abbr,['/cart/remedy/add','rid'=>$s->remedy_id,'pid'=>$s->potency_id,'vid'=>$s->vial_id],['class'=>'stock','title'=>$title]);
                    },
                ]) ?>
           </div>
        </div>
    </div>
<?php endforeach ?>

