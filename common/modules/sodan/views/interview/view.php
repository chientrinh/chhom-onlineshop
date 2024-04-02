<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use \common\models\sodan\Client;
use \common\models\sodan\WaitList;
use \common\models\sodan\InterviewStatus;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/interview/view.php $
 * $Id: view.php 4125 2019-03-20 10:00:56Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\sodan\Interview
 */

$this->params['breadcrumbs'][] = ['label' => $model->itv_id, 'url' => \yii\helpers\Url::current()];
$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;

Yii::$app->formatter->nullDisplay = '<span class="not-set">(セットされていません)</span>';

foreach(['presence','impression','advice','officer_use'] as $attr)
{
    if(! strlen(trim($model->$attr)))
        $model->$attr = null;
}

$fmt = Yii::$app->formatter;

$btnPurchase = null;
$btnCancel   = null;
$btnReserve  = null;

if(($user = Yii::$app->user->identity) && $user instanceof \backend\models\Staff)
{
    $model->scenario = $model::SCENARIO_PAY;
    $model->validate();

    if($model->hasErrors())
        $options = ['class'=>'pull-right btn btn-xs btn-default','title'=>implode(';',$model->firstErrors)];
    else
        $options = ['class'=>'pull-right btn btn-xs btn-default'];

    if(InterviewStatus::PKEY_READY == $model->status_id)
    {
        $btnCancel = Html::a('予約をキャンセルする',[
            'cancelate',
            'id'=>$model->itv_id
        ], $options);

        $btnReserve = Html::a('予約票を選択印刷',[
            'template-select',
            'id' => $model->itv_id,
            'page' => 'reserve',
            'format' => 'pdf',
        ], array_merge($options, ['target' => '_brank', 'style' => 'margin-right:10px;', 'title'  => '予約票に追加するテンプレートを選択して印刷します']));

        $btnReserve .= Html::a('予約票を印刷',[
            'print',
            'id' => $model->itv_id,
            'page' => 'reserve',
            'format' => 'pdf'
        ], array_merge($options, ['target' => '_brank', 'style' => 'margin-right:10px;']));
    }

    if(InterviewStatus::PKEY_READY == $model->status_id || InterviewStatus::PKEY_DONE == $model->status_id || InterviewStatus::PKEY_KARUTE_DONE == $model->status_id)
    {
        $btnPurchase = Html::a('お会計を起票する',[
            'purchase/create',
            'id' => $model->itv_id
        ], $options);
    }
}

$icon = $model->hadMetBefore() ? null : Html::tag('span','★',['style'=>'color:#eea236;','title'=>'初めての相談です']);

$csscode = "
#grid-view-recipe .not-set {
  color:white;
}
";
$jscode = "
if ($('#grid-view-recipe').height() > 300) {
  $('#grid-view-recipe').css({'height':'300px', 'overflow-y':'scroll'});
}
if ($('#grid-past-recipe').height() > 300) {
  $('#grid-past-recipe').css({'height':'300px', 'overflow-y':'scroll'});
}

$('.breadcrumb a').click(function(){
  if (!confirm('画面遷移してよろしいですか（カルテの保存は完了していますか）？')) {
    return false;
  }
});
";
$this->registerCss($csscode);
$this->registerJs($jscode);
?>
<script>
    function doneKarute() {
        if (!confirm('ステータスが「カルテ完了」になりますがよろしいですか？')) {
            return false;
        }
        // ストレージデータhiddenタグを作成
        $("<input>", {
            type: 'hidden',
            name: 'done_flg',
            value: '1'
        }).appendTo('form#itv_karute');
        $('form#itv_karute').submit();
    }

    function saveKarute() {
        // ストレージデータhiddenタグを作成
        $("<input>", {
            type: 'hidden',
            name: 'save_flg',
            value: '1'
        }).appendTo('form#itv_karute');
        $('form#itv_karute').submit();
    }

    function printRecipe() {
        var recipe_id = $('input[name=recipe_id]:checked').val();
        var itv_id = <?php echo $model->itv_id; ?>;

        if (!recipe_id) {
            return true;
        }
        window.open("print?id=" + itv_id + "&format=pdf&recipe_id=" + recipe_id, '_blank');
        return false;
    }

    function createRecipe() {
        // ストレージデータhiddenタグを作成
        $("<input>", {
            type: 'hidden',
            name: 'save_flg',
            value: '1'
        }).appendTo('form#itv_karute');
        $("<input>", {
            type: 'hidden',
            name: 'recipe_flg',
            value: '1'
        }).appendTo('form#itv_karute');
        $('form#itv_karute').submit();
    }
</script>

<div class="interview-view col-md-12">
    <div class="col-md-9">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'itv_date',
                    'format'    => 'html',
                    'value'     => Yii::$app->formatter->asDate($model->itv_date,'php:Y-m-d (D) ')
                                 . date('H:i', strtotime($model->itv_time))
                                 . ' ('. $model->duration . ' 分) '
                                 . Html::tag('span',$model->branch->name,['class'=>'pull-right'])
                                 . (((int)$model->pastitv <= 1) ? ' (初回)' : " ({$model->pastitv}回目)")
                                 . (($model->open_flg) ? ' ' . Html::tag('i', '', ['title' => '公開枠', 'style' => 'color:#337ab7;font-size:1.2em;', 'class' => 'glyphicon glyphicon-thumbs-up']) : '')
                ],
                [
                    'attribute' => 'homoeopath_id',
                    'format'    => 'html',
                    'value'     => ($model->homoeopath ? Html::a($model->homoeopath->homoeopathname,['homoeopath/view','id'=>$model->homoeopath_id]) : null),
                ],
                [
                    'attribute' => 'client_id',
                    'format'    => 'html',
                    'value'     => ($model->client
                                  ? Html::a($model->client->name . $icon, ['client/view','id'=>$model->client_id])
                                   . (($birth = $model->client->birth)
                                        ? Yii::$app->formatter->asDate($birth, sprintf('php: (Y-m-d 生まれ  %02d 才)',$model->client->age))
                                            : Html::tag('span','(生年月日は不明)',['class'=>'not-set']))
                                  : $fmt->asText(null)) . '&nbsp;' .
                                 (($model->client && ($client = Client::findOne($model->client_id)) && ! $client->isValid())
                                     ? Html::tag('span',implode('; ',$client->firstErrors),['class'=>'text-warning'])
                                         : ''),
                    'visible' => $model->getClient()->exists(),
                ],
                [
                    'attribute' => 'client_id',
                    'format'    => 'html',
                    'value'     => Html::a('クライアントを指定',['update', 'id' => $model->itv_id], ['class' => 'btn btn-xs btn-primary'])
                                 . ' '
                                 .  (WaitList::find()->active()->exists() ? Html::a('キャンセル待ちリストから指定',['room/search','target'=>'wait-list','id'=>$model->itv_id],['class'=>'btn btn-xs btn-warning']) : null),

                    'visible' => ! $model->getClient()->exists() && Yii::$app->user->identity instanceof \backend\models\Staff,
                ],
                [
                    'attribute' => 'product_id',
                    'format'    => 'html',
                    'value'     => ($model->product ? $model->product->name : Html::a('相談種別を指定',['update', 'id' => $model->itv_id], ['class' => 'btn btn-xs btn-primary']))
                ],
                [
                    'attribute' => 'status_id',
                    'format'    => 'raw',
                    'value'     => sprintf('%s %s %s', $model->status->name, $btnCancel, $btnReserve),
                ],
                [
                    'attribute' => 'purchase_id',
                    'format'    => 'raw',
                    'value'     => ($p = $model->purchase)
                                 ? '伝票番号：' . Html::a(sprintf('%06d', $model->purchase_id), ['/casher/default/view','id' => $model->purchase_id], ['target' => '_brank']) . '&nbsp;支払方法：' . $p->payment->name . '&nbsp;&yen;'. number_format($p->total_charge) . '&nbsp;' . ($p->isExpired() ? '無効' : ($p->paid ? '支払済' : '未払い'))
                                 : '記録なし' . $btnPurchase
                ],
            ],
        ]) ?>
    </div>
    <div class="col-md-2">
        <?php if ($img): ?>
        <img src="<?= $img ?>" height="150" width="220" alt="<?= $model->client_id ?>">
        <?php endif; ?>
    </div>

    <div class="row">
        <?php if(! $model->client_id /*クライアント未定義*/ || ((Yii::$app->get('user') && Yii::$app->user->identity instanceof \backend\models\Staff)) /* when Staff logged in */): ?>
            <p class="pull-left">
                <?php if ($cancel_set): ?>
                    <?= Html::a('キャンセル待ちを続ける', ['wait-list/create', 'itv_id' => $model->itv_id], ['class' => 'btn btn-danger']) ?>
                <?php endif; ?>
                <?= Html::a('修正', ['update','id' => $model->itv_id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('削除', ['delete', 'id' => $model->itv_id], ['class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '本当に削除してもいいですか',
                        'method'  => 'POST'
                    ],]) ?>
            </p>
        <?php endif ?>

        <?php if('app-backend' == Yii::$app->id): ?>
            <?php if ($model->client_id): ?>
                <p class="pull-left">
                    <?= Html::a('紙カルテ', [
                        'client/print',
                        'id' => $model->client_id,
                        'page' => 'kami-karute',
                        'format' => 'pdf',
                        'itv_id' => $model->itv_id
                        ], ['class' => "btn btn-default", 'target' => '_blank']) ?>
                </p>
            <?php endif; ?>
        <?php endif;?>
    </div>

    <h4><?= $model->getAttributeLabel('officer_use') ?></h4>
    <p class="well">
        <?= Yii::$app->formatter->asNtext($model->officer_use) ?>
    </p>

    <h4><?= $model->getAttributeLabel('note') ?></h4>
    <p class="well">
        <?= Yii::$app->formatter->asNtext($model->note) ?>
    </p>

    <?php if($model->client && ! $model->isExpired()): ?>
    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'id'     => 'itv_karute',
        'action' => ['update', 'id' => $model->itv_id],
        'layout' => 'default',
        'method' => 'post',
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group col-md-6">
                <?php if($model->client && ! $model->isExpired()): ?>
                    <?= Html::submitButton('カルテ保存', ['class' => 'btn btn-primary', 'onclick' => 'return saveKarute();']) ?>
                    <?= Html::submitButton('カルテ完了', ['class' => 'btn btn-danger', 'onclick' => 'return doneKarute();']) ?>
                <?php endif ?>
                <?php if($model->recipe): ?>
                    <?= Html::a('適用書印刷', ['print', 'id' => $model->itv_id, 'format'=>'pdf'], ['class' => 'btn btn-success', 'target' => '_brank', 'onclick' => 'return printRecipe();']) ?>
                <?php else: ?>
                    <?= Html::a('適用書印刷', ['print', 'id' => $model->itv_id, 'format'=>'pdf'], ['class' => 'btn btn-default disabled']) ?>
                    <span class="help-block">適用書が作成できたら印刷可能になります</span>
                <?php endif ?>
                <?= Html::a('カルテ印刷', ['print-karute', 'id' => $model->itv_id], ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <div>
        <div class="col-md-4">
            <?= $form->field($model, 'complaint')->textArea(['rows' => 5]) ?>
            <?= $form->field($model, 'questionaire')->textArea(['rows' => 15]) ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'presence')->textArea(['rows' => 5]) ?>
            <?= $form->field($model, 'impression')->textArea(['rows' => 5]) ?>
            <?= $form->field($model, 'summary')->textArea(['rows' => 5]) ?>
            <?= $form->field($model, 'advice')->textArea(['rows' => 5])->hint(false) ?>
            <?= $form->field($model, 'progress')->textArea(['rows' => 5]) ?>
        </div>
        <div class="col-md-4">
            <h4>
                <?= Html::label($model->getAttributeLabel('create_recipe')) ?>
                <?= Html::submitButton('作成', ['class' => 'btn btn-xs btn-primary', 'onclick' => 'return createRecipe();']) ?>
            </h4>

            <?= \yii\grid\GridView::widget([
                'id' => 'grid-recipe',
                'dataProvider'=> new \yii\data\ActiveDataProvider([
                    'query' => \common\models\Recipe::find()->active()->andWhere(['client_id' => $model->client_id])->andWhere(['itv_id' => $model->itv_id])->orderBy(['recipe_id' => SORT_DESC]),
                    'sort'  => false,
                ]),
                'layout' => '{items}{pager}',
                'columns' => [
                    [
                        'attribute' => 'recipe_id',
                        'format'    => 'raw',
                        'value'     => function($data) {
                            $options = [
                                'label'  => '',
                                'value'  => $data->recipe_id,
                                'uncheck'=> null,
                                'checked'=> null,
                            ];
                            return Html::radio('recipe_id', false, $options) . ' ' . Html::a($data->recipe_id, ['/recipe/admin/view', 'id' => $data->recipe_id]);
                        }
                    ],
                    [
                        'attribute' => 'create_date',
                        'value'     => function($data)
                        {
                            return $data->create_date;
                        },
                    ],
                    [
                        'attribute' => 'homoeopath_id',
                        'value'     => function($data)
                        {
                            return $data->homoeopath->homoeopathname;
                        },
                    ],
                ],
            ]) ?>

            <h4><label>過去の適用書の検索</label></h4>
            <?= \yii\grid\GridView::widget([
                'id' => 'grid-past-recipe',
                'dataProvider'=> new \yii\data\ActiveDataProvider([
                    'query' => \common\models\Recipe::find()->active()->andWhere(['client_id' => $model->client_id])->andWhere("itv_id <> {$model->itv_id} OR itv_id IS NULL")->orderBy(['recipe_id' => SORT_DESC]),
                    'sort'  => false,
                ]),
                'layout' => '{items}{pager}',
                'columns' => [
                    [
                        'attribute' => 'recipe_id',
                        'format'    => 'raw',
                        'value'     => function($data) {
                            return Html::a($data->recipe_id, ['/recipe/admin/view', 'id' => $data->recipe_id]);
                        }
                    ],
                    [
                        'attribute' => 'create_date',
                        'value'     => function($data)
                        {
                            return $data->create_date;
                        },
                    ],
                    [
                        'attribute' => 'homoeopath_id',
                        'value'     => function($data)
                        {
                            return $data->homoeopath->homoeopathname;
                        },
                    ],
                ],
            ]) ?>
        </div>
    <?php endif ?>
    </div>
    <div class="row">
        <div class="col-md-12">

            <div class="form-group col-md-6">
                <?php if($model->client && ! $model->isExpired()): ?>
                    <?= Html::submitButton('カルテ保存', ['class' => 'btn btn-primary', 'onclick' => 'return saveKarute();']) ?>
                    <?= Html::submitButton('カルテ完了', ['class' => 'btn btn-danger', 'onclick' => 'return doneKarute();']) ?>
                <?php endif ?>
                <?php if($model->recipe): ?>
                    <?= Html::a('適用書印刷', ['print', 'id' => $model->itv_id, 'format'=>'pdf'], ['class' => 'btn btn-success', 'target' => '_brank', 'onclick' => 'return printRecipe();']) ?>
                <?php else: ?>
                    <?= Html::a('適用書印刷', ['print', 'id' => $model->itv_id, 'format'=>'pdf'], ['class' => 'btn btn-default disabled']) ?>
                    <span class="help-block">適用書が作成できたら印刷可能になります</span>
                <?php endif ?>
                <?= Html::a('カルテ印刷', ['print-karute', 'id' => $model->itv_id], ['class' => 'btn btn-default']) ?>
            </div>

            <?= DetailView::widget([
                'options'=>['class'=>'col-md-4 table-condenced text-right pull-right'],
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'create_date',
                        'value'     => Yii::$app->formatter->asDate($model->create_date, 'php:Y-m-d H:i ')
                                     . (sprintf('(%s)', $model->creator ? $model->creator->name01 : $model->homoeopath->name)),
                    ],
                    [
                        'attribute' => 'update_date',
                        'value'     => Yii::$app->formatter->asDate($model->update_date, 'php:Y-m-d H:i ')
                                     . (sprintf('(%s)', $model->updator ? $model->updator->name01 : $model->homoeopath->name)),
                    ],
                ],
            ]) ?>
        </div>
    </div>
    <?php if($model->client && !$model->isExpired()): ?>
        <?php $form->end() ?>
    <?php endif ?>
</div>
