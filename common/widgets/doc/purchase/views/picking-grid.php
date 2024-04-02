<?php 
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/views/picking-grid.php $
 * $Id: picking-grid.php 3602 2017-09-21 04:31:43Z kawai $
 * @var $company Company
 * @var $items PurchaseItem
 */

use \yii\helpers\Html;
use \common\models\Company;
use \common\models\Product;
use \common\models\PurchaseItem;
use \common\models\RemedyStock;
use \common\models\RemedyVial;

?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider([
        'allModels'  => $items,
        'pagination' => false,
    ]),
    'id' => 'piking-item',
    'layout'=>'{items}',
    'caption' => sprintf('<h2>%s</h2>',strtoupper($company->key)),
    'columns' => [
        [
            'attribute' => 'pickcode',
            'format'    => 'html',
            'value'     => function($data)use($company)
            {
                if($data->pickcode)
                    return $data->pickcode;

                if((Company::PKEY_HJ == $company->company_id) &&
                    (0 == substr_compare($data->code, RemedyStock::EAN13_PREFIX, 0, strlen(RemedyStock::EAN13_PREFIX)))
                )
                {
                    if($data->children)
                        return Html::tag('span',"滴下レメディー",['class'=>'tailored-remedy']);

                    if(null === $data->parent)
                        return Html::tag('span',"単品",['class'=>'tailored-remedy']);

                    else
                        return Html::tag('span',"+");
                }

                return 'なし';
            },
            'headerOptions' => ['class'=>'col-xs-2','style'=>'width:17%'],
        ],
        [
            'attribute'      => 'quantity',
            'contentOptions' => ['class'=>'text-center'],
            'headerOptions'  => ['class'=>'col-xs-2','style'=>'width:7%'],
        ],
        [
            'label' => '',
            'value' => function($data)
            {
                if(($s = $data->stock) && ($p = $s->potency))
                    return preg_replace('/combination/', '', $p->name);
            },
            'contentOptions' => ['class'=>'text-center'],
            'headerOptions'  => ['class'=>'col-md-1 col-xs-1','style'=>'width:7%'],
        ],
        [
            'attribute'      => 'name',
            'format'         => 'html',
            'value'          => function($data)
            {
/*
                if(($s = $data->stock) && ($p = $s->potency) && ($r = $s->remedy))
                {
                    if(RemedyVial::DROP == $s->vial_id)
                        return '&nbsp;&nbsp; +' . $r->abbr;

                    if($v = $s->vial)
                        return $r->abbr .' '. preg_replace('/[小]?瓶/u', '', $v->name);
                }
*/
                $name = preg_replace('/とらのこ.*年会費/u','<strike>${0}</strike>', $data->name);

                return nl2br($name);
            },
            'contentOptions' => ['class'=>'text-left product-name'],
            'headerOptions'  => ['class'=>'text-left col-md-5 col-xs-3','style'=>'width:48%'],
        ],
        [
            'label'          => '単価',
            'attribute'      => 'price',
            'format'         => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'headerOptions'  => ['class'=>'col-xs-2','style'=>'width:7%'],
        ],
        [
            'label'          => 'ご優待',
            'value'          => function($data)
            {
                if($rate   = $data->discount_rate)   { return "- $rate %"; }
                if($amount = $data->discount_amount) { return "- ￥$amount"; }
                return 0;
            },
            'contentOptions' => ['class'=>'text-right'],
            'headerOptions'  => ['class'=>'col-xs-2','style'=>'width:7%'],
        ],
        [
            'label'          => '小計',
            'attribute'      => 'charge',
            'format'         => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'headerOptions'  => ['class'=>'col-xs-2','style'=>'width:7%'],
        ],
    ],
]) ?>

