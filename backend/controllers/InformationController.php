<?php

namespace backend\controllers;

use Yii;
use common\models\Information;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InformationController implements the CRUD actions for Information model.
 */
class InformationController extends BaseController
{

    /**
     * Lists all Information models.
     * @return mixed
     */
    public function actionIndex($expired=false)
    {
        if((int) $expired)
            $query = Information::find()->expired();
        else
            $query = Information::find()->expired(false);

        $dataProvider = new ActiveDataProvider([ 'query' => $query ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Information model.
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
     * Creates a new Information model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Information();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->info_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Information model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->info_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionActivate($id)
    {
        $model = $this->findModel($id);
        if(! $model->isExpired())
            throw new yii\web\BadRequestHttpException("The record is already activated");

        $model->expire_date = null;
        if($model->save())
            return $this->redirect(['view', 'id'=>$id]);

        return 'activation failed' . \yii\helpers\VarDumper::dump($model->errors);
    }

    public function actionExpire($id)
    {
        $model = $this->findModel($id);
        $model->expire_date = new \yii\db\Expression('NOW()');
        if($model->save())
            return $this->redirect(['view', 'id'=>$id]);

        return 'save failed' . \yii\helpers\VarDumper::dump($model->errors);
    }

    /**
     * Finds the Information model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Information the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Information::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
