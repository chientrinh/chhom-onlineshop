<?php

namespace backend\controllers;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/OfferController.php $
 * @version $Id: OfferController.php 2891 2016-09-29 01:23:00Z mori $
 */

use Yii;
use common\models\Offer;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OfferController implements the CRUD actions for Offer model.
 */
class OfferController extends Controller
{
    public $title = 'ご優待(初期値)';

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->initViewParams($action);

        return true;
    }

    /**
     * Lists all Offer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Offer();
        $query = Offer::find();

        $model->load(Yii::$app->request->get());
        $query->andFilterWhere($model->getAttributes());

        $dataProvider = new ActiveDataProvider([
            'query' => $query->with(['category','grade','category.seller'])
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $model,
        ]);
    }

    /**
     * Displays a single Offer model.
     * @param integer $category_id
     * @param integer $grade_id
     * @return mixed
     */
    public function actionView($category_id, $grade_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($category_id, $grade_id),
        ]);
    }

    /**
     * Creates a new Offer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Offer();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'category_id' => $model->category_id, 'grade_id' => $model->grade_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Offer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $category_id
     * @param integer $grade_id
     * @return mixed
     */
    public function actionUpdate($category_id, $grade_id)
    {
        $model = $this->findModel($category_id, $grade_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'category_id' => $model->category_id, 'grade_id' => $model->grade_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the Offer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $category_id
     * @param integer $grade_id
     * @return Offer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($category_id, $grade_id)
    {
        if (($model = Offer::findOne(['category_id' => $category_id, 'grade_id' => $grade_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function initViewParams($action)
    {
        if('index' !== $action->id)
            $this->view->params['breadcrumbs'][] = ['label'=>$this->title,'url'=>['index']];
    }

}
