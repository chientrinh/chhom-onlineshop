<?php
namespace frontend\modules\signup\controllers;

use Yii;
use yii\filters\VerbFilter;
use frontend\models\SignupForm;

/**
 * Site controller
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/signup/controllers/BaseController.php $
 * $Id: BaseController.php 1628 2015-10-09 13:48:35Z mori $
 */
abstract class BaseController extends \yii\web\Controller
{
    public $title;
    public $defaultAction = 'index';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->title = "会員登録";
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // all pages are under construction except 'site/index'
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions'=> ['index'],
                        'allow'  => true,
                        'roles'  => ['?'], // allow guest users only
                        'verbs'  => ['GET'],
                    ],
                    [
                        'actions'=> ['create','update','search'],
                        'allow'  => true,
                        'roles'  => ['?'], // allow guest users only
                        'verbs'  => ['GET','POST'],
                    ],
                    [
                        'allow'  => false, // everything else is denied
                        'denyCallback' => function ($rule, $action) {
                            throw new \yii\web\ForbiddenHttpException('いまログインしています。会員登録の必要はありません');
                        }
                    ],
                ],
            ],
        ];
    }

    abstract public function actionIndex();

    abstract public function actionCreate();

    abstract public function actionSearch($agreed);

    abstract public function actionUpdate($token);

}
