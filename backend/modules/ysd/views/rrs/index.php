<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/views/rrs/index.php $
 * @version $Id: index.php 3843 2018-03-14 09:14:15Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel common\models\ysd\RegisterResponse
 */

?>
<div class="register-response-index">

    <h1>登録結果</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,        'filterModel'  => $searchModel,
        'columns' => [

            [
                'attribute' => 'rrs_id',
                'format'    => 'html',
                'value'     => function($data){ return Html::a(sprintf('%06d',$data->rrs_id), ['view','id'=>$data->rrs_id]); },
            ],
            'created_at:datetime',
            [
                'attribute' => 'cdate',
                'format'    => 'date',
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute'=>'cdate',
                    'language' => 'ja',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class'=>'form-control col-md-12'],
                ]),
            ],
            'errcd',
            'custno',
            'customer.name',
        ],
    ]); ?>
    
    <p>
        ※登録結果ファイル（CSV）を下のフォームで選択し「送信」をクリックすると、データを抽出して登録します
    </p>
    <?php echo $this->render('upload', ['model'=>$csvModel]);?>


</div>
