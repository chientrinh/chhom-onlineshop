<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer/view.php $
 * $Id: view.php 4122 2019-03-13 07:20:35Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\Customer
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\ChangeLog;
use common\models\Company;
use common\models\Facility;
use common\models\Membership;
use common\models\InvoiceStatus;

$this->params['breadcrumbs'][] = ['label' => html_entity_decode($model->name) ];

$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels) . ' | ' . Yii::$app->name;

$migrate_id = ArrayHelper::getValue($model,'membercode.migrate_id');

// prepare link to webdb{20,18}
$outsideUrl = null;
$directive  = ArrayHelper::getValue($model, 'membercode.directive');
if(preg_match('/^webdb/', $directive))
    $outsideUrl = Html::a(sprintf(' (%s) ', $directive), sprintf('https://%s.homoeopathy.co.jp/index.php?m=search&out_html=customer_dsp&customerid=%s',$directive,$migrate_id),['target'=>'webdb']);
unset($directive);

?>
<div class="customer-view">

    <div class="pull-right">
        <?= Html::a("修正", ['update', 'id' => $model->customer_id], ['class' => 'btn btn-primary']) ?>

        <?php if($model->prev): ?>
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-left']), ['view','id'=>$model->prev->customer_id], ['class'=>'btn btn-xs btn-default']) ?>
        <?php endif ?>

        <?php if($model->next): ?>
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-right']), ['view','id'=>$model->next->customer_id], ['class'=>'btn btn-xs btn-default','title'=>'次']) ?>
        <?php endif ?>
    </div>

    <h1><?= $model->name ?></h1>
    <?php if($model->isExpired()): ?>
        <p class="alert alert-danger">
            この顧客は無効になりました。復活させるにはシステム担当者に以下のコマンドを実行するよう依頼してください。<br>
            <code>yii customer/activate <?= $model->customer_id ?></code>
        </p>
    <?php endif ?>
    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-condensed table-striped table-bordered'],
        'attributes' => [
            'customer_id',
            'kana',
            [
                'attribute'=> 'grade',
                'format'   => 'html',
                'value'    => Html::tag('strong',$model->grade->longname),
            ],
            'point:integer',
            [
                'attribute'=>'code',
                'format'   =>'html',
                'value'    => Html::a(Html::tag('strong',$model->code,['style'=>'font-family:fixed']),[
                              '/membercode/view','id'=>$model->code,
                              ],['class'=>'btn-default'])
                            . ((($c = $model->membercode) && ($c->code == $model->code) && $c->isVirtual()) ? Html::tag('strong','&nbsp;会員証は未発行です',['class'=>'text-danger']) : null) ,
            ],
            [
                'label'    => '旧ID',
                'format'   => 'raw',
                'value'    => $this->render('_mcode',['model'=>$model]),
            ],
            'fulladdress',
            'tel',
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
            [
                'attribute' => 'subscribe',
                'value' => ($subscribe = \common\models\Subscribe::findOne($model->subscribe)) ? $subscribe->name : '未指定',
            ],
            [
                'attribute' => 'create_date',
                'format' => ['date','php:Y-m-d H:i'],
            ],
            [
                'attribute' => 'expire_date',
                'format'    => 'html',
                'value'     => Html::tag('span', Yii::$app->formatter->asDate($model->expire_date,'php:Y-m-d H:i'), $model->isExpired() ? ['class'=>'alert-text alert-danger'] : []),
            ],
            'ysdAccount.credit_limit:currency',
        ],
    ]) ?>

    <?= Html::errorSummary($model,['class'=>'alert alert-warning']) ?>

    <?php if($model->office): ?>
    <h3><small>代理店・請求先</small></h3>
    <?= \yii\widgets\DetailView::widget([
        'model'      => $model->office,
        'options'    => ['class' => 'table table-condensed table-striped table-bordered'],
        'attributes' => [
            [
                'attribute' => 'company_name',
                'format'    => 'raw',
                'value'     => $model->office->company_name . Html::a('修正',['agency-office/update','id'=>$model->customer_id, 'from_customer' => "1"],['class'=>'pull-right'])
            ],
            'person_name',
            'addr',
            'tel',
            'fax',
            [
                'attribute' => 'payment_date',
                'format'    => 'raw',
                'value'     => $model->office->getPaymentDays($model->office->payment_date),
            ],
    ],
    ]) ?>
    <?php elseif($model->isAgency()): ?>
        <?= Html::a('代理店・請求先が未定義です',['agency-office/create','id'=>$model->customer_id]) ?>
    <?php endif ?>

    <?php if($model->agencyRatings): ?>
    <h3><small>代理店・割引率</small></h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getAgencyRatings(),
            'sort'  => [
                'defaultOrder'=> ['end_date'=>SORT_DESC]
            ],
            'pagination' => [
                'pageSize' => 5,
            ],
        ]),
        'tableOptions' => ['class' => 'table table-condensed table-striped table-bordered'],
        'layout'       => '{items}{pager}',
        'rowOptions' => function ($data, $key, $index, $grid) {
            if(strtotime($data->end_date . ' 23:59:59') < time())
                return ['class'=>'danger'];
            if(time() < strtotime($data->start_date))
                return ['class'=>'warning'];
        },
        'columns'      => [
            [
                'attribute' => 'company_id',
                'format'    => 'html',
                'value'     => function($data){ return $data->company->key; },
                'contentOptions' => ['class'=>'text-upper'],
            ],
            'discount_rate',
            'start_date',
            'end_date',
            [
                'label' => '',
                'format'=> 'raw',
                'value' => function($data){ return Html::a('修正',['/agency-rating/update','id'=>$data->rating_id]); },
                'header' => $model->isExpired() ? '' : Html::a("+", ['/agency-rating/create', 'id' => $model->customer_id], ['class' => 'btn btn-xs btn-default','title'=>'割引率を追加します']),
            ],
    ],
    ]) ?>
    <?php elseif($model->isAgencyOf(Company::PKEY_HE)): ?>
        <?= Html::a('割引率が未定義です',['agency-rating/create','id'=>$model->customer_id]) ?>
    <?php endif ?>

    <?php if($facilities = Facility::find()->andWhere(['customer_id' => $model->customer_id])->all()): ?>
    <h3><small>提携施設 (<?php print(count($facilities))?>件登録)</small></h3>
    <p>
    <?php foreach( $facilities as $facility) { ?>
        <?= $facility->name.' '.Html::a('修正',['facility/update','id'=>$facility->facility_id],['class'=>'btn btn-xs btn-default']).' '.Html::a('削除',['facility/delete','id'=>$facility->facility_id],['class'=>'btn btn-xs btn-danger','data' => [
                    'confirm' => '本当に削除していいですか？',
                ]]) ?>
    <?= \yii\widgets\DetailView::widget([
        'model' => $facility,
        'options' => ['class'=>'table table-condensed'],
        'attributes' => [
            'title',
            'zip',
            'addr',
            'tel',
            'fax',
            'pub_date',
            'private:boolean',
        ],
    ]) ?>
    <?php } ?>
    <?= Html::a('提携施設を追加',['facility/create','id'=>$model->customer_id], ['class'=>'btn btn-xs btn-primary']) ?>
    <?php elseif($model->isMemberOf([Membership::PKEY_HOMOEOPATH,
                                     Membership::PKEY_JPHMA_ANIMAL,
                                     Membership::PKEY_JPHMA_FH,
                                     Membership::PKEY_JPHMA_IC,
                                     Membership::PKEY_JPHMA_TECHNICAL,
                                     Membership::PKEY_JPHMA_ZEN,
                                     Membership::PKEY_AGENCY_HE,
                                     Membership::PKEY_AGENCY_HJ_A,
                                     Membership::PKEY_AGENCY_HJ_B,
                                     Membership::PKEY_AGENCY_HP,
                                     Membership::PKEY_JPHF_FARMER,
    ])): ?>
    <?= Html::a('提携施設が未定義です',['facility/create','id'=>$model->customer_id]) ?>
    <?php endif ?>

    <?php if($model->hjAgencyRank): ?>
        <h3><small>HJ代理店割引率</small></h3>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $model->getHjAgencyRank(),
                'sort'  => [
                    'defaultOrder'=> ['expire_date' => SORT_DESC]
                ],
                'pagination' => [
                    'pageSize' => 5,
                ],
            ]),
            'tableOptions' => ['class' => 'table table-condensed table-striped table-bordered'],
            'layout'       => '{items}{pager}',
            'rowOptions' => function ($data) {
                if(strtotime($data->expire_date) < time())
                    return ['class'=>'danger'];
                if(time() < strtotime($data->start_date))
                    return ['class' => 'warning'];
            },
            'columns'      => [
                [
                    'attribute' => 'rank_id',
                    'format'    => 'html',
                    'value'     => function($data){ return $data->rank->name; },
                ],
                [
                    'attribute' => 'liquor_rate',
                    'format'    => 'html',
                    'value'     => function($data){ return $data->rank->liquor_rate . '%'; },
                ],
                [
                    'attribute' => 'goods_rate',
                    'format'    => 'html',
                    'value'     => function($data){ return $data->rank->goods_rate . '%'; },
                ],
                [
                    'attribute' => 'remedy_rate',
                    'format'    => 'html',
                    'value'     => function($data){ return $data->rank->remedy_rate . '%'; },
                ],
                [
                    'attribute' => 'other_rate',
                    'format'    => 'html',
                    'value'     => function($data){ return $data->rank->other_rate . '%'; },
                ],
                'start_date',
                'expire_date',
                [
                    'label' => '',
                    'format'=> 'raw',
                    'value' => function($data){ return Html::a('修正',['/customer-agency-rank/update','id' => $data->id]); },
                    'header' => $model->isExpired() ? '' : Html::a("+", ['/customer-agency-rank/create', 'customer_id' => $model->customer_id], ['class' => 'btn btn-xs btn-default','title' => '割引率を追加します']),
                ],
        ],
        ]) ?>
    <?php elseif($model->isAgencyOf(Company::PKEY_HJ)): ?>
        <?= Html::a('HJ割引率が未定義です',['customer-agency-rank/create','customer_id' => $model->customer_id]) ?>
    <?php endif ?>

    <h3><small>所属</small>
    <?= Html::a('とらのこ会',['/member/toranoko/view','id'=>$model->customer_id],['class'=>'btn btn-xs btn-default pull-right']) ?>
    </h3>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'      => $model->getMemberships(true)->orderBy(['expire_date'=>SORT_DESC]),
            'pagination' => false,
            'sort'       => new \yii\data\Sort([
                'defaultOrder' => 'start_date',
            ]),
        ]),
        'layout'       => '{items}',
        'tableOptions' => ['class'=>'table table-condensed table-striped table-bordered'],
        'emptyText'    => 'まだありません &nbsp;'.Html::a("+", ['/customer-membership/create', 'customer_id' => $model->customer_id], ['class' => 'btn btn-xs btn-default','title'=>'所属を追加します']),
        'showOnEmpty' => false,
        'rowOptions' => function ($data, $key, $index, $grid)
        {
            if($data->isExpired())
                return ['class' => 'danger'];
            if(time() < strtotime($data->start_date)) // is future
                return ['class' => 'info'];
        },
        'columns' => [
            'label',
            [
                'attribute'=> 'company_id',
                'label'    => "所属",
                'value'    => function($data){return $data->membership->company->name; },
            ],
            'start_date',
            'expire_date',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model, $key) { return Html::a('修正', ['/customer-membership/update','id'=>$model->cmembership_id]); },
                ],
                'header' => $model->isExpired() ? '' : Html::a("+", ['/customer-membership/create', 'customer_id' => $model->customer_id], ['class' => 'btn btn-xs btn-default','title'=>'所属を追加します']),
            ],
        ],
        ]); ?>

<?php if ($model->parent ): ?>
    <h3><small>親会員</small></h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => [$model->parent],
            'pagination' => false,
        ]),
        'layout'       => '{items}',
        'tableOptions' => ['class'=>'table table-condensed table-striped table-bordered'],
        'columns' => [
            [
                'attribute'=>'name',
                'format'   =>'html',
                'value'    =>function($data){return Html::a($data->name, ['view','id'=>$data->customer_id]); },
            ],
            'sex.name',
            'birth:date',
        ],
        ]); ?>
<?php else: ?>
    <h3><small>
        家族会員
        <div class="pull-right">
        <?= Html::a('親子統合',['adapt','id'=>$model->customer_id],['class'=>'btn btn-xs btn-default','title'=>'既存の会員を親として、この人を家族会員にします']) ?>
        </div>
    </small></h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getChildren(),
            'pagination' => false,
        ]),
        'layout'       => '{items}',
        'tableOptions' => ['class'=>'table table-condensed table-striped table-bordered'],
        'showOnEmpty' => false,
        'emptyText' => 'まだありません &nbsp;'.Html::a('+',['create','parent'=>$model->customer_id],['class'=>'btn btn-xs btn-default']),
        'columns' => [
            [
                'attribute'=>'name',
                'format'   =>'html',
                'value'    => function($data){return Html::a($data->name, ['view','id'=>$data->customer_id]); },
            ],
            'kana',
            [
                'attribute' => 'sex',
                'value'     => function($data){ if($data->sex) return $data->sex->name; },
            ],
            'birth:date',
            'age',
            [
                'header'   => Html::a('+',['create','parent'=>$model->customer_id],['class'=>'btn btn-xs btn-default','title'=>'家族を追加します']),
            ]
        ],
        ]); ?>
<?php endif ?>

    <div class="col-md-12">
    <h3><small>付記</small></h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getInfos(),
            'pagination'=>[
                'defaultPageSize' => 5,
                'pageParam'       => 'pi', // pager for `info`
            ],
            'sort' => ['defaultOrder'=>['weight_id'=>SORT_DESC,'update_date'=>SORT_DESC]],
        ]),
        'layout'       => '{items}{pager}'.Html::a('もっと見る',['/customer-info/index','CustomerInfo[customer_id]'=>$model->customer_id],['class'=>'pull-right btn btn-default']),
        'tableOptions' => ['class'=>'table table-condensed table-striped table-bordered'],
        'emptyText'    => 'まだありません &nbsp;'.Html::a('+',['/customer-info/create','customer_id'=>$model->customer_id],['class'=>'btn btn-xs btn-default','title'=>'付記を追加します']),
        'showOnEmpty' => false,
        'columns' => [
            [
                'attribute' => 'weight_id',
                'value'     => function($data){ return ($w = $data->weight) ? $w->name : null; },
                'contentOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'content',
                'contentOptions' => ['class' => 'col-md-6'],
            ],
            'update_date',
            'updator.name',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model, $key) { return Html::a('修正', ['/customer-info/update','id'=>$model->cinfo_id]); },
                ],
                'header' => $model->isExpired() ? '' : Html::a("+", ['/customer-info/create', 'customer_id' => $model->customer_id], ['class' => 'btn btn-xs btn-default','title'=>'付記を追加します']),
            ],
        ],
        ]); ?>
    </div>

    <div class="col-md-12">
    <h3><small>住所録</small>
        <?= Html::a('+',['/customer-addrbook/create','id'=>$model->customer_id],['class'=>'btn btn-xs btn-default']) ?>
    </h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getAddrbooks(),
            'sort'  => ['defaultOrder'=>['update_date'=>SORT_DESC]],
        ]),
        'layout'       => '{items}',
        'tableOptions' => ['class'=>'table table-condensed table-striped table-bordered'],
        'columns' => [
            'id',
            'name',
            'kana',
            'zip',
            'addr',
            'tel',
            'update_date:date',
            [
                'class'    => yii\grid\ActionColumn::className(),
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model, $key) { return Html::a('修正', ['/customer-addrbook/update','id'=>$model->id]); },
                ],
            ],
        ],
    ]); ?>
    </div>

    <div class="col-md-12">
    <h3>
        <small>
            ご注文の履歴
            <?= Html::a('+',['/casher/default/apply','target'=>'customer','id'=>$model->customer_id],[
'class'=>'btn btn-xs btn-default','title'=>'この人をお客様としてレジを開きます']) ?>
        </small>
    </h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getPurchases(),
            'pagination'=> [
                'pageSize'  => 10,
                'pageParam' => 'p0-page'
            ],
            'sort' => [
                'attributes' => ['purchase_id'],
                'sortParam' => 'p0-sort',
                'defaultOrder' => ['purchase_id'=>SORT_DESC],
            ],
        ]),
        'layout'  => '{items}{pager}{summary}',
        'tableOptions'   => ['class'=>'table table-condensed table-striped'],
        'summaryOptions' => ['class'=>'small text-right pull-right'],
        'emptyText' => 'まだありません',
        'showOnEmpty' => false,
        'columns' => [
            [
                'attribute' => 'purchase_id',
                'format'    => 'html',
                'value'     => function($model){ return Html::a(sprintf('%06d',$model->purchase_id), ['/purchase/view', 'id'=>$model->purchase_id]); },
            ],
            [
                'attribute' => 'create_date',
                'format' => ['date','php:Y-m-d H:i'],
            ],
            [
                'attribute' => 'branch_id',
                'format'    => 'html',
                'value'     => function($data){ return ($b = $data->branch) ? $b->name : null; },
            ],
            [
                'attribute' => 'payment_id',
                'format'    => 'html',
                'value'     => function($data){ return ($p = $data->payment) ? $p->name : null; },
            ],
            [
                'attribute' => 'total_charge',
                'format'    => 'currency',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'point_consume',
                'format'    => 'currency',
                'value'     => function($data){ return (0 - $data->point_consume); },
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'point_given',
                'format'    => 'integer',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'status',
                'format'    => 'html',
                'value'     => function($data){ return ArrayHelper::getValue($data,'purchaseStatus.name'); },
            ],
        ],
        ]); ?>
    </div>

    <?php if($model->getCommissions()->exists()): ?>
    <div class="col-md-12">
    <h3>
        <small>
            手数料
            <small>販売店への手数料・本部ホメオパスへの報酬</small>
        </small>
    </h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider'=> new \yii\data\ActiveDataProvider([
            'query' => $model->getCommissions(),
            'pagination'=> [
                'pageSize'  => 10,
                'pageParam' => 'p3-page'
            ],
            'sort' => [
                'defaultOrder' => ['purchase_id'=>SORT_DESC],
            ],
        ]),
        'tableOptions'   => ['class'=>'table table-condensed table-striped'],
        'columns' => [
            [
                'attribute' => 'purchase_id',
                'value'     => function($data){ return sprintf('%06d', $data->purchase_id); },
            ],
            [
                'attribute' => 'commision_id',
                'format'    => 'html',
                'value'     => function($data){
                    return Html::a(sprintf('%06d',$data->commision_id), ['/commission/view','id'=>$data->commision_id]);
                },
            ],
            'company.name',
            'purchase.note',
            [
                'attribute' => 'fee',
                'format'    => 'currency',
                'contentOptions' => ['class'=>'text-right'],
            ],
        ],
    ]) ?>
    </div>
    <?php endif ?>

    <div class="col-md-12">
    <h3>
        <small>
            ポイント付与の履歴
            <small>　ポイントを受けとった履歴</small>
        </small>
    </h3>
    <?= $this->render('_pointing',[
        'dataProvider'=> new \yii\data\ActiveDataProvider([
            'query' => $model->getPointings(),
            'pagination'=> [
                'pageSize'  => 10,
                'pageParam' => 'p1-page'
            ],
            'sort' => [
                'attributes' => ['pointing_id'],
                'sortParam' => 'p1-sort',
                'defaultOrder' => ['pointing_id'=>SORT_DESC],
            ],
        ]),
    ]) ?>
    </div>

    <?php if($model->getPointings(true)->exists()): ?>
    <div class="col-md-12">
    <h3>
        <small>
            販売店・取扱所様専用売上
            <small>　ポイントを付与した履歴</small>
        </small>
    </h3>
    <?= $this->render('_pointing',[
        'dataProvider'=> new \yii\data\ActiveDataProvider([
            'query' => $model->getPointings(true)
                             ->andWhere(['not',['customer_id'=>$model->customer_id]]),
            'pagination'=> [
                'pageSize'  => 10,
                'pageParam' => 'p2-page'
            ],
            'sort' => [
                'attributes' => ['pointing_id'],
                'sortParam' => 'p2-sort',
                'defaultOrder' => ['pointing_id'=>SORT_DESC],
            ],
        ]),
    ]) ?>
    </div>
    <?php endif ?>

    <div class="col-md-12">
    <h3>
        <small>
            請求書の履歴
            <small>代理店・銀行振込のお買い物に適用されます</small>
        </small>
    </h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider'=> new \yii\data\ActiveDataProvider([
            'query' => $model->getInvoices(false),
            'pagination'=> [
                'pageSize'  => 10,
                'pageParam' => 'i-page'
            ],
            'sort' => [
                'sortParam' => 'i-sort',
                'defaultOrder' => ['invoice_id' => SORT_DESC],
            ]
        ]),
        'layout'         => '{items}{pager}{summary}',
        'tableOptions'   => ['class'=>'table table-condensed table-striped'],
        'summaryOptions' => ['class'=>'small text-right pull-right'],
        'emptyText'      => 'まだありません',
        'showOnEmpty'    => false,
        'columns' => [
            [
                'attribute' => 'invoice_id',
                'format'    => 'html',
                'value'     => function($data){ return Html::a(sprintf('%06d',$data->invoice_id),['/invoice/admin/view','id'=>$data->invoice_id]); },
            ],
            [
                'attribute' => 'target_date',
                'value'     => function($data){ return sprintf('%04d-%02d', $data->year, $data->month); },
            ],
            [
                'attribute' => 'due_total',
                'format'    => 'currency',
                'contentOptions' => ['class'=>'text-right','style'=>'font-weight:bold'],
            ],
            [
                'attribute' => 'status',
                'contentOptions' => ['class'=>'text-right','style'=>'font-weight:bold'],
                'value'     => function($data) use($labels)
                {
                    return \yii\helpers\ArrayHelper::getValue([InvoiceStatus::PKEY_VOID => '破棄', InvoiceStatus::PKEY_ACTIVE => '入金待ち', InvoiceStatus::PKEY_PAID => '入金済み', InvoiceStatus::PKEY_FORWARDED => '繰り越し' ], $data->status);
                },
            ],
            'update_date:datetime',
            [
                'attribute' => 'updated_by',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->updator->name01, ['/staff/view','id'=>$data->updated_by]); },
            ],
        ],
    ]) ?>
    </div>

    <div class="col-md-12">
    <h3><small>適用書 <small>認定ホメオパスから発行してもらった履歴</small></small>
            <?= Html::a('+',['/recipe/create/add','target'=>'client','code'=>$model->code],['class'=>'btn btn-xs btn-default','title'=>'この人をクライアントとして適用書を作成します']) ?>
</h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getRecipes(),
            'sort'       => new \yii\data\Sort([
                'defaultOrder' => ['recipe_id' => SORT_DESC],
            ]),
        ]),
        'layout'  => '{summary}{items}{pager}',
        'tableOptions'   => ['class'=>'table table-condensed table-striped'],
        'emptyText' => 'まだありません',
        'showOnEmpty' => false,
        'columns' => [
            [
                'attribute' => 'recipe_id',
                'format'    => 'html',
                'value'     => function($model){ return Html::a(sprintf('%06d',$model->recipe_id), ['/recipe/admin/view', 'id'=>$model->recipe_id]); },
            ],
            [
                'attribute' => 'create_date',
                'format'    => ['date','php:Y-m-d D'],
            ],
            [
                'attribute' => 'homoeopath_id',
                'format'    => 'html',
                'value'     => function($data){ if($data->homoeopath) return $data->homoeopath->homoeopathname; },
            ],
            [
                'attribute' => 'status',
                'format'    => 'html',
                'value'     => function($data){ return $data->statusName; },
            ],
            [
                'attribute' => 'update_date',
                'format'    => ['date','php:Y-m-d D'],
            ],
        ],
        ]); ?>
    </div>

    <?php if($model->isHomoeopath() || ($recipes = $model->getRecipes(true)->count())): ?>
    <div class="col-md-12">
    <h3>
        <small>適用書
        <small>自分が発行した履歴</small>
        </small>
        <?= Html::a('+',['/recipe/create/add','target'=>'homoeopath','id'=>$model->customer_id],['class'=>'btn btn-xs btn-default','title'=>'この人をホメオパスとして適用書を作成します']) ?>
    </h3>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getRecipes(true),
            'sort'       => new \yii\data\Sort([
                'defaultOrder' => ['recipe_id' => SORT_DESC],
            ]),
        ]),
        'layout'  => '{summary}{items}{pager}',
        'tableOptions'   => ['class'=>'table table-condensed table-striped'],
        'emptyText' => 'まだありません',
        'showOnEmpty' => false,
        'columns' => [
            [
                'attribute' => 'recipe_id',
                'format'    => 'html',
                'value'     => function($model){ return Html::a(sprintf('%06d',$model->recipe_id), ['/recipe/admin/view', 'id'=>$model->recipe_id]); },
            ],
            [
                'attribute' => 'create_date',
                'format'    => ['date','php:Y-m-d D'],
            ],
            [
                'attribute' => 'client_id',
                'format'    => 'html',
                'value'     => function($model){
                    if(! $model->client_id)
                        return '(指定なし)';

                    if($model->client->kana)
                        $kana = $model->client->kana;
                    else
                        $kana = '(未登録)';

                    return Html::a($kana, ['/customer/view','id'=>$model->client_id]);
                },
            ],
            [
                'attribute' => 'status',
                'format'    => 'html',
                'value'     => function($data){ return $data->statusName; },
            ],
            [
                'attribute' => 'update_date',
                'format'    => ['date','php:Y-m-d D'],
            ],
        ],
    ]); ?>
    </div>
    <?php endif ?>

    <div class="col-md-12">
    <h3><small>相談会 <small>本部ホメオパシーセンターで予約した履歴</small></small>
            <?= Html::a('+',['/sodan/client/book','id'=>$model->customer_id],['class'=>'btn btn-xs btn-default','title'=>'この人をクライアントとして相談会を予約します']) ?>
</h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getSodans(),
        ]),
        'layout'  => '{items}{pager}',
        'tableOptions'   => ['class'=>'table table-condensed table-striped'],
        'emptyText' => 'まだありません',
        'showOnEmpty' => false,
        'columns' => [
            [
                'attribute' => 'itv_id',
                'format'    => 'html',
                'value'     => function($data){ return Html::a(sprintf('%06d',$data->itv_id), ['/sodan/interview/view', 'id'=>$data->itv_id]); },
            ],
            [
                'attribute' => 'itv_date',
                'format'    => ['date','php:Y-m-d D'],
            ],
            [
                'attribute' => 'homoeopath_id',
                'format'    => 'html',
                'value'     => function($data){ if($data->homoeopath) return $data->homoeopath->homoeopathname; },
            ],
            [
                'attribute' => 'status_id',
                'value'     => function($data){ return $data->status->name; }
            ],
        ],
        ]); ?>
    </div>

    <div class="col-md-12">
        <h3><small>メール履歴</small>
        </h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getMails(),
        ]),
        'layout'  => '{items}{pager}',
        'emptyText' => 'まだありません',
        'showOnEmpty' => false,
        'tableOptions'   => ['class'=>'table table-condensed table-striped'],
        'summaryOptions' => ['class'=>'small text-right pull-right'],
        'columns' => [
            [
                'attribute' => 'mailer_id',
                'format'    => 'html',
                'value'     => function($data){ return Html::a(sprintf('%06d',$data->mailer_id), ['/mail-log/view', 'id'=>$data->mailer_id]); },
            ],
            [
                'attribute' => 'date',
                'format'    => ['date','php:Y-m-d H:i'],
            ],
            [
                'attribute' => 'subject',
            ],
        ],
        ]); ?>
    </div>

    <div class="col-md-12">
        <h3><small>DB操作履歴</small>
        </h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => ChangeLog::find()->where(['tbl'=>$model->tableName(), 'pkey'=>$model->customer_id]),
            'sort'  => ['defaultOrder' => ['create_date' => SORT_DESC]],
        ]),
        'layout'  => '{items}{pager}',
        'showOnEmpty' => false,
        'tableOptions'   => ['class'=>'table table-condensed table-striped'],
        'summaryOptions' => ['class'=>'small text-right pull-right'],
        'columns' => [
            [
                'attribute' => 'create_date',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->create_date,['/change-log/view','id'=>$data->create_date]); }
            ],
            'route',
            'action',
            'user.name',
        ],
        ]); ?>
    </div>

</div>
