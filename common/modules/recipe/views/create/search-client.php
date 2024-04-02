<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/create/search-client.php $
 * $Id: search-client.php 4003 2018-08-29 01:27:52Z mori $
 */

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use \common\models\Pref;

$this->params['body_id'] = 'Mypage';

$this->title = sprintf("クライアントの検索 | 新規作成 | 適用書 | %s", Yii::$app->name);
?>

<div class="cart-view">

  <div class="col-md-12">

  <?= $this->render('_tab') ?>

	<h2><span>クライアントの検索</span></h2>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'layout'  => '{items}',
    'caption' => 'クライアントを検索します',
    'columns' => [
        [
            'class'    => yii\grid\ActionColumn::className(),
            'template' => '{apply}',
            'buttons'  => [
                'apply'    => function ($url, $model, $key)
                {
                    $code = ($mc = $model->membercode) ? $mc->code : null;
                    if($model->customer_id)
                        return Html::a('✔', ['add','target'=>'client','code'=>$code],['class'=>'btn btn-xs btn-success','title'=>'このクライアントを指定する']);

                    return Html::a('移行', ['migrate-customer','code'=>$code],['class'=>'btn btn-xs btn-default','data'=>['confirm'=>sprintf('この人(%s)の移行手続を代行しますか？',$code)]]);
                },
            ],
        ],
        [
            'attribute' => 'code',
            'format'    => 'raw',
            'label'     => '豊受会員証NO',
            'value'     => function($data){
                if($parent = $data->parent)
                    $code = (($mc = $parent->membercode) ? $mc->code : null) . ' (家族)';
                else
                    $code = (($mc = $data->membercode) ? $mc->code : null) . ' (本人)   ' . Html::a(Html::tag('small','更新'), ['attach-membercode', 'id' => $data->customer_id], ['class' => 'btn btn-xs btn-default']);

                return $code;
            },
        ],
        [
            'attribute' => 'name',
            'value' => function($data){
                return implode(' ', [$data->name01, $data->name02]);
            }
        ],
        [
            'attribute' => 'pref_id',
            'filter'    => ArrayHelper::map(Pref::find()->all(),'pref_id','name'),
            'value'     => function($data){ return ($p = $data->pref) ? $p->name : null; },
        ],
        [
            'attribute' => '',
            'label'     => '編集',
            'format'    => 'raw',
            'value'     => function($data){ return Html::a('編集', ['update-customer', 'id' => $data->customer_id], ['class' => 'btn btn-default']); },
        ],
    ],
])?>

  </div>

<?php $form = \yii\bootstrap\ActiveForm::begin(['id'=>'tel-form']); ?>

    <p class="help-block">
        会員証NOを入力してください
    </p>

    <?= $form->field($searchModel, 'tel')->label(false)->textInput([
        'name'        => 'tel',
        'style'       => 'width:50%',
        'placeholder' => '0000000000',
    ]) ?>

    <p class="pull-left">
        <?= Html::submitbutton('検索',['class'=>'btn btn-success']) ?>
        <?= Html::a('新規登録', ['agreed'], ['class' => 'btn btn-default']) ?>
    </p>

<?php $form->end() ?>

</div>
