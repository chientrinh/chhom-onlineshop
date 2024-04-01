<?php

namespace backend\controllers;

use Yii;
use common\models\Company;
use common\models\Subcategory;
use common\models\SearchSubcategory;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/SubcategoryController.php $
 * $Id: SubcategoryController.php 3268 2017-04-21 01:45:07Z kawai $
 *
 * SubcategoryController implements the CRUD actions for Subcategory model.
 */
class SubcategoryController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => 'サブカテゴリー','url'=>['index']];

        return true;
    }

    /**
     * Lists all Subcategory models.
     * @return mixed
     */
    public function actionMap($company_id = null)
    {
        $html  = [];
        $query = \common\models\Company::find()->andFilterWhere(['company_id'=>$company_id]);

        if(! Yii::$app->user->can('viewProduct'))// user is tenant
            $query->andFilterWhere(['company_id' => Yii::$app->user->identity->company_id]);

        foreach($query->all() as $company)
            $html[] = $this->renderPartial('map',[
                             'company'=>$company,
                             'dataProvider'=> new \yii\data\ActiveDataProvider([
                                 'query' => \common\models\Subcategory::find()
                                     ->orderBy('weight DESC, subcategory_id ASC')
                                     ->andWhere([
                                     'company_id'=> $company->company_id,
                                     'parent_id' => null,
                                 ]),
                                 'pagination' => false,
                                 'sort' => [ 'defaultOrder' => ['company_id' => SORT_ASC] ],
                             ]),
                      ]);
        
        return $this->renderContent(implode('',$html));
    }

    public function actionIndex($company_id = null)
    {
        $searchModel = new SearchSubcategory(['company_id'=>$company_id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->with('parent');

        Url::remember(Url::current(), $this->id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Subcategory model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        Url::remember(Url::current(), $this->id);

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Subcategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($company_id)
    {
        $model = new Subcategory(['company_id'=>$company_id]);

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view', 'id' => $model->subcategory_id]);

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing Subcategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->subcategory_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionMoveItem($id, $offset=1)
    {
        $model = $this->findModel($id);
        $model->weight += $offset;
        $model->save();

        return $this->redirect(Url::previous($this->id));
    }

    /**
     * Deletes an existing Subcategory model.
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
     * Finds the Subcategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Subcategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(! $model = Subcategory::findOne($id))
            throw new NotFoundHttpException('The requested page does not exist.');

        if(! Yii::$app->user->can('viewProduct',['company_id'=>$model->company_id]))
            throw new \yii\web\ForbiddenHttpException(sprintf(
                "指定モデルの所有権がありません [model.company_id != user.company_id] (%d != %d)",
                $model->company_id,
                Yii::$app->user->identity->company_id)
            );

        if(in_array($this->action->id, ['update','move-item','delete']))
            if(! Yii::$app->user->can('updateProduct',['company_id' => $model->company_id]))
                throw new \yii\web\ForbiddenHttpException(
                    "指定モデルを編集する権限がありません"
                );

        return $model;
    }

    private function loadProvider()
    {
        $query = Subcategory::find()->where(['parent_id'=>null]);

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [ 'defaultOrder' => ['company_id' => SORT_ASC,'weight'=> SORT_DESC] ],
        ]);
        return $provider;
    }
}
