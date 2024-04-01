<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/sodan/index.php $
 * $Id: index.php 3851 2018-04-24 09:07:27Z mori $
 */

use \yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label'=>"健康相談の手続き"];

$customer = \common\models\Customer::findOne($model->client_id);
?>

<div class="col-md-12">

    <h2>
        <?= $model->name ?><small>さん</small>
    </h2>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                ダウンロード
            </div>
            <div class="panel-body">
                <p><?= Html::a('同意書', ['view', 'id' => 'agreement.pdf']) ?></p>
                <p><?= Html::a('質問票', ['view', 'id' => 'question.pdf']) ?></p>
                <p><?= Html::a('質問票(動物相談)', ['view', 'id' => 'animal.pdf']) ?></p>
                <p><?= Html::a('事前報告書', ['view', 'id' => 'report.pdf']) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                動画の視聴
            </div>
            <div class="panel-body">
                <p>下に表示されているロゴをクリックしてご視聴いただけます。また、本部センターにてDVDを無料でお配りしています。</p>
                <strong>「Zen ホメオパシー」 50分</strong>
                <p>
                <?= Html::a(Html::img('//www.youtube.com/yt/img/logo_1x.png',['alt'=>'https://youtu.be/ScF9PWKlKu4']),"//youtu.be/ScF9PWKlKu4",['title'=>'https//youtu.be/ScF9PWKlKu4']) ?>
                <?= Html::a('https://youtu.be/ScF9PWKlKu4','//youtu.be/ScF9PWKlKu4') ?>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-12" style="display:none;">アップロード</div>
    <div class="col-md-6" style="display:none;">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Html::label($model->getAttributeLabel('agreement')) ?>
            </div>
            <div class="panel-body">
                <?php if(! $model->agreement): ?>

                <?php $ff = new \common\models\FileForm() ?>
                <?php $form = \yii\bootstrap\ActiveForm::begin([
                    'id'     => 'form-agreement',
                    'action' => ['update','id'=>'agreement'],
                    'layout' => 'default',
                    'method' => 'post',
                    'options'=> ['enctype' => 'multipart/form-data'],
                ]); ?>
                <?= $form->field($ff, 'tgtFile')->label(false)->fileInput() ?>
                <?= Html::submitButton('登録',['class'=>'btn btn-primary']) ?>
                <?php $form->end() ?>

                <?php else: ?>
                    <p class="help-block">同意書はアップロード完了しました</p>
                <?php endif ?>
            </div>
        </div>
    </div>

    <?php $ff = new \common\models\FileForm() ?>
    <div class="col-md-6" style="display:none;">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Html::label($model->getAttributeLabel('questionnaires')) ?>
            </div>
            <div class="panel-body">
                <?php if($qnr = $model->questionnaires): ?>
                    <p class="help-block">質問票は<?= count($qnr) ?>件アップロード済みです</p>
                <?php endif ?>

                <?php $form = \yii\bootstrap\ActiveForm::begin([
                    'id'     => 'form-questionnaire',
                    'action' => ['update','id'=>'questionnaire'],
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

</div>
