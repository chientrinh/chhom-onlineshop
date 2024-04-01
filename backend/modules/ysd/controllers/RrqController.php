<?php

namespace backend\modules\ysd\controllers;

use Yii;
use common\models\ysd\RegisterRequest;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/views/rrs/view.php $
 * @version $Id: view.php 1961 2016-01-11 01:39:26Z mori $
 *
 * RrqController implements the CRUD actions for RegisterRequest model.
 */
class RrqController extends \backend\controllers\BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'登録依頼','url'=>['index']];

        return true;
    }

    /**
     * Lists all RegisterRequest models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model  = new RegisterRequest();
        $model->load(Yii::$app->request->get());

        $provider = $this->loadProvider($model);

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
        ]);
    }

    /**
     * Displays a single RegisterRequest model.
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
     * Finds the RegisterRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RegisterRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RegisterRequest::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function loadProvider(RegisterRequest $model)
    {
        $query = RegisterRequest::find()
            ->andFilterWhere($model->attributes);

        return new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'enableMultiSort' => true,
                'defaultOrder' => ['created_at'=>SORT_DESC],
            ],
        ]);
    }
}
