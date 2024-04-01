<?php
namespace backend\modules\ysd\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/controllers/DefaultController.php $
 * $Id: DefaultController.php 1961 2016-01-11 01:39:26Z mori $
 */
use Yii;

class DefaultController extends \backend\controllers\BaseController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
