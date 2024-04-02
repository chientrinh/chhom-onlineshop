<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/DeliveryDocument.php $
 * $Id: DeliveryDocument.php 4191 2019-10-03 15:08:53Z mori $
 */

use Yii;
use yii\helpers\ArrayHelper;

class DeliveryDocument extends \yii\base\Widget
{
    /* @var Purchase model */
    public  $model;

    /* @var string */
    public  $title = '';

    public function init()
    {
        parent::init();

        if(! $this->title)
            $this->title = '納品書';
    }

    public function run()
    {
        $html  = '';
        $html .= $this->beginWrapper();
        $html .= $this->renderHtml();
        $html .= $this->endWrapper();

        return $html;
    }

    private function beginWrapper()
    {
        return '<page>';
    }

    private function endWrapper()
    {
        return '</page>';
    }

    private function renderHtml()
    {
        $model = $this->model;

        /**
         * bitbucket#59 一括発送カートの納品書出力処理変更
         * 一社分の商品のみ受注した場合でも合計表を表示するため、コメントアウト
         */

        // if((1 == count($model->companies)) && $model->company)
        // {
        //     return $this->render('delivery', [
        //         'title'   => $this->title,
        //         'model'   => $model,
        //         'company' => $model->company,
        //         'items'   => $model->items,
        //         'summaryColumns' => $this->getSummaryColumn(),
        //     ]);
        // }

        $html  = [];
        //$tax   = $model->computeTax(true);
        $tax   = $model->getCompanyTaxes(true);

        foreach($model->companies as $company)
        {
            $items = $model->getItemsOfCompany($company->company_id);
//            $taxedSubTotal = $model->getTaxedSubTotals();
            $taxedSubTotal = ['normal' => 0, 'reduced' => 0, 'normal_tax' => 0, 'reduced_tax' => 0];
            foreach($items as $item)
            {
                if($item->isReducedTax()) {
                    $taxedSubTotal['reduced'] += $item->getUnitPrice() * $item->quantity;
                    $taxedSubTotal['reduced_tax'] += $item->getUnitTax() * $item->quantity;
                } else {
                    $taxedSubTotal['normal'] += $item->getUnitPrice() * $item->quantity;
                    $taxedSubTotal['normal_tax'] += $item->getUnitTax() * $item->quantity;
                }
            }

            $target = Yii::$app->request->get('target');
            $taxSummaryColumns = [
                [
                    'tax_rate' => 8,
                    'subtotal' => $taxedSubTotal['reduced'],
                    'tax' => $taxedSubTotal['reduced_tax'],
                ],
                [
                    'tax_rate' => 10,
                    'subtotal' => $taxedSubTotal['normal'],
                    'tax' => $taxedSubTotal['normal_tax'],
                ],
                [
                    'tax_rate' => 999,
                    'subtotal' => $taxedSubTotal['normal'] + $taxedSubTotal['reduced'] ,
                    'tax' => $taxedSubTotal['normal_tax'] + $taxedSubTotal['reduced_tax'],
                ],
            ];
            $summaryColumns = [
                [
                    'attribute'=> 'subtotal',
                    'format'   => 'currency',
                    'value'    => array_sum(ArrayHelper::getColumn($items, 'charge')),
                ],
                [
                    'attribute'=> 'tax',
                    'format'   => 'currency',
                    'value'    => ArrayHelper::getValue($tax, $company->company_id),
                ],
            ];

            if(strtotime($model->create_date) >= \common\models\Tax::newDate()) {
                unset($summaryColumns[1]);
            }

            // 健康相談納品書の場合はすべて表示させる
            if ($target === 'sodan') {
                $summaryColumns = array_merge($summaryColumns, [
                    [
                        'attribute'=> 'postage',
                        'format'   => 'currency',
                        'value'    => $model->postage
                    ],
                    [
                        'attribute'=> 'handling',
                        'format'   => 'currency',
                        'value'    => $model->handling
                    ],
                    [
                        'attribute'=> 'total_charge',
                        'format'   => 'currency',
                    ],
                    [
                        'attribute'=> 'payment_id',
                        'format'   => 'html',
                        'value'    => $model->payment->name
                    ],
                ]);
            }
            $html[] = $this->render('delivery',[
                'title'   => $this->title,
                'model'   => $model,
                'company' => $company,
                'items'   => $items,
                'target'  => $target,
                'summaryColumns' => $summaryColumns,
                'taxSummaryColumns' => $taxSummaryColumns
            ]);
        }
        if(false == $model->isGift() && $target !== 'sodan')
            $html[] = SummaryDocument::widget(['model'      => $model,
                                               'attributes' => $this->getSummaryColumn() ]);

        return implode('<pagebreak />', $html);
    }

    private function getSummaryColumn()
    {
        $model    = $this->model;
        $template = [
            'subtotal:currency',
/*
            'NORMAL_TAX' =>['label' => '10%対象',
                               'format'    => 'currency',
                               'value'     => $model->getTaxes()['normal'],
            ],
            'REDUCED_TAX' =>['label' => '8%対象',
                               'format'    => 'currency',
                               'value'     => $model->getTaxes()['reduced'],
            ],
*/
            'tax:currency',
            'postage:currency',
            'handling:currency',

            'POINT_CONSUME' =>['attribute' => 'point_consume',
                               'format'    => 'currency',
                               'value'     => 0 - abs($model->point_consume),
            ],

            'DISCOUNT' =>   ['attribute' => 'discount',
                             'format'    => 'currency',
                             'value'     => 0 - abs($model->discount),
            ],
            'total_charge:currency',

            'RECEIVE' => ['attribute' => 'receive',
                          'format'    => 'currency',
            ],
            'CHANGE'  => ['attribute' => 'change',
                          'format'    => 'currency',
            ],
            [
                'attribute' => 'payment_id',
                'value'     => ($p = $model->payment) ? $p->name : '<strong style="color:red">(未指定)</strong>',
            ],
            'PAID' =>['attribute' => 'paid',
                      'format'    => 'text',
                      'value'     => $model->paid ? '済み' : '未',
            ],
            'POINT_GIVEN' => ['attribute' => 'point_given',
                               'label' => '今回加算されたポイント',
                               'format'  => 'currency',
                               'value'   => abs($model->point_given),
            ],
            'POINT' => ['attribute' => 'point',
                        'label' => '現在のポイント',
                        'format'  => 'currency',
                        'value'   => ($model->customer) ? abs($model->customer->point) : 0,
            ]
        ];

        if(0 == $model->point_consume)
            unset($template['POINT_CONSUME']);

        if(0 == $model->discount)
            unset($template['DISCOUNT']);

        if(0 == $model->receive)
        {
            unset($template['RECEIVE']);
            unset($template['CHANGE']);
        }
        if(0 == $model->paid)
            unset($template['PAID']);

        if(strtotime($model->create_date) < \common\models\Tax::newDate()) {
            unset($template['NORMAL_TAX']);
            unset($template['REDUCED_TAX']);
        } else {
            unset($template['tax']);
        }
        return array_values($template);
    }
}
