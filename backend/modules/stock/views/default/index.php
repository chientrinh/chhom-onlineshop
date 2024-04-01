<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/stock/views/default/index.php $
 * $Id: index.php 3196 2017-02-26 05:11:03Z naito $
 *
 * $this
 * $provider
 */

$provider->sort->defaultOrder = ['abbr' => SORT_ASC];
?>

<div class="stock-default-index">
    <h1><small>ただいま、</small>LMポーテンシーの在庫<small>を表示しています。</small></h1>
    <p><?= date("Y-m-d H:i") ?> 現在</p>

<?= yii\grid\GridView::widget([
    'dataProvider' => $provider,
    'columns' => [
        [
            'attribute' => 'abbr',
            'format' => 'html',
            'value' => function($data){
                return yii\helpers\Html::a($data->abbr,['/remedy/view', 'id'=> $data->remedy_id]);
            }
        ],
        [
            'label' => 'LM01',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 25])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM02',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 26])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM03',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 27])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM04',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 28])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM05',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 29])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM06',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 30])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM07',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 31])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM08',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 32])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM09',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 33])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM10',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 34])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM11',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 35])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM12',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 36])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM13',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 37])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM14',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 38])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM15',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 39])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM16',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 40])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM17',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 41])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM18',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 42])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM19',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 43])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM20',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 44])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM21',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 45])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM22',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 46])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM23',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 47])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM24',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 48])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM25',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 49])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM26',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 50])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM27',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 51])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM28',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 52])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM29',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 53])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        [
            'label' => 'LM30',
            'value' => function($data){
                $found = \common\models\RemedyStock::find()
                       ->Where(['remedy_id' => $data->remedy_id,
                                'potency_id'=> 54])
                    ->all();
                return empty($found) ? '' : 'Y';
        },
        ],
        
    ],
]);
 ?>
<?php return; ?>

 yii\grid\GridView::widget([
    'dataProvider' => $provider,
    //    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'remedy.abbr',
        'potency.name',
        'vial.name',

    ],
]);

</div>
