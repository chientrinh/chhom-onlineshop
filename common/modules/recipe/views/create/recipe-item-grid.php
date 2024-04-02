<?php
/**
 * $URL: https://localhost:44344/svn/MALL/frontend/views/layouts/main.php $
 * $Id: main.php 1693 2015-10-20 13:04:34Z mori $
 *
 * @var $this \yii\web\View
 * @var $model \common\models\RecipeForm
 */

use yii\helpers\Html;
use yii\helpers\Url;
?>

<h2>
    <span>
        <?= Html::a('適用書 <small>追加されたアイテム</small>',['index'],['class'=>'text-muted']) ?>
    </span>
</h2>

<?= \yii\grid\GridView::widget([
    'id' => 'recipe-item-grid',
    'dataProvider' => new \yii\data\ArrayDataProvider([
        'allModels' => $model->items,
    ]),
    'tableOptions' => ['class'=>'table table-condensed table-striped alert alert-success'],
    'layout'  => '{items}{pager}{summary}',
    'summary' => '<p class="text-right">計 <strong>{totalCount}</strong> 品目</p>',
    'showOnEmpty' => false,
    'emptyText'   => 'まだありません',
    'columns' => [
        [
            'attribute' => 'name',
            'format' => 'html',
            'value' => function($data, $key, $index, $column)
            {
                if(property_exists($data, 'remedy_id') && $data->remedy)
                    $label = $data->remedy->abbr;
                else
                    $label = nl2br($data->name);

                return Html::a('✖', ['del','target'=>'item','seq'=>$index],['class'=>'btn btn-xs text-muted pull-right','title'=>'削除します'])
                     . $label;
            }
        ],
    ],
])?>

<p class="pull-right">
    <?= Html::a('適用書を表示',['index'],['class'=>'btn btn-warning']) ?>
</p>
