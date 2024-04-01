<?php 
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/sodan/views/karute/print.php $
 * $Id: print.php 1637 2015-10-11 11:12:30Z mori $
 */

use \yii\helpers\Html;

$this->title = sprintf('総カルテ | ID:%d | カルテ | 健康相談', $model->karuteid);

$customer = $model->customer;
?>

<div class="karute-print">

    <?= \yii\widgets\DetailView::widget([
        'model'      => $model,
        'options'    => ['class'=>'table'],
        'attributes' => [
            [
                'label'     => '氏名',
                'attribute' => 'customerid',
                'format'    => 'raw',
                'value'     => ! $model->customer ? null : $model->customer->name,
            ],
            [
                'label'     => '住所',
                'visible'   => $customer instanceof \common\models\Customer,
                'value'     => null, // TBD
            ],
            'karute_syuso:ntext',
            'karute_fax_data:ntext',
        ],
    ]) ?>

<?= \yii\widgets\ListView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getItems()->orderBy('syoho_date ASC'),
        'pagination' => false,
    ]),
    'layout' => '{items}',
    'itemView' => 'item',
]) ?>

    <?= Html::a('戻る',['view','id'=>$model->karuteid],['class'=>'btn btn-default']) ?>
</div>
