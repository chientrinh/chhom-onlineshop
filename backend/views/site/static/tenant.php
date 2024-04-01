<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/site/static/tenant.php $
 * $Id: tenant.php 3269 2017-04-23 02:05:31Z naito $
 */
use \common\models\Company;
use \common\models\PurchaseStatus;
use \yii\helpers\Html;

$id = Yii::$app->user->identity->company_id;

$company = Company::findOne($id);
?>

<div>

    <div class="list-group col-md-2 col-sm-2">

        <p><strong><?= $company->name ?></strong></p>

        <div class="panel panel-default">

            <div class="panel-heading">
                注文管理
            </div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <?= Html::a('一覧',['casher/trose/index']) ?>
                    </li>
                </ul>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                商品管理
            </div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <?= Html::a('登録・変更',['product/index', 'company' => $id]) ?>
                    </li>
                    <li class="list-group-item">
                        <?= Html::a('表示名・表示順',['product-master/index','company_id' => $id]) ?>
                    </li>
                    <li class="list-group-item">
                        <?= Html::a('サブカテゴリー',['subcategory/map',   'company_id' => $id]) ?>
                    </li>
                </ul>
        </div>

    </div>

</div>
