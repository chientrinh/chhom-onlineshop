<?php
/**
 * $URL: http://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/index.php $
 * $Id: index.php 3568 2017-08-25 08:54:21Z naito $
 *
 * @var $dataProvider
 * @var $searchModel
 */

use \yii\helpers\Html;
use yii\helpers\Url;

$b         = $this->context->module->branch;
$branch_id = $b ? $b->branch_id : '';
$label     = ($b && $b->isWarehouse()) ? '注文' : '売上';

$this->params['breadcrumbs'][] = ['label' => $label, 'url' => ['index'] ];

$q   = clone($dataProvider->query);
$sum = $q->select('SUM(total_charge)')->scalar();
?>

<div class="casher-default-index">
    <?php if((Yii::$app->user->identity->company_id != \common\models\Company::PKEY_TROSE)) { ?>
        <a href="_menu.php"></a>
    <?php } ?>
    <div class="body-content">

    <?php if((Yii::$app->user->identity->company_id != \common\models\Company::PKEY_TROSE)) { ?>

        <div class="list-group col-md-2">
            <?= $this->render('_menu') ?>
        </div>
    <?php } ?>
        
        <?php $form = \yii\bootstrap\ActiveForm::begin([
            'id'    => 'casher-index-form',
            'action'=> 'index',
            'method'=> 'get',
        ]); ?>

        <div class="col-md-10">
            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'layout'       => '{items}{pager}{summary}',
                'summaryOptions' => ['class'=>'pull-right small text-muted'],
                'tableOptions' => ['class'=>'table table-condensed table-striped'],
                'showFooter'   => true,
                'columns'      => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'checkboxOptions' => function ($model, $key, $index, $column)
                        {
                            return ['value' => $model->purchase_id];
                        },
                        'visible' => true,
                    ],
                    [
                        'attribute' => 'purchase_id',
                        'format'    => 'html',
                        'value'     => function($model){
                            return Html::a(sprintf('%06d',$model->purchase_id), ['view', 'id'=> $model->purchase_id]);
                        },
                        'contentOptions' => ['class'=>'text-center'],
                        'headerOptions'  => ['class'=>'col-xs-1 col-md-1'],
                    ],
                    [
                        'attribute' => 'create_date',
                        'format'    => 'html',
                        'value'     => function($model){
                            return date('m-d H:i', strtotime($model->create_date)); },
                        'filter' => \yii\jui\DatePicker::widget([
                            'model' => $searchModel,
                            'attribute'=>'create_date',
                            'language' => 'ja',
                            'dateFormat' => 'yyyy-MM-dd',
                            'options' => ['class'=>'form-control col-md-12'],
                        ]),
                        'contentOptions' => ['class'=>'text-nowrap'],
                        'headerOptions'  => ['class'=>'col-xs-1 col-md-1'],
                    ],
                    [
                        'attribute' => 'customer_id',
                        'format'    => 'html',
                        'value'     => function($model)
                        {
                            if($model->customer)
                                return Html::a($model->customer->name,['/customer/view','id'=>$model->customer_id],['style'=>'color:black']);

                            if($d = $model->delivery){ return $d->name; }

                            return '';
                        },
                        'headerOptions'  => ['class'=>'col-xs-3 col-md-3'],
                    ],
                    [
                        'attribute' => 'customer_msg',
                        'format'    => 'html',
                        'value'     => function($model)
                        {
                            if($msg = $model->customer_msg)
                                return Html::a(\yii\helpers\StringHelper::truncate($msg, 32),
                                               ['view','id'=>$model->purchase_id,'#'=>'customer_msg'],
                                               ['title'=>$msg,'style'=>'color:black']);
                        },
                     'headerOptions'  => ['class'=>'col-xs-3 col-md-3'],
                    ],
                    [
                        'attribute' => 'agent_id',
                        'label'     => 'サポート注文者',
                        'format'    => 'html',
                        'value'     => function($model)
                        {
                            if($model->agent)
                                return Html::a($model->agent->name, ['/customer/view', 'id' => $model->agent_id], ['style' => 'color:black']);

                            return '';
                        },
                        'headerOptions'  => ['class'=>'col-xs-3 col-md-3'],
                        'visible'   => in_array(Yii::$app->controller->id, ['atami','ropponmatsu']) || $branch_id == \common\models\Branch::PKEY_CHHOM_TOKYO,
                    ],
                    [
                        'attribute' => 'total_charge',
                        'format'    => 'raw',
                        'value'     => function($model)
                        {
                            if(in_array($this->context->id, ['atami','ropponmatsu']))
                            {
                                $route  = ['print', 'id'=>$model->purchase_id, 'format'=>'pdf'];
                                $title  = '納品書をダウンロードしますが、状態は変更しません';
                            }
                            else
                            {
                                $route = ['receipt', 'id'=>$model->purchase_id];
                                $title  = 'レシートを印刷します';
                            }

                            $label = Html::tag('i', '&nbsp;￥'.number_format($model->total_charge),
                                               ['class'=>'glyphicon glyphicon-print']);

                            return Html::a($label, $route, ['title'=> $title]);

                        },
                        'footer'         => '合計' . Yii::$app->formatter->asCurrency($sum),
                        'contentOptions' => ['class'=>'text-right'],
                        'footerOptions'  => ['class'=>'text-right'],
                    ],
                    [
                        'label'     => '送り状種別',
                        'attribute' => 'payment_id', // 本当は payment.handling を参照したい
                        'value'     => function($model){ return in_array($model->payment_id, [\common\models\Payment::PKEY_DROP_SHIPPING, // 代行発送
                                                                                              \common\models\Payment::PKEY_YAMATO_COD,    // ヤマト便 代引
                                                                                              \common\models\Payment::PKEY_POSTAL_COD,    // ゆうメール代引 Cash On Delivery
                                                                                              \common\models\Payment::PKEY_PARCEL_COD])   // ゆうパック代引 Cash On Delivery
                                                                ? '代引き'
                                                                : '発払い';
                        },
                        'contentOptions' => ['class'=>'text-center'],
                        'filter'    => ['no'=>'発払い','yes'=>'代引き'],
                        'headerOptions'  => ['class'=>'col-xs-2 col-md-2'],
                        'visible'   => in_array(Yii::$app->controller->id, ['atami','ropponmatsu']),
                    ],
                    [
                        'attribute' => 'pref_id',
                        'label'     => '都道府県',
                        'value'     => function($model){ return $model->delivery ? $model->delivery->pref->name : "";},
                        'filter'    => \yii\helpers\ArrayHelper::map(\common\models\Pref::find()->all(), 'pref_id','name'),
                        'visible'   => in_array(Yii::$app->controller->id, ['atami','ropponmatsu']),
                    ],
                    [
                        'attribute' => 'payment_id',
                        'value'     => function($model){ return $model->payment->name; },
                        'filter'    => \yii\helpers\ArrayHelper::map(\common\models\Payment::find()->all(), 'payment_id','name'),
                        'visible'   => ! in_array(Yii::$app->controller->id, ['atami','ropponmatsu']),
                    ],
//                    [
//                        'attribute' => 'shipped',
//                        'label'     => '発送の状態',
//                        'value'     => function($model){ return $model->shipped ? "発送済" : "未発送"; },
//                        'filter'    => [0 => "未発送", 1 => "発送済"],
//                        'headerOptions'  => ['class'=>'col-xs-2 col-md-2'],
//                        'contentOptions' => ['class'=>'text-center'],
//                        'visible'   => ! in_array(Yii::$app->controller->id, ['atami','ropponmatsu']),
//                    ],
                    [
                        'attribute' => 'status',
                        'value'     => function($model){ return \yii\helpers\ArrayHelper::getvalue($model,'purchaseStatus.name'); },
                        'filter'    => \yii\helpers\ArrayHelper::map(\common\models\PurchaseStatus::find()->all(), 'status_id','name'),
                        'headerOptions'  => ['class'=>'col-xs-2 col-md-2'],
                        'contentOptions' => ['class'=>'text-center'],
                    ],
//                    [
//                        'attribute' => 'company_id',
//                        'format'    => 'text',
//                        'value'     => function($model){ return $model->company ? $model->company->key : null; },
//                        'filter'    => \yii\helpers\ArrayHelper::map(\common\models\Company::find()->all(), 'company_id','name'),
//                        'contentOptions' => ['class'=>'text-uppercase'],
//                        'headerOptions'  => ['class'=>'col-md-1'],
//                    ],
//                    [
//                        'attribute' => 'branch_id',
//                        'format'    => 'html',
//                        'value'     => function($model){
//                            $name = ($b = $model->branch) ? $b->name : null;
//                            return Html::tag('span',\yii\helpers\StringHelper::truncate($model->branch->name,7),['title'=>$model->branch->name]);
//                        },
//                        'filter'    => \yii\helpers\ArrayHelper::map(\common\models\Branch::find()->orderBy(['name'=>SORT_DESC])->all(), 'branch_id','name'),
//                        'headerOptions' => ['class'=>'col-md-1'],
//                    ],
                    [
                        'attribute' => 'staff_id',
                        'format'    => 'html',
                        'value'     => function($model){ return ($s = $model->staff) ? $s->name01 : null; },
                        'filter'    => \yii\helpers\ArrayHelper::map(\backend\models\Staff::find()->all(), 'staff_id','name'),
                    ],
                ],
            ]); ?>
                        
        <?php if(isset($b) && $b->isWarehouse()): ?>
        <p>
            <?= Html::hiddenInput('basename', \backend\modules\casher\Module::getPrintBasename()) ?>
            <?= Html::submitButton("納品書を印刷",[
                'class'    => 'btn btn-warning',
                'title'    => '納品書をダウンロードし、ステータスを「発送済み」にします',
                'name'     => 'target',
                'value'    => 'default',
                'onClick'  => "this.form.action='print'",
            ]) ?>
        </p>
        
<?php if((Yii::$app->user->identity->company_id != \common\models\Company::PKEY_TROSE)) { ?>
        <p>
            <?= Html::submitButton("レメディーラベル出力",[
                'class'    => 'btn btn-info',
                'title'    => '滴下レメディーのラベルをプリントします',
                'name'     => 'target',
                'onClick'  => "this.form.action='print-remedy-label'",
            ]) ?>
        </p>
<?php } ?>
        <p>
            <?= Html::submitButton("ヤマト便CSV",[
                'id'       => 'submit-csv-prepaid',
                'class'    => 'btn btn-default',
                'title'    => 'ヤマト便のCSVを出力します',
                'name'     => 'target',
                'value'    => 'default',
                'onClick'  => "this.form.action='print-csv'",
            ]) ?>

            <?= Html::submitButton("ゆうプリ用CSV",[
                'id'       => 'submit-csv-prepaid',
                'class'    => 'btn btn-default',
                'title'    => 'ゆうプリ用のCSVを出力します',
                'name'     => 'target',
                'value'    => 'default',
                'onClick'  => "this.form.action='print-csv-for-yu-print'",
            ]) ?>
        </p>
        <?php else: ?>
    <?php if(($branch_id == \common\models\Branch::PKEY_CHHOM_TOKYO)) { ?>
        <div style="display:inline-block;">
            <?= Html::submitButton("配信チケット購入CSV",[
                'class'    => 'btn btn-info',
                'title'    => 'ライブ配信チケット注文情報（4/26分〜現時点）のCSVを出力します',
                'name'     => 'target',
                'onClick'  => "this.form.action='".Url::home(true)."/casher/default/live-data-print-stat'",
            ]) ?>
            <?= Html::hiddenInput('basename', \backend\modules\casher\Module::getPrintBasename()) ?>
            <?= Html::submitButton("レシートをまとめて印刷",[
                'class'    => 'btn btn-warning',
                'title'    => 'レシートをまとめて印刷します',
                'name'     => 'target',
                'value'    => 'default',
                'onClick'  => "this.form.action='all-receipt'",
            ]) ?>
        </div>
    <?php } else { ?>
        <p>
            <?= Html::hiddenInput('basename', \backend\modules\casher\Module::getPrintBasename()) ?>
            <?= Html::submitButton("レシートをまとめて印刷",[
                'class'    => 'btn btn-warning',
                'title'    => 'レシートをまとめて印刷します',
                'name'     => 'target',
                'value'    => 'default',
                'onClick'  => "this.form.action='all-receipt'",
            ]) ?>
        </p>
    <?php } ?>
        <?php endif ?>
        </div>

    </div>

<?php $form->end() ?>

        <?php if(($branch_id == \common\models\Company::PKEY_GAIBUEVENT)) { ?>
        <p>
            ※売上ファイル（CSV）を下のフォームで選択し「送信」をクリックすると、データを抽出して登録します
        </p>
        <?php echo $this->render('upload', ['model'=>$csvModel]);?>
        <?php } ?>

    <p class="pull-right">
        <?= Html::a('過去全件',['/purchase/index','SearchPurchase[branch_id]' => $branch_id ],['class'=>'btn btn-default','title'=>'すべての売上を表示します']) ?>
    </p>

</div>

