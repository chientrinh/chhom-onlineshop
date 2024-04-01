<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/materialmaker/index.php $
 * $Id: index.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchMaterialMaker */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Material Makers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-maker-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Material Maker', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'maker_id',
            'material_id',
            'name',
            'manager',
            'email:email',
            // 'zip01',
            // 'zip02',
            // 'pref_id',
            // 'addr01',
            // 'addr02',
            // 'tel01',
            // 'tel02',
            // 'tel03',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
