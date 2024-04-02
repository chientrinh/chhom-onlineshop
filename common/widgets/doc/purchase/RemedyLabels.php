<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/RemedyLabels.php $
 * $Id: RemedyLabels.php 2621 2016-06-24 09:57:25Z mori $
 */

use Yii;

class RemedyLabels extends \yii\base\Widget
{
    /* @var purchase_id to find Purchase Model,
       if it is invalid purchase_id, a bogus receipt will be rendered */
    public  $purchase_id;

    /* @var Purchase model itself.
       if $purchase_id is specified, it will be overridden */
    public  $model;

    private $_items;

    public function init()
    {
        parent::init();

        if($this->purchase_id)
            $this->model = \common\models\Purchase::findOne($this->purchase_id);
        else
            $this->purchase_id = $this->model->purchase_id;
    }

    public function run()
    {
        if(! $this->model instanceof \common\models\Purchase)
            throw new \yii\base\NotSupportedException('model %s is not Purchase', $this->model->className());

        return $this->beginWrapper()
             . $this->generateLabels();
    }

    private function beginWrapper()
    {
        return sprintf('<head><meta charset="%s"></head>', Yii::$app->charset);
    }

    private function generateLabels()
    {
        $html = [];

        $complexRemedies = $this->model->getItemsToDrop();
        $machineRemedies = $this->model->getItemsToMachine();
        
        if($complexRemedies)
        foreach($complexRemedies as $complexRemedy)
        {
            for ($i = 1; $i <= $complexRemedy->qty; $i++)
                $html[] = \common\widgets\doc\remedy\RemedyLabel::widget([
                    'title' => sprintf('%06d', $this->purchase_id),
                    'model' => $complexRemedy,
                ]);
        }

        if($machineRemedies)
        foreach($machineRemedies as $machineRemedy)
        {
            $html[] = \common\widgets\doc\remedy\RemedyLabel::widget([
                'title' => sprintf('%06d', $this->purchase_id),
                'model' => $machineRemedy,
            ]);
        }

        return implode('', $html);
    }

}
