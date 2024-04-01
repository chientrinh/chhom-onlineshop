<?php

namespace backend\controllers;

use Yii;
use common\models\Membercode;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MembercodeController implements the CRUD actions for Membercode model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/MembercodeController.php $
 * $Id: MembercodeController.php 1911 2015-12-23 02:46:18Z mori $
 */
class MembercodeController extends BaseController
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
     * Lists all Membercode models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new \common\models\Membercode();
        $searchModel->load(Yii::$app->request->get());

        $dataProvider = self::loadProvider($searchModel);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * Displays a single Membercode model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Membercode model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Membercode();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->code]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Membercode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->code]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Membercode model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        throw new \yii\web\ForbiddenHttpException("会員証NOは削除できません");
    }

    /**
     * Finds the Membercode model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Membercode the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Membercode::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private static function loadProvider($model)
    {
        $query = Membercode::find()
           ->andFilterWhere(['AND',
                             ['like','code',       $model->code      ],
                             ['like','customer_id',$model->customer_id],
                             ['like','pw',         $model->pw        ],
                             ['like','directive',  $model->directive ],
                             ['like','migrate_id', $model->migrate_id],
                             ['status'          => $model->status    ],
           ]);

        return new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['enableMultiSort' => true],
        ]);
    }

}
