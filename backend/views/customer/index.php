<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer/index.php $
 * $Id: index.php 4151 2019-04-12 02:09:01Z mori $
 *
 * @var $this yii\web\View
 * @var $searchModel backend\models\SearchCustomer
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use \yii\bootstrap\ActiveForm;

$title = "顧客";

$agency_array = [99 => '', 0 => 'HJ',1 => 'HE',2 => 'HP', 3 => 'HJ HE', 4 => 'HJ HP', 5 => 'HE HP', 6  => 'HJ HE HP'];



$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels) . ' | ' . Yii::$app->name;

$jscode = "
$('#toggle-btn-1').click(function(){
         $('.sub-menu-1').toggle();
 	return true;
});
";
$this->registerJs($jscode);

?>
<div class="customer-index">

    <h1>
      <?= $title ?>
      <small>

      <?php if(Yii::$app->request->isPost): ?>
      <?php else: ?>

          <?php if($searchModel->grade_id): ?>
              (<?= $searchModel->grade->name ?>)
          <?php endif ?>

          <?php if($searchModel->company): ?>
              (<?= \common\models\Company::findOne($searchModel->company)->name ?>)
          <?php endif ?>

          <?php if($searchModel->membership): ?>
              (<?= \common\models\Membership::findOne($searchModel->membership)->name ?>)
          <?php endif ?>

      <?php endif ?>
      </small>
    </h1>

    <div class="row">

    <div class="sub-menu-1" style="<?= Yii::$app->request->isPost ? 'display:none' : '' ?>">
    <?php $form = ActiveForm::begin([
        'action' => [$this->context->action->id,
                     'membership'=> Yii::$app->request->get('membership'),
                     'company'   => Yii::$app->request->get('company'),
                     'grade'     => Yii::$app->request->get('grade'),
        ],
        'method' => 'get',
        'fieldConfig' => [
            'enableLabel' => false,  
        ],
    ]); ?>

    <div class="col-md-6">
    <?= $form->field($searchModel, 'keywords')->textInput([
        'placeholder'=>'かな TEL 会員証NO 氏名 メモ',
        'title' => '顧客をあいまい検索します',
    ]) ?>
    </div>

    <div class="col-md-2">
    <?= Html::submitInput('検索',['class'=>'form-control btn btn-info']) ?>
    <?= Html::a('詳細...', '#',['id'=>'toggle-btn-1','class'=>'pull-right']) ?>
    </div>

    <div class="col-md-1 pull-right">
        <?= Html::a('CSV',\yii\helpers\Url::current(['format'=>'csv']),['class'=>'form-control btn btn-default']) ?>
    </div>
    <div class="col-md-1 pull-right">
        <?= Html::a('顧客統合', ['unite-customer'], ['class'=>'form-control btn btn-default']) ?>
    </div>

    <?php $form->end() ?>

    </div>

    <div class="sub-menu-1" style="<?= Yii::$app->request->isPost ? '' : 'display:none'?>">
    <div class="well">
        <?= $this->render('_search', ['model'=>$searchModel]) ?>
    </div>
    </div>


    </div>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'filterUrl'    => \yii\helpers\Url::current(),
        'pager'        => ['maxButtonCount' => 20],
        'rowOptions' => function($model) {
            if($model->isExpired())
                return ['style' => 'background:#cccccc;'];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'customer_id',
                'headerOptions' => ['class'=>'col-md-1'],
            ],
            [
                'attribute'=> 'grade_id',
                'label'    => $searchModel->getAttributeLabel('grade'),
                'filter'   => \yii\helpers\ArrayHelper::map(\common\models\CustomerGrade::find()->all(), 'grade_id', 'longname'),
                'format'   => 'html',
                'value'    => function($data){ return Html::tag('small',$data->grade->longname); },
            ],
            [
                'attribute' => 'is_agency',
                'format'    => 'raw',
                'value'     => function($data)
                {
                    return true == $data->isAgency() ? '代理店' : '個人';
                },
                'filter' => ['1' => '個人', '2' => '代理店'],
            ],
            ['class' => 'yii\grid\DataColumn' ,
                'attribute' => '代理店所属',
                'format'    => 'raw',
                'value'     => function($model)
                {
                    $agency_array = [99 => '', '0' => 'HJ','1' => 'HE', '2' => 'HP', '3' => 'HJ HE', '4' => 'HJ HP', '5' => 'HE HP', '6'  => 'HJ HE HP'];
                    return $agency_array[$model->getAgencies()];
                },
                'filter' => Html::activeDropDownList($searchModel, "agencies", $agency_array,['class' => 'form-control']),
            ],
            [
                'attribute'=> 'name',
                'format'   => 'html',
                'value'    => function($data){ return Html::a($data->name, ['/customer/view','id'=>$data->customer_id]); },
            ],
            'kana',
            [
                'attribute'=> 'pref_id',
                'format'   => 'html',
                'label'    => $searchModel->getAttributeLabel('pref'),
                'filter'   => \yii\helpers\ArrayHelper::map(\common\models\Pref::find()->all(), 'pref_id', 'name'),
                'value'    => function($data){return Html::tag('div',$data->pref->name,['class'=>'col-md-12','title'=>$data->addr]); },
            ],
            [
                'attribute'=> 'code',
                'format'   => 'html',
                'value'    => function($data){ return Html::tag('code',$data->code); },
            ],
            [
                'attribute'=> 'create_date',
                'format'   => ['date','php:Y-m-d'],
            ],
            [
                'header'  => Html::a('購入日',['purchase']),
                'format'  => ['date','php:Y-m-d'],
                'value'   => function($data){
                    return $data->getPurchases()->max('create_date');
                },
            ],
            [
                'label' => '有効・無効',
                'attribute' => 'is_active',
                'format'    => 'raw',
                'value'     => function($data)
                {
                    return $data->isExpired() ? '無効' : '有効';
                },
                'filter' => ['0' => 'すべて', '1' => '有効', '2' => '無効'],
            ]
        ],
    ]); ?>

    <p>
<?= Html::a("顧客を作成", ['create'], ['class' => 'btn btn-success']) ?>
    </p>
  
</div>
