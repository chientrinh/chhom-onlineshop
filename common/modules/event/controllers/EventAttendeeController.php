<?php

namespace backend\controllers;

use Yii;
use common\models\EventAttendee;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventAttendeeController implements the CRUD actions for EventAttendee model.
 */
class EventAttendeeController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all EventAttendee models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => EventAttendee::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EventAttendee model.
     * @param integer $venue_id
     * @param integer $customer_id
     * @return mixed
     */
    public function actionView($venue_id, $customer_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($venue_id, $customer_id),
        ]);
    }

    /**
     * Creates a new EventAttendee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EventAttendee();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'venue_id' => $model->venue_id, 'customer_id' => $model->customer_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EventAttendee model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $venue_id
     * @param integer $customer_id
     * @return mixed
     */
    public function actionUpdate($venue_id, $customer_id)
    {
        $model = $this->findModel($venue_id, $customer_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'venue_id' => $model->venue_id, 'customer_id' => $model->customer_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EventAttendee model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $venue_id
     * @param integer $customer_id
     * @return mixed
     */
    public function actionDelete($venue_id, $customer_id)
    {
        $this->findModel($venue_id, $customer_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EventAttendee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $venue_id
     * @param integer $customer_id
     * @return EventAttendee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($venue_id, $customer_id)
    {
        if (($model = EventAttendee::findOne(['venue_id' => $venue_id, 'customer_id' => $customer_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
