<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/site/index.php $
 * $Id: index.php 4105 2019-01-31 03:02:24Z kawai $
 *
 * @var $this yii\web\View
 */
use \yii\helpers\Html;
use \common\models\Purchase;

$this->params['breadcrumbs'][] = ['label'=>'玄関'];
?>

<div class="site-index">

    <div class="well text-center">
        本日は <strong><?= date('Y 年 m 月 d 日 D') ?> </strong>です
    </div>

    <div class="body-content">

        <div class="row">

            <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                    <span class="glyphicon glyphicon-cutlery" style="font-size: 5em"></span>
                    <div class="caption">
                        <h3><?= Html::a('実店舗',['/casher/default']) ?></h3>
                        <?php $query = Purchase::find()->where(['like','create_date',date('Y-m-d')])->andWhere(['paid'=>1])->active(); ?>
                        <p>売上 <strong><?= $query->count() ?></strong> 件</p>
                        <p>
                            <?= Html::a('レジ',['/casher/default/create'],['role'=>'button','class'=>"btn btn-default"]) ?>
                            <?= Html::a('売上',['/casher/default/index'],['role'=>'button','class'=>"btn btn-default"]) ?>
                            <?= Html::a('店間',['/casher/transfer/create'],['role'=>'button','class'=>"btn btn-default"]) ?>
                            <?= Html::a('集計',['/casher/default/stat'],['role'=>'button','class'=>"btn btn-default"]) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                    <span class="glyphicon glyphicon-globe" style="font-size: 5em"></span>
                    <div class="caption">
                        <h3><?= Html::a('熱海発送センター',['/casher/atami/index']) ?></h3>
                        <?php $query = Purchase::find()->where(['and',['like','create_date',date('Y-m-d')],['branch_id'=>6]])->active(); ?>
                        <p>受注 <strong><?= $query->count() ?></strong> 件</p>
                        <p>
                            <?= Html::a('売上',['/casher/atami/index'],['role'=>'button','class'=>"btn btn-default"]) ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                    <span class="glyphicon glyphicon-tree-conifer" style="font-size: 5em"></span>
                    <div class="caption">
                        <h3><?= Html::a('六本松発送所',['/casher/ropponmatsu/index']) ?></h3>
                        <?php $query = Purchase::find()->where(['and',['like','create_date',date('Y-m-d')],['branch_id'=>5]])->active(); ?>
                        <p>受注 <strong><?= $query->count() ?></strong> 件</p>
                        <p>
                            <?= Html::a('売上',['/casher/ropponmatsu/index'],['role'=>'button','class'=>"btn btn-default"]) ?>
                            <?= Html::a('店間',['/casher/ropponmatsu/transfer'],['role'=>'button','class'=>"btn btn-default"]) ?>
                            <?= Html::a('野菜',['/vegetable'],['role'=>'button','class'=>"btn btn-default"]) ?>
                            <?= Html::a('在庫',['/casher/stock/index?id='.\common\models\Branch::PKEY_ROPPONMATSU],['role'=>'button','class'=>"btn btn-default"]) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                    <span class="glyphicon glyphicon-lamp" style="font-size: 5em"></span>
                    <div class="caption">
                        <h3><?= Html::a('健康相談',['/sodan']) ?></h3>
                        <?php $query = \common\models\sodan\Interview::find()->where(['itv_date'=>date('Y-m-d')])->andWhere(['>','client_id',0])->active(); ?>
                        <p>相談会 <strong><?= $query->count() ?></strong> 件</p>
                        <p>
                            <?= Html::a('予約',['/sodan/client'],['role'=>'button','class'=>"btn btn-default"]) ?>
                            <?= Html::a('相談会',['/sodan/interview?time=0'],['role'=>'button','class'=>"btn btn-default"]) ?>
                            <?= Html::a('お会計',['/sodan/interview?time=0&bill=on'],['role'=>'button','class'=>"btn btn-default"]) ?>
                            <?= Html::a('本部クライアント',['/sodan/client'],['role'=>'button','class'=>"btn btn-default"]) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                    <span class="glyphicon glyphicon-user" style="font-size: 5em"></span>
                    <div class="caption">
                        <h3><?= Html::a('顧客',['/customer']) ?></h3>
                        <?php $query = \common\models\Customer::find()->active(); ?>
                        <p>総計 <strong><?= number_format($query->count()) ?></strong> 名</p>
                        <p>
                            <?= Html::a('一覧',['/customer/index'],['role'=>'button','class'=>"btn btn-default"]) ?>
                            <?= Html::a('所属',['/membership'],['role'=>'button','class'=>"btn btn-default"]) ?>
                            <?= Html::a('検索', ['/customer/index'], [
                                'role'  =>'button',
                                'class' => 'btn btn-default',
                                'data'  => ['method' => 'post']
                            ]) ?>
                            <?= Html::a('HJ代理店割引率', ['/agency-rank'], ['role' => 'button', 'class' => "btn btn-default"]) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                    <span class="glyphicon glyphicon-apple" style="font-size: 5em"></span>
                    <div class="caption">
                        <h3>商品</h3>
                        <p>　</p>
                        <p>
                            <?= Html::a('商品',['/product/index'],['role'=>'button','class'=>"btn btn-default"]) ?>
                            <?= Html::a('レメディー・MT・FE',['/remedy'],['role'=>'button','class'=>"btn btn-default"]) ?>
                        </p>
                    </div>
                </div>
            </div>

　　　　　　<div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                    <span class="glyphicon glyphicon-euro" style="font-size: 5em"></span>
                    <div class="caption">
                        <h3>経理</h3>
                        <p>　</p>
                        <p>
                            <?php //Html::a('統計',['/purchase-survey/index'],['role'=>'button','class'=>"btn btn-default"]) ?>
                            <?= Html::a('請求',['/invoice/admin'],['role'=>'button','class'=>"btn btn-default"]) ?>
                            <?= Html::a('入金',['/invoice/finance'],['role'=>'button','class'=>"btn btn-default"]) ?>
                            <?= Html::a('販売管理', ['/summary/index'], ['role' => 'button', 'class' => "btn btn-default"]) ?>
                            <?= Html::a('精算書', ['/summary/payoff'], ['role' => 'button', 'class' => "btn btn-default"]) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
