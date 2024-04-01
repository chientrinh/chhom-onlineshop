<?php

namespace backend\modules\ysd;

use Yii;

/*
* $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/Module.php $
* $Id: Module.php 2281 2016-03-21 02:07:57Z mori $
*/
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'backend\modules\ysd\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['wizard'],
                    ],
                    [
                        'actions' => ['index','view'],
                        'allow'   => true,
                        'roles'   => ['worker'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        Yii::$app->controller->view->params['breadcrumbs'][] = [
            'label'=>'YSD口座振替',
            'url'=>[sprintf('%s/index', $this->defaultRoute)]
        ];

        return true;
    }
}
