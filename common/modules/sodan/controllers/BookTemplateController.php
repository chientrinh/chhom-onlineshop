<?php

namespace common\modules\sodan\controllers;

use Yii;
use common\models\Customer;
use common\models\sodan\BookTemplate;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use backend\models\Staff;
use common\models\Branch;

/**
 * BookTemplateController implements the CRUD actions for BookTemplate model.
 */
class BookTemplateController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => '予約票テンプレート', 'url' => ['index']];
        return true;
    }

    /**
     * Lists all BookTemplate models.
     * @return mixed
     */
    public function actionIndex()
    {
        $param = Yii::$app->request->queryParams;
        $model = new BookTemplate();
        $model->load($param);
        $provider = new ActiveDataProvider([
            'query' => BookTemplate::find()->andFilterWhere($model->dirtyAttributes)
        ]);

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
        ]);
    }

    /**
     * Displays a single BookTemplate model.
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
     * Creates a new BookTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BookTemplate();

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view', 'id' => $model->template_id]);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing WaitList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view', 'id' => $model->template_id]);

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the BookTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WaitList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = BookTemplate::findOne($id);
        if(! $model)
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');

        return $model;
    }
}
