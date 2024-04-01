<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/magazine/controllers/LoginController.php $
 * $Id: LoginController.php 1220 2015-07-31 16:43:07Z mori $
 */

namespace app\modules\magazine\controllers;

use Yii;

/**
 * @brief 「こちらのコンテンツは会員限定です」に貼ってあるURLから誘導される
 */
class LoginController extends BaseController
{ 
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    // deny guest users
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    // allow registered users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @brief history.goBack() のようなことを実現させたかったのでこうなった
     * (login を誘導されて、loginしてみたら、その記事が読める)
     */
    public function actionView($id,$page)
    {
        return $this->redirect([sprintf('/magazine/%s/%s', $id, $page)]);
    }

}
