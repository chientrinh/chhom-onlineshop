<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/RemedyPotencyController.php $
 * $Id: RemedyPotencyController.php 1157 2015-07-15 13:01:02Z mori $
 */

namespace backend\controllers;

use Yii;
use common\models\RemedyPotency;
use common\models\SearchRemedyPotency;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\SearchRemedyCategoryDescription;
use common\models\RemedyCategoryDescription;

/**
 * RemedyCategoryDescriptionController implements the CRUD actions for RemedyCategoryDescription model.
 */
class RemedyCategoryDescriptionController extends BaseController
{

    protected $backUrl;

    /**
     * 事前処理チェック・パンくず作成
     *
     * @param unknown $action
     * @return boolean
     */
    public function beforeAction($action)
    {
        if (! parent::beforeAction($action))
            return false;

        $this->backUrl = Yii::$app->request->getReferrer();

        $this->view->params['breadcrumbs'][] = ['label' => 'レメディー共通補足説明', 'url' => ['index']];
        return true;
    }

    /**
     * Lists all RemedyPotency models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchRemedyCategoryDescription();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RemedyPotency model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new RemedyPotency model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RemedyCategoryDescription();

        if (Yii::$app->request->isPost) {

            $model->load ( Yii::$app->request->post () );

            // 説明区分が「広告」の場合、カテゴリーIDを0に表示順はデフォルトに設定
            if ($model->isAd ()) {
                $model->title = null; // DB上に見出しが登録されていた場合でも初期化する（バックヤードでの混乱を防ぐため）
            }

            if ($model->save ()) {
                // 処理成功時
                return $this->redirect(['index']);

            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing RemedyPotency model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

         if (Yii::$app->request->isPost) {

            $model->load ( Yii::$app->request->post () );

            // 説明区分が「広告」の場合、カテゴリーIDを0に表示順はデフォルトに設定
            if ($model->isAd ()) {
                $model->title = null; // DB上に見出しが登録されていた場合でも初期化する（バックヤードでの混乱を防ぐため）
            }

            if ($model->save ()) {
                // 処理成功時
                return $this->redirect(['index']);

            }
        }

        return $this->render('update', [
            'model' => $model
        ]);

    }

    /**
     * Updates an existing RemedyPotency model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the RemedyPotency model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RemedyPotency the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RemedyCategoryDescription::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
