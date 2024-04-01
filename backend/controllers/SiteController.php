<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use backend\models\LoginForm;

/**
 * Site controller
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/SiteController.php $
 * $Id: SiteController.php 2759 2016-07-21 07:17:12Z mori $
 */
class SiteController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login','forgot-password','renew-password','error'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'], // allow authenticated user only for all other actions
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionFile($name)
    {
        $path = $this->viewPath . '/static/' . $name;

        if(! is_file($path) || ! is_readable($path))
            throw new \yii\web\NotFoundHttpException();

        return Yii::$app->response->sendFile($path, $name, ['inline'=>true]);
    }

    public function actionIndex()
    {
        if(Yii::$app->user->can('viewSitemap'))
            $viewFile = 'index';
        else
            $viewFile = 'static/tenant';

        return $this->render($viewFile);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login())
            return $this->goBack();

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionForgotPassword()
    {
        $model = new \backend\models\ForgotPasswordForm();
        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            if ($model->sendEmail())
                return $this->render('readyPasswordResetToken');

            else
                Yii::$app->getSession()->addFlash('error', "パスワード初期化の準備ができませんでした");
        }

        return $this->render('forgotPassword', [
            'model' => $model,
        ]);
    }

    public function actionRenewPassword($token)
    {
        try {
            $model = new \backend\models\RenewPasswordForm($token);
        } catch (\yii\base\InvalidParamException $e) {
            throw new \yii\web\BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->addFlash('success', "新しいパスワードが保存されました");
        }

        return $this->render('renewPassword', [
            'model' => $model,
        ]);
    }

    public function actionView($page)
    {
        $page = sprintf('static/%s',$page);

        return $this->render($page);
    }
}
