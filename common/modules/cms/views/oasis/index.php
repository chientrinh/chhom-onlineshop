<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/cms/views/oasis/index.php $
 * $Id: index.php 2921 2016-10-05 06:47:10Z mori $
 *
 * @var $this     \yii\base\View
 * @var $customer \common\models\Customer
 * @var $provider \yii\data\ActiveDataProvider
 */

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

$this->params['body_id'] = 'MyPage';

$this->params['breadcrumbs'][] = ['label' => 'マイページ', 'url' => ['/profile']];
$this->params['breadcrumbs'][] = ['label' => '会報誌「オアシス」', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '一覧', 'url' => ['index']];

// generate <title>
$labels = ArrayHelper::getColumn($this->params['breadcrumbs'], 'label');
krsort($labels);
$labels[] = Yii::$app->name;
$this->title = implode(' | ', $labels);

?>

<div class="product-index">

    <h1 class="mainTitle">会報誌「オアシス」</h1>

        <?= \yii\widgets\ListView::widget([
            'dataProvider' => new \yii\data\ArrayDataProvider([
                'allModels' => $models,
                'sort' => [
                    'attributes'   => ['id', ],
                    'defaultOrder' => ['id' => SORT_DESC],
                ],
                'pagination' => ['pageSize' => 8],
            ]),
            'layout'    => '{pager}{items}{pager}',
            'itemView'  => '_item',
        ]) ?>

</div>
