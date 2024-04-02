<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/default/view.php $
 * $Id: view.php 4040 2018-09-27 06:01:23Z mori $
 */

use \yii\helpers\Html;

$title = sprintf('%06d', $model->recipe_id);
$this->params['body_id']       = 'Mypage';
$this->params['breadcrumbs'][] = ['label'=> $title];

?>



<div class="cart-view">
    <div class="col-md-12">
    <p class="pull-right">
      <?= Html::a('履歴（一覧）に戻る', ['index'], ['class'=>'btn btn-info']) ?>
    </p>
    </div>
    <div class="col-md-12">

    <h2><span>適用書 : <?= $title ?></h2>



<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider([
'allModels' => $model->parentItems,
'pagination' => false,
        'sort' => [
            'attributes' => [
                'remedy' => [
                    'default' => SORT_DESC,
                ],
                'remedy' => [
                    'asc' => ['remedy.abbr' => SORT_ASC  ],
                    'desc'=> ['remedy.abbr' => SORT_DESC ],
                    'default' => SORT_ASC,
                ],
                'potency' => [
                    'asc' => ['potency.weight' => SORT_ASC  ],
                    'desc'=> ['potency.weight' => SORT_DESC ],
                    'default' => SORT_ASC,
                ],
                'vial' => [
                    'asc' => ['vial_id' => SORT_ASC  ],
                    'desc'=> ['vial_id' => SORT_DESC ],
                    'default' => SORT_ASC,
                ],
                'seq',
                'quantity',
            ],
            'defaultOrder' => ['seq' => SORT_ASC ],
        ],
    ]),
    'layout'  => '{items}{summary}',
    'summary' => '<p class="text-right">計 <strong>{totalCount}</strong> 品目</p>',
    'columns' => [
        [
            'class'=> '\yii\grid\SerialColumn',
        ],
        [
            'attribute' => 'name',
            'format'    => 'html',
            'value'     => function($data)
            {
                return nl2br($data->fullname);
            },
        ],
        [
            'attribute'=>'potency',
            'value'    => function($data)
            {
                if($data->potency)
                    return $data->potency->name;
            }
        ],
        [
            'attribute'=>'vial',
            'value'    => function($data)
            {
                if($data->vial)
                return $data->vial->name;
            }
        ],
        'quantity',
        [
            'attribute'=>'instruction',
            'value'    => function($data)
            {
                if($data->instruction)
                return $data->instruction->name;
            }
        ],
        'memo',
    ],
])?>

  <p class="form-group">
  <!-- recipe/create/update has not implement yet
  <?php if(! $model->isExpired() && (time() - strtotime($model->update_date)) < (30 * 60)): /* 30 分以内 */ ?>
       <?= Html::a('修正', ['update','id'=>$model->recipe_id],[
           'class'=>'btn btn-success',
           'title'=>'発行後、30 分以内なら修正できます',
       ])?>
  <?php else: ?>
    <?= Html::tag('span','修正できません', [
        'class'=>'btn btn-default',
        'title'=>'最終更新から 30 分が経過したか、クライアントが購入したか、すでに無効となっている場合、修正できません',
    ]) ?>
  <?php endif ?>
  -->
  </p>

  <?php if(! $model->isExpired()): ?>
      <p class="pull-left">
<!--
       <?php if ($model->publish_flg): ?>
            <?= Html::a('非公開にする', ['close', 'id' => $model->recipe_id], [
                'class' => 'btn btn-default',
                'data' => [
                    'confirm' => 'この適用書を顧客非公開にしますか',
                    'method'  => 'post',
               ],
           ]) ?>
        <?php else: ?>
            <?= Html::a('公開する', ['publish', 'id' => $model->recipe_id], [
                 'class' => 'btn btn-default',
                 'data' => [
                     'confirm' => 'この適用書を顧客に公開してよろしいですか。',
                     'method'  => 'post',
                ],
            ]) ?>
       <?php endif; ?>
-->
       <?= Html::a('無効にする', ['expire','id'=>$model->recipe_id],[
           'class'=>'btn btn-danger',
           'title'=>'有効期限を現在時刻に設定し、クライアントが購入できないようにします',
           'data'=>['confirm'=>sprintf('この適用書(%06d)をほんとうに無効にしますか？',$model->recipe_id)],
       ])?>       
      </p>
  <?php endif ?>
  <p class="pull-left" style="margin-left: 5px;">
  <?php $disabled = ($model->status === common\models\Recipe::STATUS_SOLD) ? 'disabled' : null; ?>
  <?= Html::a('編集', ['updateedit', 'id' => $model->recipe_id], ['class' => 'btn btn-primary ' . $disabled, 'title'=>'この適用書を編集します。']) ?>
  <?= Html::a('再作成', ['update', 'id' => $model->recipe_id], ['class' => 'btn btn-warning','title'=>'この適用書を元にして新たな適用書を作成します。']) ?>
  <?= Html::a('コピー', ['updatekeepsts', 'id' => $model->recipe_id], ['class' => 'btn btn-success','title'=>'この適用書を元にして新たな適用書を作成します。（この適用書のステータスは変更されません）']) ?>
  </p>
  <p class="pull-right">
  <?= Html::a('印刷', ['print','id'=>$model->recipe_id, 'format'=>'pdf'],['class'=>'btn btn-xl btn-warning','target'=>'_blank'])?>
  </p>

<?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'options' => ['class'=>'table table-condensed'],
    'attributes' => [
        'note',
        [
            'attribute' => 'client_id',
            'format'    => 'html',
            'value'     => Html::tag('strong',($c = $model->client) ? $c->name : ($model->manual_client_name ? $model->manual_client_name : null)),
        ],
        [
            'attribute' => 'homoeopath_id',
            'format'    => 'html',
            'value'     => Html::tag('strong',($h = $model->homoeopath) ? $h->homoeopathname : null),
        ],
        [
            'label'     => '状態',
            'attribute' => 'statusName',
            'format'    => 'html',
            'value'     => Html::tag('strong',$model->statusName),
        ],
        [
            'attribute' => 'create_date',
            'format'    => ['date', 'php:Y-m-d D H:i'],
        ],
        [
            'attribute' => 'expire_date',
            'format'    => ['date', 'php:Y-m-d D H:i'],
        ],
    ],
]) ?>

<?php if($model->client_id): ?>
  <p class="pull-right">
      <?= Html::a('サポート注文', ['/cart/recipe/proxy','id'=>$model->recipe_id],[
      'class'=>'btn btn-xl btn-success' . ($model->isExpired() ? ' disabled' : ''),
      'title'=>'クライアントに成り代わって注文します'
  ])?>
  </p>
<?php endif ?>
</div>

</div>
