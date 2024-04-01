<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/webdb/views/customer/index.php $
 * $Id: index.php 1538 2015-09-22 13:04:18Z mori $
 */

use yii\helpers\Html;
use \yii\bootstrap\ActiveForm;

/**
 * @var $this         yii\web\View
 * @var $db           string
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = "$db 顧客";
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index','db'=>$db]];

$dataProvider->sort->defaultOrder = ['customerid'=>SORT_DESC];

?>
<div class="customer-index">

    <h1>
      <?= $this->title ?>
    </h1>

    <div class="row">

    <?php $form = ActiveForm::begin([
        'action' => [$this->context->action->id,
                     'db' => $db,
        ],
        'method' => 'get',
        'fieldConfig' => [
            'enableLabel' => false,
            'template' => '{input}{error}',
        ],
    ]); ?>

    <div class="col-md-6">
    <?= $form->field($searchModel, 'keywords')->textInput([
        'placeholder'=> $searchModel->getAttributeHint('keywords'),
        'title' => '顧客をあいまい検索します',
    ]) ?>
    </div>

    <div class="col-md-2">
    <?= Html::submitInput('検索',['class'=>'form-control btn btn-info']) ?>
    </div>

    <?php $form->end() ?>

    </div>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'customerid',
            [
                'attribute'=> 'name',
                'format'   => 'html',
                'value'    => function($data)use($db){ return Html::a($data->name, ['/webdb/customer/view','id'=>$data->customerid,'db'=>$db]); },
            ],
            'kana',
        ],
    ]); ?>

</div>
