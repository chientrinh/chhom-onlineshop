<?php
namespace frontend\modules\profile\controllers;

use Yii;
use common\models\Facility;
use common\models\Zip;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/controllers/FacilityController.php $
 * $Id: FacilityController.php 4067 2018-11-28 08:10:14Z kawai $
 */

class FacilityController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['body_id']       = 'MyPage';
        $this->view->params['breadcrumbs'][] = ['label'=>'提携施設','url'=>[$this->defaultAction]];

        return true;
    }

    /**
     * display customer's all facilities
     */
    public function actionIndex()
    {
        $user  = Yii::$app->user->identity;
        $models = $this->findModel()->all();
        
        return $this->render('index', ['customer' => $user, 'models' => $models]);
    }

    /**
     * display customer's one facility
     */
    public function actionView($id=null)
    {
        throw new \yii\web\NotFoundHttpException();
    }

    /**
     * create customer's facility
     */
    public function actionCreate()
    {
        $user  = Yii::$app->user->identity;

        $this->view->params['breadcrumbs'][] = ['label'=>'作成'];

        $model = new Facility(['customer_id' => $user->id,
                                   'private'     => 1,
                                   'pub_date'    => date('Y-m-d'),]);

        if('zip2addr' == Yii::$app->request->post('scenario'))
        {
            $model->load(Yii::$app->request->post());
            $this->zip2addr($model);
        }

        elseif($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->addFlash('success',"保存しました");
            return $this->redirect(['index']);
        }

        return $this->render('form', [
            'model'      => $model,
        ]);
    }

    /**
     * update a model
     */
    public function actionUpdate($id=null)
    {
        $this->view->params['breadcrumbs'][] = ['label'=>'修正'];

        $model = $this->findModel($id)->One();

        if('zip2addr' == Yii::$app->request->post('scenario'))
        {
            $model->load(Yii::$app->request->post());
            $this->zip2addr($model);
        }

        elseif($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->addFlash('success',"保存しました");
            return $this->redirect(['index']);
        }

        return $this->render('form', [
            'model'      => $model,
        ]);
    }

    /**
     * delete a model
     */
    public function actionDelete($id=null)
    {
        $model = $this->findModel($id)->One();

        if($model->isNewRecord)
            throw new \yii\web\NotFoundHttpException();

        if(! $model->delete())
            Yii::$app->session->addFlash('success',"提携施設を削除できませんでした。システム担当者へご連絡ください");

        Yii::$app->session->addFlash('success',"提携施設を削除しました");

        return $this->redirect(['index']);
    }

    private function findModel($id=null)
    {
        $user  = Yii::$app->user->identity;
        if(isset($id)) {
            $model = Facility::find()->andWhere(['customer_id' => $user->id])->andWhere(['facility_id' => $id]);
        } else {
            $model = Facility::find()->andWhere(['customer_id' => $user->id]);
        }
        return $model;
    }

    private function zip2addr($model)
    {
        if('zip2addr' !== Yii::$app->request->post('scenario'))
            return false;

        if(! $hint = Zip::zip2addr($model->zip01, $model->zip02))
        {
            Yii::$app->session->addFlash('warning',"郵便番号({$model->zip})に対応する市区町村が見つかりません");
            return true;
        }

        $model->pref_id = $hint->pref_id;
        $model->addr01  = array_shift($hint->addr01);

        return true;
    }

}
