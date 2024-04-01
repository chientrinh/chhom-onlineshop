<?php

namespace frontend\modules\profile;
use Yii;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/Module.php $
 * $Id: Module.php 1921 2015-12-26 04:38:36Z mori $
 */

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'frontend\modules\profile\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        Yii::$app->controller->view->params['breadcrumbs'][] = ['label'=>'マイページ','url'=>['/profile/default/index']];

        return true;
    }

}
