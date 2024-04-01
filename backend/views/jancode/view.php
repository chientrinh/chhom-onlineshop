<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/jancode/view.php $
 * $Id: view.php 2316 2016-03-27 06:18:47Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Product
 * @var $model ( ProductJan | RemedyStockJan )
 */

use yii\helpers\Html;
use yii\helpers\Url;

if(0 < $model->product_id)
{
    $name = $model->product->name;
    $url  = Url::toRoute(['product/view','id'=>$model->product_id]);
}
elseif(0 < $model->remedy_id)
{
    $name = $model->stock->name;
    $url  = Url::toRoute(['remedy-stock/view',
                     'remedy_id'  => $model->remedy_id,
                     'potency_id' => $model->potency_id,
                     'vial_id'    => $model->vial_id    ]);
}
else
{
    $name = '<span class="not-set">不正なモデルです</span>';
    $url  = null;
}

$this->params['breadcrumbs'][] = ['label' => $name];

?>

<div class="product-create">

    <p class="pull-right">
        <?= Html::a('編集',['update','id'=>$model->product_id ? $model->product_id : $model->sku_id],['class'=>'btn btn-primary']) ?>
    </p>
    <h1><?= Html::encode($name) ?></h1>

    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            'jan',
            [
                'label'  => '商品',
                'format' => 'html',
                'value'  => Html::a($name, $url),
            ],
        ],
    ]) ?>

</div>
