<?php

namespace backend\controllers;

use Yii;
use common\models\Streaming;
use common\models\StreamingSearch;
use common\models\StreamingBuy;
use common\models\ProductGrade;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StreamingController implements the CRUD actions for Straming model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/StreamingController.php $
 * $Id: StreamingController.php 2286 2016-03-21 06:11:00Z mori $
 */
class StreamingController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'ライブ配信管理','url'=>['index']];

        return true;
    }

    /**
     * Lists all Streaming models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StreamingSearch();
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
        $model = new Streaming();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($model->save()) {
                return $this->redirect(['view', 'id' => $model->streaming_id]);
            } else {
                Yii::$app->session->addFlash('warning', print_r($model->errors, true));
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
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

            foreach ($param['Streaming'] as $key => $value) {
                $model->$key = $value;                
            }
            // $model->load($param);

        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }

        if ($model->validate() && $model->save(false)) {
            return $this->redirect(['view', 'id' => $model->streaming_id]);
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
        if (($model = Streaming::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
