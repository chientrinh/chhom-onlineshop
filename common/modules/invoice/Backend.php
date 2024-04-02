<?php

namespace common\modules\invoice;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/invoice/Backend.php $
 * $Id: Backend.php 1674 2015-10-16 20:19:06Z mori $
 */

class Backend extends \yii\base\Module
{
    public $controllerNamespace = 'common\modules\invoice\controllers';
    public $defaultRoute        = 'invoice/backend/index';

    public $year;
    public $month;

    public function init()
    {
        parent::init();

        if(! $this->year && ! $this->month)
        {
            $target_date = strtotime('-1 month');
            $this->year  = date('Y', $target_date);
            $this->month = date('m', $target_date);
        }
    }

    public function initialize()
    {
        if(! $this->validate())
            return false;

        return false;
    }

    private function validate()
    {
        $yearValidator = new \yii\validators\NumberValidator([
            'min' => 1900,
            'max' => date('Y'),
        ]);
        $monthValidator = new \yii\validators\NumberValidator([
            'min' =>  1,
            'max' => 12,
        ]);
        if(
            $yearValidator->validate($this->year, $error) &&
            $monthValidator->validate($this->month, $error)
        )
            return true;
        
        return false;
    }
}
