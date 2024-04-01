<?php
namespace frontend\modules\signup\controllers;

use Yii;

/**
 * Site controller
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/signup/controllers/HjController.php $
 * $Id: HjController.php 1413 2015-08-29 12:34:43Z mori $
 */
class HjController extends MigrateController
{
    public function actionIndex()
    {
        return $this->render('index');
        //return $this->redirect(['search','agreed'=>1]);
    }

}
