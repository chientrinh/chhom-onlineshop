<?php

namespace backend\controllers;

use Yii;
use common\models\LiveInfo;
use common\models\LiveInfoSearch;
use common\models\Product;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StreamingController implements the CRUD actions for Straming model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/LiveInfoController.php $
 * $Id: LiveItemInfoController.php 2286 2016-03-21 06:11:00Z mori $
 */
class LiveInfoController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'ライブ配信追加情報','url'=>['index']];

        return true;
    }

    /**
     * Lists all Streaming models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LiveInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Streaming model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Streaming model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LiveInfo();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->info_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Streaming model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $param = Yii::$app->request->post();

        if($param)
        {

            foreach ($param['LiveInfo'] as $key => $value) {
                $model->$key = $value;                
            }
            // $model->load($param);

        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }

        if(! $model->validate()) // 注意喚起のため、どこがおかしいか表示する
            Yii::$app->session->addFlash('error', \yii\helpers\Html::errorSummary($model));

        if ($model->validate() && $model->save(false)) {
            return $this->redirect(['view', 'id' => $model->info_id]);
        }
        // return to the last page the user was on.
        return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * Expires an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model)
        {
            if($model->delete()) {
                return $this->redirect(['index']);
            } else {
                throw new NotFoundHttpException('削除時にエラーが発生したか、すでに存在しないレコードです');
            }                
        }
    }

    /**
     * Finds the Streaming model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Streaming the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LiveInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
