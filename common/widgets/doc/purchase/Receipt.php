<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/Receipt.php $
 * $Id: Receipt.php 3498 2017-07-20 10:13:30Z kawai $
 */

use Yii;

class Receipt extends \yii\base\Widget
{
    /* @var purchase_id to find Purchase Model,
       if it is invalid purchase_id, a bogus receipt will be rendered */
    public  $purchase_id;

    /* @var Purchase model itself.
       if $purchase_id is specified, it will be overridden */
    public  $model;

    /* @var document title, such as '領収書' */
    public  $title;

    public  $print_name_flg = 0;

    private $_items;

    public function init()
    {
        parent::init();

        if($this->purchase_id)
            $this->model = \common\models\Purchase::findOne($this->purchase_id);

        elseif(! $this->model)
            $this->model = new \common\models\Purchase();

        if(! $this->title)
        {
            if(! $this->model->isNewRecord && $this->model->paid)
                $this->title = '領収書';
            else
                $this->title = '明細書';
        }

        if(\common\models\Payment::PKEY_DROP_SHIPPING == $this->model->payment_id)
        foreach($this->model->items as $k => $item)
        {
            if(0 < $item->discount_rate)  { $item->discount_rate   = 0; }
            if(0 < $item->discount_amount){ $item->discount_amount = 0; }
        }
    }

    public function run()
    {
        if(! $this->model instanceof \common\models\Purchase)
            throw new \yii\base\NotSupportedException('model %s is not Purchase', $this->model->className());

        return $this->generateHtml();
    }

    private function generateHtml()
    {
        if($this->model->isExpired())
            return '伝票が無効のため、 レシートは印刷できません';
        
        return $this->render('receipt', [
            'model' => $this->model,
            'title' => $this->title,
            'print_name_flg' => $this->print_name_flg,
        ]);
    }

}
