<?php

namespace backend\modules\casher\controllers;

/**
 * $Id: Hj_tokyo_shopController.php 2019-12-18 21:26 sakai $
 */

use Yii;
use \common\models\Branch;
use \common\models\Payment;
use \common\models\PurchaseDelivery;
use \backend\models\SearchPurchase;
use \backend\models\CsvUploadMultiForm;
use yii\web\UploadedFile;

class HjTokyoShopController extends DefaultController
{
    public $branch_id = Branch::PKEY_HJ_TOKYO;

    public function getViewPath()
    {
        return dirname(__DIR__) . '/views/default';
    }

    public function init()
    {
      $this->module->setBranch($this->branch_id);
    }

    public function actionCreate()
    {
        $this->module->setBranch($this->branch_id);
        $this->view->params['breadcrumbs'][] = ['label'=> 'レジ'];
        return BaseController::actionCreate();
    }

}
