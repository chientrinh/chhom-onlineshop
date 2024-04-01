<?php 
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/karute/print.php $
 * $Id: print.php 2664 2016-07-06 08:36:09Z mori $
 */

use \yii\helpers\Html;

$this->title = sprintf('総カルテ | ID:%d | カルテ | %s', $model->karuteid, Yii::$app->name);
$this->params['breadcrumbs'][] = ['label' => $model->karuteid, 'url' =>['view','id'=>$model->karuteid]];
$this->params['breadcrumbs'][] = ['label' => '総カルテ'];

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
            'query'      => $model->getItems()->orderBy('syoho_date ASC'),
            'pagination' => false,
        ]),
        'layout'   => '{items}',
        'itemView' => 'item',
    ]) ?>

    <?= Html::a('戻る',['view','id'=>$model->karuteid],['class'=>'btn btn-default']) ?>

</div>
