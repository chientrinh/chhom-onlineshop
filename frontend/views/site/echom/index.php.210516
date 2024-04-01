<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/index.php $
 * $Id: index.php 3810 2018-01-05 09:59:37Z naito $
 *
 * @var $this yii\web\View
 * @var $shops array of common\models\Branch
 */

$this->title = Yii::$app->name;

$current = sprintf('/%s/%s',$this->context->id, $this->context->defaultAction);
// $defaultTag = 'hot';
?>
<div class="site-index">

    <div class="body-content">

    <div class="Info-Area">
        <h2><span>オススメ</span></h2>
        <?= \frontend\widgets\HotProductList::widget([
            'tag' => 'recommend',
            'options'      => ['class' => 'list-view Item-Area'],
            'layout'       => '{items}{pager}',
        ])?>
    </div>

    </div>


</div>
