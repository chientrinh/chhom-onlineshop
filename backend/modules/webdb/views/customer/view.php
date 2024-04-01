<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/webdb/views/customer/view.php $
 * $Id: view.php 1634 2015-10-11 01:55:10Z mori $
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var $this  yii\web\View
 * @var $db    string
 * @var $model common\models\Customer
 */

$this->title = "$model->name | $db 顧客 | ".Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => "$db 顧客", 'url' => ['index','db'=>$db]];
$this->params['breadcrumbs'][] = $model->name;

$customer = null;
if($membercode = \common\models\Membercode::find()->where([
    'migrate_id' => $model->customerid,
    'directive'  => Yii::$app->request->get('db'),
])->one())
    $customer = $membercode->customer;

if(preg_match('/webdb/',$db))
    $outerUrl = Html::a($db, sprintf('https://%s.homoeopathy.co.jp/index.php?m=search&out_html=customer_dsp&customerid=%d',
                    $db,
                    $model->customerid), [                            'class' => 'glyphicon glyphicon-log-out']);
else
    $outerUrl = null;
?>
<div class="customer-view">

    <div class="pull-right">
        <?= Html::a('', ['view','db'=>$db,'id'=>$model->customerid-1], ['class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-left','title'=>sprintf('前:%s',$model->customerid-1)]) ?>
        <?= Html::a('', ['view','db'=>$db,'id'=>$model->customerid+1], ['class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-right','title'=>sprintf('次:%s',$model->customerid+1)]) ?>
    </div>

    <h1><?= Html::encode($model->name) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'kana',
            [
                'attribute' => 'customerid',
                'format'    => 'raw',
                'value'     => ($membercode ? $membercode->migrate_id : null) . '&nbsp;' . $outerUrl,
            ],
            [
                'attribute' => 'address1',
                'value'     => $model->postnum . ' ' . $model->address1 . $model->address2 . $model->address3,
            ],
            [
                'attribute' => 'sex',
                'value'     => $model->sex ? $model->sex->name : null,
            ],
            [
                'attribute'=>'birth',
                'value'=> preg_match('/0000/', $model->birth)
                ? null
                : Yii::$app->formatter->asDate($model->birth, 'full'),
            ],
            'email',
            'tel',
        ],
    ]) ?>

    <?php if($customer): ?>
        <p class="help-block">
            移行は完了しています <?= Html::a('詳しく見る',['/customer/view','id'=>$customer->customer_id]) ?>
        </p>
        <p>
            <?= Html::a('もう一度同期させる',['/customer/migrate','from'=>Yii::$app->request->get('db'),'id'=>$model->customerid,],['class'=>'btn btn-default','title'=>'WEBDBからえびすへ、まだ移行できていない会員情報を移行します']) ?>
        </p>
    <?php else: ?>
        <div class="form-group">
            <?= Html::a('移行する',['/customer/migrate','from'=>Yii::$app->request->get('db'),'id'=>$model->customerid,],['class'=>'btn btn-warning','title'=>'WEBDBからえびすへ、会員情報を移行します。ご本人の同意を得たうえで操作してください']) ?>
        </div>
    <?php endif ?>

    <div>
        <?php $params = $model->migrateAttributes(); ?>
        <?php if(isset($params['memberships'])): ?>
            <?= \yii\grid\GridView::widget([
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'allModels' => $params['memberships'],
                    'pagination' => false,
                ]),
                'layout' => '{items}',
                'rowOptions' => function ($data, $key, $index, $grid) {
                    return (strtotime($data['expire_date']) < time()) ? ['class' => 'danger'] : [];
                },
                'columns' => [
                    [
                        'attribute' => 'membership_id',
                        'value'     => function($data,$key,$idx){ return \common\models\Membership::findOne($data['membership_id'])->name; },
                    ],
                    [
                    'attribute' => 'start_date',
                    ],
                    [
                    'attribute' => 'expire_date',
                    ],
                ],
            ]) ?>
    <?php endif ?>
    </div>

</div>
