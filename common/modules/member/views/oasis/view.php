<?php
/**
 * $URL$
 * $Id$
 *
 * $shipped      bool
 * $model        \common\modules\member\models\Oasis
 * $dataProvider DataProvider of Customer
 */

use \yii\helpers\Html;

$jscode = "
$('.grid-view').on('click','.btn-default',function(e){
   $.ajax({
       url: $(this).attr('href'),
       data: {},
       success: function(data) {
           $(e.target).attr('class','btn btn-xs btn-primary glyphicon glyphicon-ok');
           $(e.target).attr('href','#');
       }
   });
   return false;
});
";
$this->registerJs($jscode);

$title = sprintf('%s (%s)', $model->name, ($shipped ? '配布済み' : ((null===$shipped) ? '対象者 全員' : '未発送')));
$this->title = $title . ' | ' . $this->title;

array_pop($this->params['breadcrumbs']);
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['view','pid'=>$model->product_id,'shipped'=>$shipped]];

?>

<h2>
    <?= $title ?>
</h2>

<?php if($shipped || (null === $shipped)): ?>
<?= Html::a('CSV', ['csv', 'pid' => $model->product_id], ['class'=>'btn btn-primary','title'=>'対象者全員の住所をCSVに出力します']) ?>
<?php endif ?>

<?php if($shipped): ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'customer_id',
            'format'    => 'html',
            'value'     => function($data){
                $customer = \common\models\Customer::findOne($data['customer_id']);
                if($customer)
                return Html::a($customer->name,['toranoko/view','id'=>$customer->customer_id]);
            },
        ],
        [
            'label'     => '起票日',
            'attribute' => 'create_date',
            'format'    => ['date', 'php:Y-m-d H:i'],
        ],
        [
            'label'     => '起票＠社内',
            'attribute' => 'purchase_id',
            'format'    => 'html',
            'value'     => function($data){
                $purchase = \common\models\Purchase::findOne($data['purchase_id']);
                if($purchase)
                    return Html::a($purchase->purchase_id,['/purchase/view','id'=>$purchase->purchase_id]);
            },
        ],
        [
            'label'     => '起票＠代理店',
            'attribute' => 'pointing_id',
            'format'    => 'html',
            'value'     => function($data){
                $pointing = \common\models\Pointing::findOne($data['pointing_id']);
                if($pointing)
                    return Html::a($pointing->pointing_id,['/pointing/view','id'=>$pointing->pointing_id]);
            },
        ],
    ],
]) ?>
<?php else: ?>

    <?php if($model->getCustomers(false)->count()): ?>
        <?= Html::a('CSV + 全員を発送済みにする',['csv','pid'=>$model->product_id,'shipped'=>false],['class'=>'btn btn-success']) ?>
    <?php else: ?>
        <span class="text-success">全員発送済みです</span>
    <?php endif ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'emptyText' => '対象者はいません',
    'columns' => [
        [
            'attribute' => 'customer_id',
            'format'    => 'html',
            'value'     => function($data){ return Html::a($data->name,['toranoko/view','id'=>$data->customer_id]); },
        ],
        'fulladdress',
        [
            'label' => '発送日',
            'format'    => 'html',
            'value' => function($data)use($model)
            {
                $pid   = $model->product_id;
                $query = $data->getPurchases()->active()->joinWith(['items'=>function($query)use($pid){$query->andWhere(['product_id' => $pid]);}],false,'INNER JOIN');
                if($row = $query->one())
                    return $row->create_date;

                $query = $data->getPointings()->active()->joinWith(['items'=>function($query)use($pid){$query->andWhere(['product_id' => $pid]);}],false,'INNER JOIN');
                if($row = $query->one())
                    return $row->create_date;

                return Html::a('発送済みにする',['oasis/mark-as-shipped','pid'=>$model->product_id,'cid'=>$data['customer_id']],['class'=>'btn-default']); },
        ],
    ],
]) ?>

<?php endif ?>
