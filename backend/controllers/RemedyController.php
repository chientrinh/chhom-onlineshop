<?php

namespace backend\controllers;

use Yii;
use common\models\Remedy;
use common\models\SearchRemedy;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RemedyController implements the CRUD actions for Remedy model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/RemedyController.php $
 * $Id: RemedyController.php 1573 2015-10-01 13:38:04Z mori $
 */
class RemedyController extends BaseController
{
    /**
     * Lists all Remedy models.
     * @return mixed
     */
    public function actionIndex($format='html',$pagination='true')
    {
        $model = new SearchRemedy();
        $provider = $model->search(Yii::$app->request->queryParams);
        $provider->query->orderBy('abbr ASC');

        if('true' !== $pagination)
            $provider->pagination = false;
      
        return $this->render('index', [
            'format'   => $format,
            'model'    => $model,
            'provider' => $provider,
        ]);
    }

    /**
     * Displays a single Remedy model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionViewbyname($name)
    {
        $model = $this->findModelByName($name);

        if($name != $model->abbr)
            return $this->redirect(['viewbyname','name'=>$model->abbr]);

        return $this->render('view', ['model'=>$model]);
    }

    /**
     * Creates a new Remedy model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Remedy();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->remedy_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Remedy model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->remedy_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
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
            $model->on_sale = 0;
            if($model->save())
                return $this->redirect(['view','id'=>$id]);
        }
    }

    /**
     * Finds the Remedy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Remedy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Remedy::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    protected function findModelByName($abbr)
    {
        $model = Remedy::findOne(['abbr'=>$abbr]);
        if(! $model)
            $model = Remedy::find()->where(['like','abbr',$abbr.'%',false])->one();

        if(! $model)
            throw new NotFoundHttpException('The requested page does not exist.');

        return $model;
    }
}
