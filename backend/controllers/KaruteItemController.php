<?php

namespace backend\controllers;

use Yii;
use common\models\webdb20\KaruteItem;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * KaruteItemController implements the CRUD actions for KaruteItem model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/KaruteItemController.php $
 * $Id: KaruteItemController.php 2276 2016-03-20 06:58:20Z mori $
 */
class KaruteItemController extends BaseController
{
    public function init()
    {
        parent::init();
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['worker'],
                    ],
                ],
            ],
            [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'子カルテ','url'=>['index']];

        if('index' == $action->id)
            $this->view->params['breadcrumbs'][] = ['label'=>'一覧'];
        if('view' == $action->id)
            $this->view->params['breadcrumbs'][] = ['label'=>'詳細'];
        if('create' == $action->id)
            $this->view->params['breadcrumbs'][] = ['label'=>'追加'];
        if('update' == $action->id)
            $this->view->params['breadcrumbs'][] = ['label'=>'修正'];

        return true;
    }

    /**
     * Lists all Karute models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => KaruteItem::find()->orderBy('syohoid DESC'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Karute model.
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
     * Creates a new Karute model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new KaruteItem();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->karuteid]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Karute model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->karuteid]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Karute model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Karute model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Karute the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = KaruteItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
