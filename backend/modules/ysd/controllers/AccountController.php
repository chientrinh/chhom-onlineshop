<?php
namespace backend\modules\ysd\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/controllers/DefaultController.php $
 * $Id: DefaultController.php 1961 2016-01-11 01:39:26Z mori $
 */
use Yii;
use \common\models\ysd\Account;

class AccountController extends \backend\controllers\BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'口座','url'=>'index'];

        return true;
    }

    public function actionIndex()
    {
        $searchModel = new Account();
        $params = Yii::$app->request->queryParams;
        $searchModel->load($params);

        $provider = $this->loadProvider($searchModel);

        return $this->render('index', [
            'provider' => $provider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view',['model'=>$model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->addFlash('success', "変更されました");
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('update',['model'=>$model]);
    }

    private function findModel($id)
    {
        return Account::findOne(['customer_id'=>$id]);
    }
    
    protected function loadProvider($searchModel)
    {
        $query = Account::find()->andFilterWhere(['AND',
            ['customer_id' => $searchModel->customer_id],
            ['expire_id' => $searchModel->expire_id],
        ]);
        
        if($searchModel->credit_limit)
            $query->andFilterWhere(['AND','credit_limit LIKE \''.$searchModel->credit_limit.'%\'']); // 前方一致;
                
        return new \yii\data\ActiveDataProvider([
            'query'=>$query,
        ]);
    }
}
