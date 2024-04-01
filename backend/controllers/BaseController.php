<?php
namespace backend\controllers;

use Yii;
use \yii\base\View;
use \yii\helpers\ArrayHelper;

/**
 * Site controller
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/BaseController.php $
 * $Id: BaseController.php 3851 2018-04-24 09:07:27Z mori $
 */
abstract class BaseController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only'  => ['index','view','viewbyname','create','update','add','delete','expire','activate'],
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
            'crud' => [
                'class' => \yii\filters\AccessControl::className(),
                'only'  => ['create','update','delete','expire','activate'],
                'rules' => [
                    [
                        // customer 系統は従業員なら誰でも編集を許可
                        'controllers'   => ['customer','customer-info','customer-membership'],
                        'allow'         => true,
                        'roles'         => ['worker'],
                    ],
                    [
                        // 他の系統は、もうちょっと偉い人だけ編集を許可
                        'allow'         => true,
                        'roles'         => ['manager'],
                    ],
                    [
                           'controllers' => ['pointing'],
                           'actions' => ['create', 'expire'],
                           'allow' => true,
                           // Allow moderators and admins to update
                           'roles' => ['worker'],
                    ],

                    [
                        // テナントには特定のモデルなら編集を許可 jancodeを許可 2017/04/05
                        'controllers' => ['purchase','product-master','product','product-description','product-image','jancode','subcategory','offer-seasonal'],
                        'roles'       => ['tenant'],
                        'allow'       => true,
                    ],
                ],
            ],
            'privacy' => [
                'class' => \yii\filters\AccessControl::className(),
                'only'  => ['view'],
                'rules' => [
                    [
                        // 健康相談の系統に制限をかける
                        'controllers'   => ['recipe','karute','karute-item'],
                        'allow'         => true,
                        'matchCallback' => function ($rule, $action)
                        {
                            $staff = Yii::$app->user->identity;
                            return $staff->hasRole(['reception','manager','wizard']);
                        },
                    ],
                    [
                        // 他の系統にはこのルールを適用しない：全員許可
                        'allow' => true,
                    ]
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        if(! Yii::$app->user->can('viewSitemap'))
            $this->layout = '/visitor';

        return true;
    }

    /**
     * Deletes an existing Category model.
     * @param integer $id
     * @return 403 Forbidden
     */
    public function actionDelete($id)
    {
        throw new \yii\web\HttpException(403, "削除は許可されていません");
    }

}
