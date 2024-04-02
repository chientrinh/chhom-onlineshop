<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/client/print.php $
 * $Id: print.php 2664 2016-07-06 08:36:09Z mori $
 */

use \yii\helpers\Html;
use common\models\sodan\Interview;
use common\models\BinaryStorage;

$this->params['breadcrumbs'][] = ['label' => $model->client->name, 'url' => ['view','id' => $model->client_id]];
$this->params['breadcrumbs'][] = ['label' => 'カルテ'];
$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'], 'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;

$data = BinaryStorage::find()->where(['tbl_name' => 'dtb_sodan_client', 'property' => 'photo', 'pkey' => $model->client_id])->orderBy(['create_date' => SORT_DESC])->one();
if ($data) {
    $filename = $data->basename;
    $binary = $data->data;
    $type     = pathinfo($filename, PATHINFO_EXTENSION);
    $img    = 'data:image/' . $type . ';base64,' . base64_encode($binary);
} else {
    $img = '';
}
?>
<style>
    .table {
        table-layout: fixed;
    }
    .table th {
        table-layout: fixed;
        width:10%;
    }
    .table td {
        width:90%;
        word-wrap: break-word;
    }
</style>

<div class="karute-print">

    <div style="width:100%;">
        <div class="pull-left" style="width:60%;float:left;">
            <?= \yii\widgets\DetailView::widget([
                'model'      => $model,
                'options'    => ['class'=>'table'],
                'attributes' => [
                    [
                        'label'     => '氏名',
                        'attribute' => 'name',
                        'format'    => 'raw',
                        'value'     => $model->client->name,
                    ],
                    [
                        'label'     => '',
                        'visible'   => $model,
                        'format'    => 'html',
                        'value'     => ($model->getAttribute('birth') ? Yii::$app->formatter->asDate($model->getAttribute('birth'),sprintf('php:Y-m-d %02d 才 ', $model->client->getAttribute('age'))) : '(生年月日は不明) ') . $model->client->kana
                                   . '&nbsp;' . (($sex = $model->client->getAttribute('sex')) ? $sex->name : '(性別不明)')
                    ],
                    [
                        'label'     => '主訴',
                        'attribute' => 'complaint',
                        'format'    => 'raw',
                        'value'     => ($model) ? nl2br($model->complaint) : null
                    ],
                    [
                        'label'     => '質問票',
                        'attribute' => 'questionaire',
                        'format'    => 'raw',
                        'value'     => ($model) ? nl2br($model->questionaire) : null
                    ],
                ],
            ]) ?>
        </div>
        <div class="pull-right" style="width:40%;float:right;">
            <?php if ($img): ?>
            <img src="<?= $img ?>" height="150" width="220" alt="<?= $model->client_id ?>">
            <?php endif; ?>
        </div>
    </div>
    <div style="clear: both; margin: 0pt; padding: 0pt; "></div>

    <h4>相談会</h4>
    <?= \yii\widgets\ListView::widget([
        'dataProvider'   => new \yii\data\ActiveDataProvider([
            'query'      => Interview::find()->where(['itv_id' => $model->itv_id]),
            'pagination' => false,
        ]),
        'layout' => '{items}',
        'itemView' => 'customer-print-item-itv',
        'emptyText' => '終了した相談会はありません',
    ]) ?>

    <h4>適用書</h4>
    <?= \yii\widgets\ListView::widget([
        'dataProvider'   => new \yii\data\ActiveDataProvider([
            'query'      => $model->getRecipe()->active(),
            'pagination' => false,
        ]),
        'layout' => '{items}',
        'itemView' => 'customer-print-item-recipe',
        'emptyText' => '適用書はまだありません',
    ]) ?>

    <div class="btn-group-vertical">

    <?php
        $migrate_id = \common\models\Membercode::find()
                    ->where([
                        'directive'   => 'webdb20',
                        'customer_id' => $model->client_id,
                    ])
                    ->andWhere(['>','migrate_id',0])
                    ->select('migrate_id')
                    ->scalar();

    if($migrate_id)
        //$query = \common\models\webdb20\Karute::find()->where(['customerid' => $migrate_id]);
    ?>
    <?php if(isset($query) && $query->exists()): ?>
        <?= \yii\widgets\ListView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query'      => $query,
                'pagination' => false,
            ]),
            'layout'       => '{items}',
            'itemView'     => function ($model, $key, $index, $widget)
            {
                return Html::a('旧カルテ'.$model->karuteid,['/karute/print','id'=>$model->karuteid],['class'=>'btn btn-info']);
            },
        ]) ?>
    <?php endif ?>

    </div>
    <div class="pull-left">
        <?= Html::a('カルテ印刷', ['print-karute', 'id' => $model->itv_id, 'format' => 'pdf'], ['class' => "btn btn-default", 'target' => '_blank', 'style' => 'margin-right:10px;']) ?><br>
    </div>
    <div class="pull-left">
        <?= Html::a('戻る', ['view', 'id' => $model->itv_id], ['class' => 'btn btn-default']) ?>
    </div>
</div>
