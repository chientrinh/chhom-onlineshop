<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer-addrbook/view.php $
 * $Id: view.php 2994 2016-10-20 05:03:22Z mori $
 *
 * @var $this yii\web\View
 * @var $customer common\models\Customer
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->params['breadcrumbs'][] = ['label' => '顧客', 'url' => ['/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $customer->name, 'url' => ['/customer/view','id'=>$customer->customer_id]];
$this->params['breadcrumbs'][] = ['label' => '住所録', 'url' => Url::current()];

?>
<div class="customer-addrbook-view">

    <h1><?= $customer->name ?><small>さん 住所録</small></h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'=>$customer->getAddrbooks(),
            'sort' => [
                'attributes' => [
                    'name'  => ['asc' => ['name01'=>SORT_ASC,'name02'=>SORT_ASC ],
                               'desc'=> ['name01'=>SORT_DESC,'name02'=>SORT_DESC]],
                    'kana' => ['asc' => ['kana01'=>SORT_ASC, 'kana02'=>SORT_ASC ],
                               'desc'=> ['kana01'=>SORT_DESC,'kana02'=>SORT_DESC]],
                    'zip'  => ['asc' => ['zip01'=>SORT_ASC , 'zip02'=>SORT_ASC ],
                               'desc'=> ['zip01'=>SORT_DESC, 'zip02'=>SORT_DESC]],
                    'addr' => ['asc' => ['pref_id'=>SORT_ASC ],
                               'desc'=> ['pref_id'=>SORT_DESC]],
                    'tel'  => ['asc' => ['tel01'=>SORT_ASC ,'tel02'=>SORT_ASC ,'tel03'=>SORT_ASC ],
                               'desc'=> ['tel01'=>SORT_DESC,'tel02'=>SORT_DESC,'tel03'=>SORT_DESC]],
                    'update_date',
                ],
            ],
        ]),
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
            ],
        ],
    ]); ?>

    <?= Html::a('追加',['create','id'=>$customer->customer_id],['class'=>'btn btn-success']) ?>
</div>
