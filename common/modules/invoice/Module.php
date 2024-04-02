<?php

namespace common\modules\invoice;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/invoice/Module.php $
 * $Id: Module.php 2177 2016-02-28 08:09:15Z mori $
 */

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'common\modules\invoice\controllers';
    public $defaultRoute        = 'unknown';

    public function init()
    {
        parent::init();
    }

    public function beforeAction($action)
    {
        Yii::$app->controller->view->params['breadcrumbs'][] = ['label' => '請求書', 'url'=>['/invoice']];
        Yii::$app->controller->setViewOption($action);

        return parent::beforeAction($action);
    }

    public function initialize($year, $month)
    {
        if(! $this->validate($year, $month))
            return false;

        return components\InvoiceMaker::generateAll($year, $month);
    }

    public function update($year, $month, $customer_id)
    {
        if(! $this->validate($year, $month))
            return false;

        if(false !== components\InvoiceMaker::generateOne($year, $month, $customer_id))
            return true;

        return false;
    }

    private function validate($year, $month)
    {
        $model = new \common\models\DateForm([
            'year' => $year,
            'month'=> $month,
        ]);
        return ($year && $month && $model->validate());
    }
}
