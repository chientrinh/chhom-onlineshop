<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/RemedyLabels.php $
 * $Id: RemedyLabels.php 2621 2016-06-24 09:57:25Z mori $
 */

use Yii;

class RemedyLabelCsvs extends \yii\base\Widget
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

        return $this->generateCsvs();
    }


    private function generateCsvs()
    {
        $record = "";
        $complex_csv = [];
        $machine_csv = [];
        $widget = new \common\widgets\doc\remedy\RemedyLabelCsv();
        $complexRemedies = $this->model->getItemsToDrop();
        $machineRemedies = $this->model->getItemsToMachine();
        
//        print_r($complexRemedies);
//        print("hogehogehoge\n\n");
//        print_r($machineRemedies);
//        exit;
        if($complexRemedies) {
            foreach($complexRemedies as $complexRemedy)
            {
                $csv_record = "1\t";
                for ($i = 1; $i <= $complexRemedy->qty; $i++) {
                    $csv_record .= \common\widgets\doc\remedy\RemedyLabelCsv::widget([
                        'title' => sprintf('%06d', $this->purchase_id),
                        'model' => $complexRemedy,]);
                    if(isset($csv_record) && strlen($csv_record) > 0) {
                        $complex_csv[] = $csv_record;
                    }
                }
            }
            if($complex_csv && count($complex_csv) > 0)
                $record .= implode('', $complex_csv);
        }
        
        if($machineRemedies) {
            foreach($machineRemedies as $machineRemedy)
            {
                $csv_record = "1\t";
                $csv_record .= \common\widgets\doc\remedy\RemedyLabelCsv::widget([
                    'title' => sprintf('%06d', $this->purchase_id),
                    'model' => $machineRemedy,
                ]);

                if($csv_record && strlen($csv_record) > 0)
                    $machine_csv[] = $csv_record;

            }
            if($machine_csv && count($machine_csv) > 0)
                $record .= implode('', $machine_csv);
        }

        return $record;
    }

}
