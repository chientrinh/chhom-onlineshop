<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/default/view.php $
 * $Id: view.php 3607 2017-09-24 06:01:26Z naito $
 */

use \yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label' => "会員情報の確認・変更"];

?>

<div class="cart-view">
  <h1 class="mainTitle">マイページ</h1>
  <p class="mainLead">お客様ご本人の情報の閲覧・編集ができます。</p>
  <div class="col-md-3">
	<div class="Mypage-Nav">
	  <div class="inner">
		<h3>Menu</h3>
          <?= Yii::$app->controller->nav->run() ?>
	  </div>
	</div>
  </div>

  <div class="col-md-9">
	<h2><span>会員情報の確認・変更</span></h2>
	<p class="windowtext">ご本人情報を下記の内容で登録しています。<br>
	  変更する場合は、一番下の「編集する」ボタンをクリックしてください。</p>

	  <table summary="会員情報の確認・変更" id="FormTable" class="table table-bordered">
		<tr>
		  <th>
            <div><label><?= Html::activeLabel($model, 'name') ?></label></div>
          </th>
		  <td>
             <?= $model->name ?> (<?= $model->kana ?>)
          </td>
		</tr>

		<tr>
		  <th>
            <div><label>家族会員</label></div>
          </th>
		  <td>
          <?php if($model->children): ?>
             <?= \yii\widgets\ListView::widget([
                 'dataProvider' => new \yii\data\ArrayDataProvider([
                     'allModels' => $model->children,
                     'pagination' => false,
                     'sort'       => false,
                 ]),
                 'layout'   => '{items}',
                 'itemView' => function ($model, $key, $index, $widget)
                 {
                     return Html::tag('p',
                                      sprintf('%s (%s) ',
                                              $model->name,
                                              $model->kana)
                                      . Html::a('修正',['update','id'=>$model->customer_id],['class'=>'btn btn-xs btn-default'])
                     );
                 },
             ]) ?>
          <?php else: ?>
             (なし)
          <?php endif ?>
             <?= Html::a("<strong> + </strong>追加",['create','target'=>'child'],['class'=>'btn btn-success pull-right']) ?>
		  </td>
		</tr>

		<tr>
		  <th>
            <div><label><?= Html::activeLabel($model, 'addr') ?></label></div>
          </th>
		  <td>〒<?= $model->zip ?> <?= $model->addr ?></td>
		</tr>

		<tr>
		  <th>
            <div><label><?= Html::activeLabel($model, 'tel') ?></label></div>
          </th>
		  <td><?= $model->tel ?></td>
		</tr>

		<tr>
		  <th>
            <div><label><?= Html::activeLabel($model, 'sex') ?></label></div>
          </th>
		  <td><?= $model->sex ? $model->sex->name : '(指定なし)' ?></td>
		</tr>

		<tr>
		  <th>
            <div><label><?= Html::activeLabel($model, 'birth') ?></label></div>
          </th>
          <td>
              <?php if($model->birth && ! preg_match('/0000/',$model->birth)): ?>
              <?= date('Y年m月d日',strtotime($model->birth)) ?>
              <?php else: ?>
                  (指定なし)
              <?php endif ?>
          </td>
		</tr>

		<tr>
		  <th>
            <div><label><?= Html::activeLabel($model, 'email') ?></label></div>
          </th>
		  <td><?= $model->email ?></td>
		</tr>

		<tr>
		  <th>
            <div><label><?= Html::activeLabel($model, 'password') ?></label></div>
          </th>
		  <td>*******</td>
		</tr>

		<tr>
		  <th>
            <div><label><?= Html::activeLabel($model, 'subscribe') ?></label></div>
          </th>
		  <td>
              <?= ($subscribe = \common\models\Subscribe::findOne($model->subscribe)) ? $subscribe->name : "未指定" ?>
          </td>
		</tr>

	  </table>

	  <div class="form-group">
		<?= Html::a("編集する",['update'],['class'=>'btn btn-primary']) ?>
	  </div>

  </div>
</div>
