<?php

namespace frontend\components;
use Yii;

/**
 * @link $URL: https://tarax.toyouke.com/svn/MALL/frontend/components/DefaultController.php $
 * @version $Id: DefaultController.php 1047 2015-05-27 04:25:35Z mori $
 *
 * Abstract class for log-in required pages
 *
 */

abstract class DafaultController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }
}
