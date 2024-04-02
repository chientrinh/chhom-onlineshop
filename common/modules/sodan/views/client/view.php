<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use \yii\grid\GridView;
use \common\models\FileForm;
use \yii\bootstrap\ActiveForm;
use \yii\data\ActiveDataProvider;
use \backend\models\Staff;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/client/view.php $
 * $Id: view.php 4135 2019-03-28 04:55:37Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\sodan\Interview
 */

$this->params['breadcrumbs'][] = ['label' => sprintf('%s (%s)', $model->name, $model->kana)];
$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'], 'label');
rsort($labels);
$labels[] = Yii::$app->name;
$this->title = implode(' | ', $labels);
?>

<div class="interview-view col-md-12">

    <?php if($model->customer->isExpired()): ?>
        <p class="alert alert-danger">
            この顧客は無効になりました。復活させるにはシステム担当者に以下のコマンドを実行するよう依頼してください。<br>
            <code>yii customer/activate <?= $model->client_id ?></code>
        </p>
    <?php endif ?>
    <h2>
        <?= $model->name ?>
        <?= $model->isAnimal() ? Html::img(Url::base().'/img/paw.png',['class'=>'icon','title'=>'動物相談です']) : null ?>
    </h2>

    <div class="row">
    <div class="col-md-10">
        <div class="col-md-10">
            <?= DetailView::widget([
                'model' => $model,
                'options'    => ['class'=>'table table-condensed'],
                'attributes' => [
                    [
                        'label'    => '',
                        'format'   => 'html',
                        'value'    => (($birth = $model->customer->birth)
                            ? Yii::$app->formatter->asDate($birth, sprintf('php: Y-m-d 生まれ  %02d 才',$model->customer->age))
                                : Html::tag('span','(生年月日は不明)',['class'=>'not-set'])
                        ) . '&nbsp;' . $model->kana
                          . '&nbsp;' . (($sex = $model->getAttribute('sex')) ? $sex->name : '(性別不明)')
                    ],
                    [
                        'attribute'=> 'note',
                        'format'   => 'ntext',
                    ],
                    [
                        'attribute'=> 'client_id',
                        'format'   => 'html',
                        'value'    => $model->client_id ? Html::a(sprintf('%06d',$model->client_id),['/customer/view','id'=>$model->client_id]) : null,
                        'visible'  => ('app-frontend' !== Yii::$app->id),
                    ],
                    [
                        'attribute'=> 'client_id',
                        'format'   => 'html',
                        'value'    => $model->client_id ? sprintf('%06d',$model->client_id) : null,
                        'visible'  => ('app-frontend' === Yii::$app->id),
                    ],
                    [
                        'attribute'=> 'grade_id',
                        'format'   => 'html',
                        'value'    => $model->customer ? $model->customer->grade->name : ''
                    ],
                    [
                        'attribute'=> 'branch_id',
                        'format'   => 'html',
                        'value'    => $model->branch ? $model->branch->name : ''
                    ],
                    [
                        'attribute'=> 'homoeopath_id',
                        'format'   => 'html',
                        'value'    => $model->homoeopath ? $model->homoeopath->homoeopathname : null
                    ],
                    [
                        'attribute'=> 'parent_name',
                        'format'   => 'html',
                        'value'    =>($model->customer->parent) ? $model->customer->parent->name :  $model->parent_name
                    ],
                    [
                        'attribute'=> 'ng_flg',
                        'format'   => 'html',
                        'value'    => $model->ng_flg ? '公開NG' : '公開OK'
                    ],
                    'skype',
                    [
                        'attribute'=> 'agreement',
                        'format'   => 'raw',
                        'value'    => GridView::widget([
                            'tableOptions' => ['class'=>'table table-condensed table-striped'],
                            'dataProvider' => new ActiveDataProvider([
                                'query' => $model->getAgreement(),
                                'sort'  => [
                                    'defaultOrder' => ['update_date' => SORT_DESC],
                                ],
                            ]),
                            'layout' => '{items}{pager}',
                            'showOnEmpty' => false,
                            'emptyText' => Yii::$app->formatter->nullDisplay,
                            'columns' => [
                                [
                                    'attribute' => 'basename',
                                    'format'    => 'html',
                                    'value'     => function($data)
                                    {
                                        return Html::a($data->basename, ['view','id'=>$data->file_id, 'target'=>'file']);
                                    }
                                ],
                                [
                                    'attribute' => 'create_date',
                                    'format'    => 'html',
                                    'value'     => function($data)
                                    {
                                        return Yii::$app->formatter->asDate($data->create_date,'php:Y-m-d H:i ')
                                                  . sprintf('(%s)', $data->creator->name01);
                                    }
                                ],
                                [
                                    'attribute' => 'update_date',
                                    'format'    => 'html',
                                    'value'     => function($data)
                                    {
                                        return Yii::$app->formatter->asDate($data->update_date,'php:Y-m-d H:i ')
                                                  . sprintf('(%s)', $data->updator->name01);
                                    }
                                ],
                            ],
                        ])
                    ],
                    [
                        'attribute'=> 'questionnaires',
                        'format'   => 'raw',
                        'value'    => GridView::widget([
                            'tableOptions' => ['class'=>'table table-condensed table-striped'],
                            'dataProvider' => new ActiveDataProvider([
                                'query' => $model->getQuestionnaires(),
                                'sort'  => [
                                    'defaultOrder' => ['update_date' => SORT_DESC],
                                ],
                            ]),
                            'layout' => '{items}{pager}',
                            'showOnEmpty' => false,
                            'emptyText' => Yii::$app->formatter->nullDisplay,
                            'columns' => [
                                [
                                    'attribute' => 'basename',
                                    'format'    => 'html',
                                    'value'     => function($data)
                                    {
                                        return Html::a($data->basename, ['view','id'=>$data->file_id, 'target'=>'file']);
                                    }
                                ],
                                [
                                    'attribute' => 'create_date',
                                    'format'    => 'html',
                                    'value'     => function($data)
                                    {
                                        return Yii::$app->formatter->asDate($data->create_date,'php:Y-m-d H:i ')
                                                  . sprintf('(%s)', $data->creator->name01);
                                    }
                                ],
                                [
                                    'attribute' => 'update_date',
                                    'format'    => 'html',
                                    'value'     => function($data)
                                    {
                                        return Yii::$app->formatter->asDate($data->update_date,'php:Y-m-d H:i ')
                                                  . sprintf('(%s)', $data->updator->name01);
                                    }
                                ],
                            ],
                        ])
                    ],
                    [
                        'attribute'=> 'report',
                        'format'   => 'raw',
                        'value'    => GridView::widget([
                            'tableOptions' => ['class'=>'table table-condensed table-striped'],
                            'dataProvider' => new ActiveDataProvider([
                                'query' => $model->getReport(),
                                'sort'  => [
                                    'defaultOrder' => ['update_date' => SORT_DESC],
                                ],
                            ]),
                            'layout' => '{items}{pager}',
                            'showOnEmpty' => false,
                            'emptyText' => Yii::$app->formatter->nullDisplay,
                            'columns' => [
                                [
                                    'attribute' => 'basename',
                                    'format'    => 'html',
                                    'value'     => function($data)
                                    {
                                        return Html::a($data->basename, ['view','id'=>$data->file_id, 'target'=>'file']);
                                    }
                                ],
                                [
                                    'attribute' => 'create_date',
                                    'format'    => 'html',
                                    'value'     => function($data)
                                    {
                                        return Yii::$app->formatter->asDate($data->create_date,'php:Y-m-d H:i ')
                                                  . sprintf('(%s)', $data->creator->name01);
                                    }
                                ],
                                [
                                    'attribute' => 'update_date',
                                    'format'    => 'html',
                                    'value'     => function($data)
                                    {
                                        return Yii::$app->formatter->asDate($data->update_date,'php:Y-m-d H:i ')
                                                  . sprintf('(%s)', $data->updator->name01);
                                    }
                                ],
                            ],
                        ])
                    ],
                    [
                        'attribute'=> 'binaries',
                        'format'   => 'raw',
                        'value'    => GridView::widget([
                            'tableOptions' => ['class'=>'table table-condensed table-striped'],
                            'dataProvider' => new ActiveDataProvider([
                                'query' => $model->getBinaries(),
                                'sort'  => [
                                    'defaultOrder' => ['update_date' => SORT_DESC],
                                ],
                            ]),
                            'layout' => '{items}{pager}',
                            'showOnEmpty' => false,
                            'emptyText'   => 'まだありません',
                            'columns' => [
                                [
                                    'attribute' => 'basename',
                                    'format'    => 'html',
                                    'value'     => function($data)
                                    {
                                        return Html::a($data->basename, ['view','id'=>$data->file_id, 'target'=>'file']);
                                    }
                                ],
                                'property',
                                [
                                    'attribute' => 'create_date',
                                    'format'    => 'html',
                                    'value'     => function($data)
                                    {
                                        return Yii::$app->formatter->asDate($data->create_date,'php:Y-m-d H:i ')
                                                  . sprintf('(%s)', $data->creator->name01);
                                    }
                                ],
                                [
                                    'attribute' => 'update_date',
                                    'format'    => 'html',
                                    'value'     => function($data)
                                    {
                                        return Yii::$app->formatter->asDate($data->update_date,'php:Y-m-d H:i ')
                                                  . sprintf('(%s)', $data->updator->name01);
                                    }
                                ],
                            ],
                        ])
                    ],
                ]
            ]) ?>
        </div>
        <div class="col-md-2">
            <img src="<?= $img ?>" height="150" width="210" alt="<?= $model->client_id ?>">
        </div>
    <?php if (Yii::$app->id === 'app-backend'): ?>
    <?php $ff = new FileForm(); ?>
    <div class="col-md-6" style="height:209px;">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Html::label($model->getAttributeLabel('agreement')) ?>
            </div>
            <div class="panel-body">
                <?php if($qnr = $model->agreement): ?>
                    <p class="help-block">同意書は<?= count($qnr) ?>件アップロード済みです</p>
                <?php else: ?>
                    <p class="help-block">同意書はアップロードされていません</p>
                <?php endif;?>

                <?php $form = ActiveForm::begin([
                    'id'     => 'form-agreement',
                    'action' => ['fileupload','id' => 'agreement', 'client_id' => $model->client_id],
                    'layout' => 'default',
                    'method' => 'post',
                    'options'=> ['enctype' => 'multipart/form-data'],
                ]); ?>
                <?= $form->field($ff, 'tgtFile')->label(false)->fileInput() ?>
                <?= Html::submitButton($model->agreement ? '追加' : '登録' ,['class'=>'btn btn-primary']) ?>
                <?php $form->end() ?>
            </div>
        </div>
    </div>

    <?php $ff = new FileForm() ?>
    <div class="col-md-6" style="height:209px;">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Html::label($model->getAttributeLabel('questionnaires')) ?>
            </div>
            <div class="panel-body">
                <?php if($qnr = $model->questionnaires): ?>
                    <p class="help-block">質問票は<?= count($qnr) ?>件アップロード済みです</p>
                <?php else: ?>
                    <p class="help-block">質問票はアップロードされていません</p>
                <?php endif ?>

                <?php $form = ActiveForm::begin([
                    'id'     => 'form-questionnaire',
                    'action' => ['fileupload','id' => 'questionnaire', 'client_id' => $model->client_id],
                    'layout' => 'default',
                    'method' => 'post',
                    'options'=> ['enctype' => 'multipart/form-data'],
                ]); ?>
                <?= $form->field($ff, 'tgtFile')->label(false)->fileInput() ?>
                <?= Html::submitButton($model->questionnaires ? '追加' : '登録' ,['class'=>'btn btn-primary']) ?>
                <?php $form->end() ?>
            </div>
        </div>
    </div>

    <?php $ff = new FileForm() ?>
    <div class="col-md-6" style="height:209px;">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Html::label($model->getAttributeLabel('report')) ?>
            </div>
            <div class="panel-body">
                <?php if($qnr = $model->report): ?>
                    <p class="help-block">事前報告書は<?= count($qnr) ?>件アップロード済みです</p>
                <?php else: ?>
                    <p class="help-block">事前報告書はアップロードされていません</p>
                <?php endif ?>

                <?php $form = ActiveForm::begin([
                    'id'     => 'form-report',
                    'action' => ['fileupload','id' => 'report', 'client_id' => $model->client_id],
                    'layout' => 'default',
                    'method' => 'post',
                    'options'=> ['enctype' => 'multipart/form-data'],
                ]); ?>
                <?= $form->field($ff, 'tgtFile')->label(false)->fileInput() ?>
                <?= Html::submitButton($model->report ? '追加' : '登録' ,['class'=>'btn btn-primary']) ?>
                <?php $form->end() ?>
            </div>
        </div>
    </div>

    <?php $ff = new FileForm() ?>
    <div class="col-md-6" style="height:209px;">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Html::label($model->getAttributeLabel('binaries')) ?>
            </div>
            <div class="panel-body">
                <?php if($qnr = $model->binaries): ?>
                    <p class="help-block">その他の資料は<?= count($qnr) ?>件アップロード済みです</p>
                <?php else: ?>
                    <p class="help-block">その他の資料はアップロードされていません</p>
                <?php endif ?>

                <?php $form = ActiveForm::begin([
                    'id'     => 'form-report',
                    'action' => ['fileupload','id' => 'binaries', 'client_id' => $model->client_id],
                    'layout' => 'default',
                    'method' => 'post',
                    'options'=> ['enctype' => 'multipart/form-data'],
                ]); ?>
                <?= $form->field($ff, 'tgtFile')->label(false)->fileInput() ?>
                <?= Html::submitButton($model->binaries ? '追加' : '登録' ,['class'=>'btn btn-primary']) ?>
                <?php $form->end() ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    </div>
        <?php if(Yii::$app->user->identity instanceof Staff): ?>
            <?php $is_disabled = ($model->interviews) ? '' : 'disabled'; ?>
            <?= Html::a('配付資料',['print','id'=>$model->client_id,'page'=>'welcome','format'=>'pdf'],['class'=>"btn btn-default {$is_disabled}",'target'=>'_blank', 'style' => 'margin-left:10px;']) ?><br><br>
            <?= Html::a('修正',['update','id'=>$model->client_id,'target'=>'client'],['class'=>'btn btn-primary', 'style' => 'margin-left:10px;']) ?>
        <?php endif ?>
    </div>

    <h4>クーポン利用履歴</h4>
    <?= GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $model->getCouponLog()]),
        'columns'   => [
                'log_id',
                [
                    'attribute' => 'product_id',
                    'format'    => 'html',
                    'value'     => function($data){ return $data->product->name; },
                ],
                'create_date',
                [
                    'attribute' => 'created_by',
                    'format'    => 'html',
                    'value'     => function($data){ return $data->creator->name; },
                ]
            ]
    ]) ?>

    <h4>キャンセル待ち
        <?php if(Yii::$app->user->identity instanceof Staff): ?>
        <?= Html::a('＋', ['wait-list/create','client_id'=>$model->client_id], ['class' => 'btn btn-xs btn-default','title'=>'キャンセル待ちを追加します']) ?>
        <?php endif ?>
    </h4>
    <?= \common\modules\sodan\widgets\WaitListGrid::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $model->getWaitlist()]),
        'attributes'   => ['wait_id','branch_id','homoeopath_id','note','expire_date']
    ]) ?>

    <h4>相談会
        <?php if(Yii::$app->user->identity instanceof Staff): ?>
        <?= Html::a('＋', ['book','id'=>$model->client_id], ['class' => 'btn btn-xs btn-default','title'=>'相談会を予約します']) ?>
        <?php endif ?>
    </h4>

    <?php if($complaints = \yii\helpers\ArrayHelper::getColumn($model->interviews,'complaint')): ?>
        <p>主訴:
        <?= Html::tag('span',implode('→',array_unique($complaints))) ?>
        </p>
    <?php endif ?>
    <?= \common\modules\sodan\widgets\InterviewGrid::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $model->getInterviews(),
            'sort'  => ['defaultOrder' => ['itv_date'=> SORT_DESC]],
        ]),
        'attributes'   => ['itv_id','branch_id','itv_date','itv_time','homoeopath_id','product_id','status_id'],
    ]) ?>

    <h4>適用書</h4>
    <?= $this->render('../admin/recipe-grid',[
        'dataProvider' => new ActiveDataProvider([
            'query' => $model->getRecipes(),
            'sort'  => ['defaultOrder'=> ['create_date' => SORT_DESC] ]
        ]),
        'searchModel'  => null,
    ]) ?>

    <?php if (Yii::$app->id === 'app-backend'): ?>
        <div class="pull-left">
            <?= Html::a('総カルテ', ['print','id'=>$model->client_id,'target'=>'client'],['class'=>'btn btn-default', 'style' => 'margin-right:10px;']) ?>
        </div>
        <div class="pull-left">
            <?= Html::a('総適用書', ['print','id' => $model->client_id, 'target' => 'client', 'only' => 'recipe'], ['class' => 'btn btn-default']) ?>
        </div>
    <?php endif; ?>
</div>

