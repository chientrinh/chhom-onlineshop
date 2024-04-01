<?php

namespace backend\controllers;

use Yii;
use common\models\ChangeLog;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ChangeLogController implements the CRUD actions for ChangeLog model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/ChangeLogController.php $
 * $Id: ChangeLogController.php 2727 2016-07-16 03:31:24Z mori $
 */
class ChangeLogController extends Controller
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => 'DB操作履歴', 'url'=>['index']];

        return true;
    }

    /**
     * Lists all ChangeLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new ChangeLog();
        $model->load(Yii::$app->request->get());

        $provider = $this->loadProvider($model);

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
        ]);
    }

    /**
     * Displays a single ChangeLog model.
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
     * Finds the ChangeLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ChangeLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(! $model = ChangeLog::findOne($id))
            throw new NotFoundHttpException('The requested page does not exist.');

        return $model;
    }

    private function loadProvider($model)
    {
        $query = ChangeLog::find();

        foreach($model->attributes as $attr => $value)
            $query->andFilterWhere(['like', $attr, $value]);

        return new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['create_date' => SORT_DESC]],
        ]);
    }

}
