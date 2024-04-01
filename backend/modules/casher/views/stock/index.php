<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/stock/views/default/index.php $
 * $Id: $
 *
 * $this
 * $provider
 */

use yii\helpers\Html;
use common\models\Product;

$this->params['breadcrumbs'][] = ['label' => '在庫', 'url'=> ['index'] ];

?>

<div class="body-content">

    <div class="list-group col-md-2">
        <?= $this->render('/default/_menu') ?>
    </div>

    <div class="col-md-10">
        <div class="stock-default-index">
            <p><?= date("Y-m-d H:i") ?> 現在</p>
            <?= Html::a(Html::submitButton('追加', [
                'class' => 'pull-right btn btn-success',
                'title' => '商品を新規登録する',
            ]),['create']) ?></a>

<?php
            echo yii\grid\GridView::widget([
                    'dataProvider' => $provider,
                    'id'           => 'inventory-items-grid',
                    'tableOptions' => ['class'=>'table table-condensed table-striped'],
                    'layout'       => '<div>{summary}</div>{pager}{items}{pager}',
                    'columns'      => [
                        [
                            'label'     => 'バーコード',
                            'value'     => function($data) {
                                return $data->ean13;
                            },
                            'headerOptions' => ['class'=>'col-md-1']
                        ],
                        [
                            'label'     => '商品名',
                            'format'    => 'html',
                            'value'     => function($data){
                                               return $data->products[0]->name;
                                           },
                            'headerOptions' => ['class'=>'col-md-4']                                           
                        ],
                        [
                            'label'     => '在庫数（現在の在庫数）',
                            'value'     => function($data){
                                               return empty($data->actual_qty) ? '0' : $data->actual_qty;
                            },
                            'contentOptions' => ['class'=>'text-right'],
                            'headerOptions' => ['class'=>'col-md-1']    
                        ],
                        [
                            'label'     => '　在庫数（総数）　',
                            'format'    => 'raw',
                            'value'     => function($data){ return $this->render('_qty',['model'=>$data]); },
                            'contentOptions' => ['class'=>'text-right'],
                            'headerOptions'  => ['class'=>'col-md-1'],
                        ],
                        [
                            'label'     => '閾値',
                            'value'     => function($data){
                                               return empty($data->threshold) ? '0' : $data->threshold;
                            },
                            'contentOptions' => ['class'=>'text-right'],
                            'headerOptions'  => ['class'=>'col-md-1'],
                        ],
                        [
                            'label'     => '最終更新日時',
                            'format'    => 'raw',
                            'value'     => function($data){ return $data->update_date; },
                            'contentOptions' => ['class'=>'text-right'],
                            'headerOptions'  => ['class'=>'text-center col-md-2'],
                        ],
                        [
                            'attribute' => 'staffs.name01',
                            'format'    => 'raw',
                            'value'     => function($data){ return $data->updator->name01. " ". $data->updator->name02; },
                            'contentOptions' => ['class'=>'text-right'],
                            'headerOptions'  => ['class'=>'text-center col-md-2'],
                        ],
                        [
                            'label'     => '',
                            'format'    => 'raw',
                            'value'     => function($data) {
                                return Html::a('削除', ['delete', 'id' => $data->stock_id], ['class' => 'btn btn-xs btn-danger',
                                        'data' => [
                                            'confirm' => '本当に削除していいですか？',
                                        ]]);
                            },
                            'contentOptions' => ['class'=>'text-center'],
                        ],
                     ],
            ]);
?>
        </div>
    </div>
</div>
