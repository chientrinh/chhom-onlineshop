<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/StaffRoleController.php $
 * $Id: StaffRoleController.php 2337 2016-03-31 01:42:46Z mori $
 */

namespace backend\controllers;

use Yii;
use \yii\web\NotFoundHttpException;
use \backend\models\StaffRole;
use \backend\models\SearchStaffRole;

/**
 * StaffController implements the CRUD actions for Staff model.
 */
class StaffRoleController extends BaseController
{

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => '従業員', 'url' => ['/staff/index']];
        $this->view->params['breadcrumbs'][] = ['label' => '役割',   'url' => ['/role/index']];
        $this->view->params['breadcrumbs'][] = ['label' => '全体を一覧',   'url' => ['index']];

        return true;
    }

    /**
     * Lists all StaffRole models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchStaffRole();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new StaffRole model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new StaffRole(['staff_id'=>$id]);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->authManager->revokeAll($id);

            return $this->redirect(['staff/view', 'id' => $id ]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    private function afterSave()
    {
        return $this->redirect(['staff/view', 'id' => $id ]);
        Yii::$app->authManager->refresh($id);
    }
    /**
     * Updates an existing StaffRole model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$ro,$br=null)
    {
        $model = StaffRole::find()->where([
            'staff_id'  => $id,
            'role_id'   => $ro,
            'branch_id' => $br,
        ])->one();

        if(! $model)
            throw new NotFoundHttpException('The requested page does not exist.');

        if($model->delete())
        {
            Yii::$app->authManager->revokeAll($id);

            Yii::$app->session->addFlash('success','役割を削除しました');
        }

        return $this->redirect(['staff/view', 'id' => $id ]);
    }

    /**
     * Finds the StaffRole model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Staff the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(! $model = StaffRole::findOne($id))
            throw new NotFoundHttpException('The requested page does not exist.');

        return $model;
    }
}
