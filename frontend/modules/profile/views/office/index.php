<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/office/index.php $
 * $Id: index.php 3604 2017-09-24 05:08:26Z naito $
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

        <h2><span>請求先情報</span></h2>

        <p class="help-block">
            出店企業と月締めでご契約いただいている場合、以下の請求先情報が請求先の宛先に記載されます。
        </p>

        <?php if($model->isNewRecord): ?>
            <div class="help-block alert alert-warning">
            登録がありません
            </div>
        <?php else: ?>
        <?= \yii\widgets\DetailView::widget([
            'model' => $model,
            'attributes' => [
                'company_name',
                'person_name',
                'zip',
                'addr',
                'tel',
                'fax',
            ],
        ]) ?>
        <?php endif ?>

        <?php if($model->isNewRecord): ?>

            <?= Html::a('作成',['create'],['class'=>'btn btn-success']) ?>

        <?php else: ?>

            <?= Html::a('修正',['update'],['class'=>'btn btn-success']) ?>

        <?php endif ?>

    </div>

</div>

