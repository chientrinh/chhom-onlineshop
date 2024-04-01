<?php

namespace backend\controllers;

use Yii;
use common\models\ProductDescription;
use common\models\ProductMaster;
use common\models\SearchProductDescription;
use yii\web\NotFoundHttpException;

/**
 * ProductDescriptionController implements the CRUD actions for ProductDescription model.
 */
class ProductDescriptionController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => '商品', 'url' => ['/product/index']];
        $this->view->params['breadcrumbs'][] = ['label' => '補足', 'url' => ['index']];

        return true;
    }

    /**
     * Lists all ProductDescription models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchProductDescription();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->with('product');

        if(! Yii::$app->user->can('viewProduct')) // user is tenant, show only their own products
            $dataProvider->query
                      ->innerJoin(ProductMaster::tableName().' m',
                                  'm.product_id = mtb_product_description.product_id')
                      ->andWhere(['m.company_id' => Yii::$app->user->identity->company_id]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ProductDescription model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $product = \common\models\Product::findOne($id);
        if(! $product)
            throw new \yii\web\BadRequestHttpException("Invalid product_id: ". $id);

        $model = new ProductDescription();
        $model->product_id = $product->product_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['product/view', 'id' => $model->product->product_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ProductDescription model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['product/view', 'id' => $model->product->product_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->delete())
            Yii::$app->session->addFlash('success', "{$model->product->name}の補足「{$model->title}」を削除しました");
            return $this->redirect(['product/update', 'id' => $model->product->product_id]);

        Yii::$app->session->addFlash('error', "{$model->product->name}の補足「{$model->title}」を削除できません、システム担当者へ連絡してください");
        return $this->render(['update', 'model' => $model]);
    }

    /**
     * Finds the ProductDescription model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductDescription the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(! $model = ProductDescription::findOne($id))
            throw new NotFoundHttpException('The requested page does not exist.');

        $p = $model->product;
        if(! Yii::$app->user->can('viewProduct',['company_id'=>$p->company->company_id]))
            throw new \yii\web\ForbiddenHttpException(sprintf(
                "指定モデルの所有権がありません [model.company_id != user.company_id] (%d != %d)",
                $p->company->company_id,
                Yii::$app->user->identity->company_id)
            );

        if(in_array($this->action->id, ['update','expire','activate']))
            if(! Yii::$app->user->can('updateProduct',['company_id' => $p->company->company_id]))
                throw new \yii\web\ForbiddenHttpException(
                    "指定モデルを編集する権限がありません"
                );

        return $model;
    }
}
