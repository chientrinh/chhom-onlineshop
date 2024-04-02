<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\sodan\Room;
use common\models\sodan\RoomStatus;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/interview/_karute.php $
 * @version $Id: _karute.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  common\models\sodan\Room
 */

    $form = \yii\bootstrap\ActiveForm::begin([
        'id' => 'foo',
        'enableClientScript' => false,
        'fieldConfig' => [
        ],
    ]);
    $query = \common\models\sodan\InterviewStatus::find();
    if('app-frontend' == Yii::$app->id)
        $query->andWhere(['or',
                         ['<=','status_id',\common\models\sodan\InterviewStatus::PKEY_KARUTE_DONE],
                         ['status_id' => \common\models\sodan\InterviewStatus::PKEY_VOID],
    ]);
    $status   = ArrayHelper::map($query->all(),'status_id','name');
?>
<div class="interview-form col-md-12">

    <div class="row">

    <div class="col-md-6">
    <div class="row">

        <label class="control-label"><?= $model->getAttributeLabel('branch_id') ?></label>
        <p><?= ($b = $model->branch) ? $b->name : null ?></p>

        <div class="row">
            <div class="col-md-4">
                <label class="control-label"><?= $model->getAttributeLabel('itv_date') ?></label>
                <p><?= $model->itv_date ?></p>
            </div>
            <div class="col-md-4">
                <label class="control-label"><?= $model->getAttributeLabel('itv_time') ?></label>
                <p><?= $model->itv_time ?></p>
            </div>
            <div class="col-md-4">
                <label class="control-label"><?= $model->getAttributeLabel('duration') ?></label>
                <p><?= $model->duration ?></p>
            </div>
        </div>

    </div>
    </div>

        <div class="col-md-6 col-xs-4">

            <label class="control-label"><?= $model->getAttributeLabel('homoeopath_id') ?></label>
            <p><?= ($h = $model->homoeopath) ? $h->name : null ?></p>

            <label class="control-label"><?= $model->getAttributeLabel('client_id') ?></label>
            <p><?= ($c = $model->client) ? $c->name : null ?></p>

            <label class="control-label"><?= $model->getAttributeLabel('product_id') ?></label>
            <p><?= ($p = $model->product) ? $p->name : null ?></p>

            <?php if($model->isExpired()): ?>
                <?= $form->field($model, 'status_id')->dropDownList($status,['class'=> 'form-control','disabled'=>'disabled','title'=>'キャンセル後に状態を変えることはできません']) ?>
            <?php else: ?>
                <?= $form->field($model, 'status_id')->dropDownList($status,['class'=> 'form-control']) ?>
            <?php endif ?>

        </div>

    <?php if('app-backend' == Yii::$app->id): ?>
        <div class="col-md-12 col-xs-8">
        <?= $form->field($model, 'officer_use')->textArea() ?>
        </div>
    <?php endif;?>

    <div class="col-md-12 col-xs-8">
    <?php if(! $model->isNewRecord): ?>
        <div id="use-by-homoeopath" >
        <?= $form->field($model, 'complaint' )->textArea() ?>
        <?= $form->field($model, 'presence'  )->textArea(['rows'=>5]) ?>
        <?= $form->field($model, 'impression')->textArea(['rows'=>5]) ?>
        <?= $form->field($model, 'advice'    )->textArea(['rows'=>5]) ?>
        <?= $form->field($model, 'summary')->textArea(['rows' => 5]) ?>
        <?= $form->field($model, 'progress')->textArea(['rows' => 5]) ?>
        </div>
    <?php endif ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['name'   => 'command',
                                        'value' => 'edit',
                                        'class' => 'btn btn-primary']) ?>
    </div>
    </div>

    </div>

    <?php $form->end(); ?>

    <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>

</div>
