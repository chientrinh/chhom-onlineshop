<?php
namespace backend\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/OfferSeasonalController.php $
 * $Id: OfferSeasonalController.php 3855 2018-04-27 02:01:14Z mori $
 */

use Yii;
use common\models\OfferSeasonal;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * OfferSeasonalController implements the CRUD actions for OfferSeasonal model.
 */
class OfferSeasonalController extends BaseController
{
    public $title = 'ご優待';

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => '商品', 'url' => ['/product/index']];
        $this->view->params['breadcrumbs'][] = ['label' => 'ご優待', 'url' => ['index']];

        return true;
    }

    /**
     * Lists all OfferSeasonal models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => OfferSeasonal::find()->joinWith('master')
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OfferSeasonal model.
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
     * Creates a new OfferSeasonal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OfferSeasonal();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            $product_id = $model->getMaster()
                                ->select('product_id')
                                ->scalar();

            if ($product_id) {
                return $this->redirect(['/product/view', 'id' => $product_id, 'target'=>'offer']);
            } else {
                return $this->redirect(['index']);
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OfferSeasonal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->seasonal_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if($model->delete())
            Yii::$app->session->addFlash('success',sprintf("<strong>%s</strong> のご優待を1件 削除しました",($m = $model->master) ? $m->name : null));
        else
            Yii::$app->session->addFlash('error',"削除できませんでした");

        return $this->redirect(['index']);
    }

    /**
     * Finds the OfferSeasonal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OfferSeasonal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OfferSeasonal::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
