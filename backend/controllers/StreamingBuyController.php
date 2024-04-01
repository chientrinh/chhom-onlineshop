<?php

namespace backend\controllers;

use Yii;
use common\models\StreamingBuy;
use common\models\StreamingBuySearch;
use common\models\Streaming;
use common\models\ProductGrade;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StreamingController implements the CRUD actions for StramingBuy model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/StreamingBuyController.php $
 * $Id: StreamingBuyController.php 2286 2020-04-28 15:31:00Z kawai $
 */
class StreamingBuyController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'ライブ配信購入情報管理','url'=>['index']];

        return true;
    }

    /**
     * Lists all Streaming models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StreamingBuySearch();
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
        $model = new StreamingBuy();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->streaming_buy_id]);
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

            foreach ($param['StreamingBuy'] as $key => $value) {
                $model->$key = $value;                
            }

        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }

        if ($model->validate() && $model->save(false)) {
            return $this->redirect(['view', 'id' => $model->streaming_buy_id]);
        }
    }


    public function actionDelete($id)
    {

        $transaction = Yii::$app->db->beginTransaction();
        $model = $this->findModel($id);

        try {
            if($model->delete()) {
                $transaction->commit();
                return $this->redirect('index');
            } else {
                Yii::$app->session->addFlash('error', "削除できませんでした");
                return $this->redirect('index');
            }
        } catch (\yii\Exception $e)
        {
            Yii::error($e->__toString(), $this->className().'::'.__FUNCTION__);
            Yii::$app->session->addFlash('error',"削除中にシステムエラーが発生しました。システム管理者にご連絡ください。");
        }
        Yii::$app->session->addFlash('error', "削除できませんでした");
                return $this->redirect('index');
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
        if (($model = StreamingBuy::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
