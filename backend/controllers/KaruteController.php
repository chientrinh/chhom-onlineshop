<?php

namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use \common\models\webdb20\Karute;

/**
 * KaruteController implements the CRUD actions for Karute model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/KaruteController.php $
 * $Id: KaruteController.php 2276 2016-03-20 06:58:20Z mori $
 */
class KaruteController extends BaseController
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
        ]);
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'カルテ','url'=>['index']];

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
        $searchModel  = new Karute(['scenario'=>'search']);
        $searchModel->load(Yii::$app->request->queryParams);
        $dataProvider = $this->loadProvider($searchModel);

        return $this->render('index', [
            'searchModel'  => $searchModel,
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
     * Displays a single Karute with all KaruteItems
     * @param integer $id
     * @return mixed
     */
    public function actionPrint($id)
    {
        return $this->render('print', [
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
        $model = new Karute();

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
     * Finds the Karute model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Karute the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Karute::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function loadProvider(Karute $model)
    {
        $query = $model->find()->andFilterWhere(['AND',
                ['karuteid'          => $model->karuteid],
                ['customerid'        => $model->customerid],
                ['syoho_homeopathid' => $model->syoho_homeopathid],
                ['like', 'karute_date', $model->karute_date],
                ['like', 'karute_syuso', mb_convert_encoding($model->karute_syuso, 'CP51932', 'UTF-8')],
        ]);

        return new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]);
    }

}
