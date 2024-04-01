<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/stat.php $
 * $Id: stat.php 3080 2016-11-13 06:21:17Z mori $
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\helpers\ArrayHelper;
use \yii\data\ArrayDataProvider;
use \common\models\Company;

$this->params['breadcrumbs'][] = ['label' => '精算書', 'url' => ['payoff']];

$provider = new ArrayDataProvider([
    'allModels' => $query->asArray()->all(),
    'pagination' => [
        'pageSize' => 0,
    ],
    'sort' => ['attributes' => ['company_id']],
]);
?>
<script>
  function print() {
    var company = $('.company:checked').map(function() {
      return $(this).val();
    }).get();
    var company_list = company.join(',');
    var year = $('input[name=year]').val();
    var month = $('select[name=month]').val();
    window.open('print-payoff?year=' + year + '&month=' + month + '&company=' + company_list, '_blank');
  }
</script>
<?php $form = \yii\bootstrap\ActiveForm::begin([
    'method' => 'get',
    'action' => Url::current(['start_date' => null, 'end_date' => null]),
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label'   => 'col-sm-4',
            'wrapper' => 'col-sm-8',
            'error'   => '',
            'hint'    => '',
        ],
    ],
]) ?>
<div class="dispatch-default-index">
  <div class="body-content">
    <div class="col-md-10" style="margin-bottom: 15px;">
        <h2>月次集計</h2>

        <div id="tab" class="btn-group pull-left" data-toggle="buttons" style="margin-right:15px;">
            <p>年</p>
            <?php
                $year = (is_null(Yii::$app->request->get('year'))) ? date('Y') : Yii::$app->request->get('year');
            ?>
            <?= Html::textInput('year', $year, ['class' => 'form-control']) ?>
        </div>


        <div id="tab" class="btn-group pull-left" data-toggle="buttons" style="margin-right:15px;">
            <p>月</p>
            <?php
                $month = (is_null(Yii::$app->request->get('year'))) ? date('n') : Yii::$app->request->get('month');
                $month_list = ['1' => 1 ,'2' => 2,'3' => 3,'4' => 4,'5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, '10' => 10, '11' => 11, '12' => 12];
            ?>
            <?= Html::dropDownList('month', $month, $month_list, ['class' => 'form-control'], ['options' => [$month => ["Selected" => true]]]) ?>
        </div>

        <div class="col-md-2 pull-right" style="margin-top:15px;">
            <?= Html::submitInput('出力',['class' => 'form-control btn btn-primary pull-right', 'style' => 'width:100%;', 'onclick' => 'return print();']) ?>
        </div>
        <div class="col-md-2 pull-right" style="margin-top:15px;">
            <?= Html::submitInput('実行', ['class' => 'form-control btn btn-info pull-right', 'style' => 'width:100%;']) ?>
        </div>
    </div>
  </div>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $provider,
        'layout'  => '{items}{pager}',
        'columns' => [
            [
                'attribute' => '',
                'label'     => '出力',
                'format'    => 'raw',
                'value'     => function ($data)
                {
                    return Html::checkbox('company_id[]', false, ['value' => ArrayHelper::getValue($data, 'company_id'), 'label' => null, 'class' => 'company']);
                },
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => '',
                'label'     => '年月',
                'format'    => 'html',
                'value'     => function($data)
                {
                    $year = ArrayHelper::getValue($data, 'year');
                    $month = sprintf('%02d', ArrayHelper::getValue($data, 'month'));

                    return $year . $month;
                },
            ],
            [
                'attribute' => 'company_id',
                'label'     => '販売会社',
                'format'    => 'html',
                'value'     => function($data)
                {
                    $company = Company::findOne(ArrayHelper::getValue($data, 'company_id'));
                    if($company)
                        return $company->name;

                    return null;
                },
            ],
            [
                'attribute' => 'net_sales',
                'label'     => '支払額',
                'format'    => 'currency',
                'value'     => function ($data)
                {
                    // （通販売上） - （付与ポイント） - （使用ポイント（店頭））
                    return $data['sales'] - $data['point_given'] + $data['point_consume'];
                },
                'contentOptions' => ['class'=>'text-right'],
            ],
        ],
    ]) ?>
    <?php $form->end() ?>
  </div>
</div>

