<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/admin/interview-update.php $
 * $Id: interview-update.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\sodan\Interview
 */

$this->title = sprintf('%s | %s | %s', $model->itv_id, '相談会', Yii::$app->name);
$this->params['breadcrumbs'][] = ['label'=>'相談会','url'=>['interview-index']];
$this->params['breadcrumbs'][] = $model->itv_id;

Yii::$app->formatter->nullDisplay = '<span class="not-set">(セットされていません)</span>';

$query  = \common\models\Product::find()->where(['category_id'=>8])
        ->andWhere(['like','name','健康相談'])
        ->orderBy('name DESC');
$age    = $model->client->age;
if(null === $age) { }
elseif(13 < $age) { $query->andWhere(['like','name','大人']); }
elseif($age < 13) { $query->andWhere(['not',['like','name','大人']]); }

$products = $query->all();
$products = \yii\helpers\ArrayHelper::map($products, 'product_id', 'name');

$status = \yii\helpers\ArrayHelper::map(\common\models\sodan\InterviewStatus::find()->select(['status_id','name'])->asArray()->all(), 'status_id', 'name');
?>
<div class="interview-update">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'itv_date',
                'format'    => 'html',
                'value'     => Yii::$app->formatter->asDate($model->itv_date,'php:Y-m-d (D) ')
                             . Yii::$app->formatter->asTime($model->itv_time,'php:H:i')
            ],
            [
                'attribute' => 'homoeopath_id',
                'format'    => 'html',
                'value'     => $model->homoeopath
                           ? Html::a($model->homoeopath->name,['interview-index','Interview[homoeopath_id]'=>$model->homoeopath_id])
                           : null,
            ],
            [
                'attribute' => 'client_id',
                'format'    => 'html',
                'value'     => $model->client
                           ? Html::a($model->client->name,['view','id'=>$model->client_id,'target'=>'client']) . sprintf(' (%02d 才)', $model->client->age)
                           : null,
            ],
        ],
    ]) ?>


<?php $form = \yii\bootstrap\ActiveForm::begin([
    'method' => 'post',
]) ?>

    <?= $form->field($model, 'status_id')->dropDownList($status) ?>
    <?= $form->field($model, 'product_id')->dropDownList($products) ?>
    <?= $form->field($model, 'complaint')->textInput() ?>
    <?= $form->field($model, 'officer_use')->textarea(['rows' => 6]) ?>

<?= $form->field($model,'complaint')->textInput() ?>
<?= $form->field($model,'presence')->textArea(['rows'=>6]) ?>
<?= $form->field($model,'impression')->textArea(['rows'=>6]) ?>
<?= $form->field($model, 'summary')->textArea(['rows' => 6]) ?>
<?= $form->field($model, 'progress')->textArea(['rows' => 6]) ?>
<?= $form->field($model,'advice')->textArea(['rows'=>6]) ?>

<?php if(Yii::$app->user->identity instanceof \backend\models\Staff): ?>
<?= $form->field($model,'officer_use')->textArea() ?>
<?php endif ?>

    <div class="row">
    <div class="col-md-6">
        <?= Html::submitButton('更新',['class'=>'btn btn-primary']) ?>
    </div>
    </div>

<?php $form->end() ?>
</div>
