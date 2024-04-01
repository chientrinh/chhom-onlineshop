<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/materialmaker/view.php $
 * $Id: view.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MaterialMaker */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Material Makers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-maker-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->maker_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->maker_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'maker_id',
            'material_id',
            'name',
            'manager',
            'email:email',
            'zip01',
            'zip02',
            'pref_id',
            'addr01',
            'addr02',
            'tel01',
            'tel02',
            'tel03',
        ],
    ]) ?>

</div>
