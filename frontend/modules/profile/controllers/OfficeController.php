<?php
namespace frontend\modules\profile\controllers;

use Yii;
use \common\models\AgencyOffice;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/controllers/OfficeController.php $
 * $Id: OfficeController.php 3970 2018-07-13 08:46:33Z mori $
 */

class OfficeController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['body_id']       = 'MyPage';
        $this->view->params['breadcrumbs'][] = ['label'=>'請求先情報','url'=>[$this->defaultAction]];

        return true;
    }

    /**
     * display customer's office
     */
    public function actionIndex()
    {
        $model = $this->findModel();

        return $this->render('index', ['model' => $model]);
    }

    /**
     * 404 Not Found
     */
    public function actionView($id=null)
    {
        throw new \yii\web\NotFoundHttpException();
    }

    /**
     * 404 Not Found
     */
    public function actionCreate()
    {
        $this->view->params['breadcrumbs'][] = ['label'=>'作成'];

        $model = $this->findModel();

        if(! $model->isNewRecord)
            return $this->redirect(['update']);

        if('zip2addr' == Yii::$app->request->post('scenario'))
        {
            $model->load(Yii::$app->request->post());
            $candidates = $model->zip2addr();
        }

        elseif($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->addFlash('success', "保存しました");
            return $this->redirect(['index']);
        }

        return $this->render('form', [
            'model'      => $model,
            'candidates' => isset($candidates) ? $candidates : null,
        ]);
    }

    /**
     * delete a model
     */
    public function actionDelete()
    {
        $model = $this->findModel();

        if($model->isNewRecord)
            throw new \yii\web\NotFoundHttpException();

        if(! $model->delete())
            Yii::$app->session->addFlash('success',"請求先情報を削除できませんでした。システム担当者へご連絡ください");

        Yii::$app->session->addFlash('success',"請求先情報を削除しました");

        return $this->redirect(['index']);
    }

    /**
     * update a model
     */
    public function actionUpdate($id=null)
    {
        $this->view->params['breadcrumbs'][] = ['label'=>'修正'];

        $model = $this->findModel();

        if('zip2addr' == Yii::$app->request->post('scenario'))
        {
            $model->load(Yii::$app->request->post());
            $candidates = $model->zip2addr();
        }

        elseif($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['index']);

        return $this->render('form', [
            'model'      => $model,
            'candidates' => isset($candidates) ? $candidates : null,
        ]);
    }

    private function findModel()
    {
        $user  = Yii::$app->user->identity;
        $model = AgencyOffice::findOne(['customer_id' => $user->id]);

        if(! $model)
            $model = new AgencyOffice(['customer_id' => $user->id]);

        return $model;
    }

}
