<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/facility/index.php $
 * $Id: index.php 4005 2018-08-30 08:09:23Z mori $
 *
 * $model \common\models\AgencyOffice
 */
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
?>

<div class="cart-view">

    <h1 class="mainTitle">マイページ</h1>
    <p class="mainLead">このページでは出店企業・団体からのご優待や特典リンクをご案内します。</p>

    <div class="col-md-3">
        <div class="Mypage-Nav">
            <div class="inner">
                <h3>Menu</h3>
                <?= Yii::$app->controller->nav->run() ?>
            </div>
        </div>
    </div>

    <div class="col-md-9">

        <h2><span>提携施設</span></h2>

        <p class="help-block">
            豊受モール出店企業と提携いただいている場合、施設の情報を入力すると公開されます。
        </p>

        <?php /*\yii\grid\GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $customer->getMemberships()
                                 ->active(),
                'pagination' => false,
            ]),
            'layout' => '{items}',
            'emptyText' => '提携はありません',
            'columns' => [
                [
                    'class' => \yii\grid\CheckboxColumn::className(),
                    'multiple' => false,
                    'checkboxOptions' => function(){ return ['checked' => 'checked','disabled'=>'disabled']; },
                ],
                [
                    'header'    => '提携施設',
                    'attribute' => 'membership.name',
                ],
            ],
        ])*/ ?>
    <?php foreach($models as $model){ ?>
        <?php if($model->isNewRecord): ?>
            <div class="help-block alert alert-warning">
            登録がありません
            </div>
        <?php else: ?>
        <?= \yii\widgets\DetailView::widget([
            'model' => $model,
            'attributes' => [
                'facility_id',
                'name',
                'email',
                'url',
                'title',
                'zip',
                'addr',
                'tel',
                'fax',
            ],
        ]) ?>

        <label><?= $model->getAttributeLabel('summary') ?></label>
        <p class="well" style="background-color:white">
            <?= nl2br($model->summary) ?>
        </p>

        <label><?= $model->getAttributeLabel('pub_date') ?></label>
        <p>
            <?php if($model->private): ?>
            <?= Html::tag('del', $model->pub_date) ?> 公開しない
            <?php else: ?>
            <?= Html::tag('span', $model->pub_date) ?>
            <?php endif ?>
        </p>
        <?php endif ?>

        <?php if(! $model->isNewRecord): ?>
            <?= Html::a('修正',['update', 'id' => $model->facility_id], ['class'=>'btn btn-success']) ?>
            <p>
            <hr>
        <?php endif ?>

    <?php } ?>
            <p><p>
            <?= Html::a('提携施設を追加',['create'],['class'=>'btn btn-primary']) ?>
    </div>

</div>

