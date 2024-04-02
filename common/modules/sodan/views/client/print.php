<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/client/print.php $
 * $Id: print.php 4125 2019-03-20 10:00:56Z kawai $
 */

use \common\models\sodan\InterviewStatus;
use \yii\helpers\Html;
use common\models\sodan\Interview;
use common\models\BinaryStorage;

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view','id'=>$model->client_id]];
$this->params['breadcrumbs'][] = ($only === 'recipe') ? ['label' => '適用書'] : ['label' => '総カルテ'];
$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;

// 最新の相談会を取得
$previous_itv = Interview::find()
    ->where(['client_id' => $model->client_id])
    ->andWhere(['in', 'status_id', [InterviewStatus::PKEY_DONE, InterviewStatus::PKEY_KARUTE_DONE]])
    ->orderBy(['itv_date' => SORT_DESC, 'itv_time' => SORT_DESC])
    ->one();

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
    <div style="width:100%">
        <div style="width:60%;float:left;">
            <?= \yii\widgets\DetailView::widget([
                'model'      => $model,
                'options'    => ['class'=>'table'],
                'attributes' => [
                    [
                        'label'     => '氏名',
                        'attribute' => 'name',
                        'format'    => 'raw',
                        'value'     => $model->name,
                    ],
                    [
                        'label'     => '',
                        'visible'   => $model,
                        'format'    => 'html',
                        'value'     => (($birth = $model->customer->birth)
                                        ? Yii::$app->formatter->asDate($birth, sprintf('php: Y-m-d (%02d 才)', $model->customer->age)) : Html::tag('span', '(生年月日は不明)', ['class' => 'not-set']))
                                    . ' ' . $model->kana
                                    . '&nbsp;' . (($sex = $model->getAttribute('sex')) ? $sex->name : '(性別不明)')
                    ],
                    [
                        'label'     => '主訴',
                        'attribute' => 'complaint',
                        'format'    => 'raw',
                        'value'     => ($previous_itv) ? nl2br($previous_itv->complaint) : null
                    ],
                    [
                        'label'     => '質問票',
                        'attribute' => 'questionaire',
                        'format'    => 'raw',
                        'value'     => ($previous_itv) ? nl2br($previous_itv->questionaire) : null
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
            'query'      => $model->getInterviews()->andWhere(['in', 'status_id', [InterviewStatus::PKEY_DONE, InterviewStatus::PKEY_KARUTE_DONE]])->orderBy('itv_id ASC'),
            'pagination' => false,
        ]),
        'layout' => '{items}',
        'itemView' => 'customer-print-item-itv',
        'emptyText' => '終了した相談会はありません',
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
        <?php if ($only === 'recipe'):?>
            <?= Html::a('適用書印刷', ['print', 'id' => $model->client_id, 'page'=>'print', 'format' => 'pdf', 'only' => 'recipe'], ['class' => "btn btn-default", 'target' => '_blank', 'style' => 'margin-right:10px;']) ?><br>
        <?php else:?>
            <?= Html::a('総カルテ印刷', ['print', 'id' => $model->client_id, 'page'=>'print', 'format' => 'pdf'], ['class' => "btn btn-default", 'target' => '_blank', 'style' => 'margin-right:10px;']) ?><br>
        <?php endif;?>
    </div>
    <div class="pull-left">
        <?= Html::a('戻る',['view','id'=>$model->client_id,'target'=>'client'],['class'=>'btn btn-default']) ?>
    </div>
</div>
