<?php

namespace backend\controllers;

use Yii;
use common\models\AgencyRank;
use common\models\AgencyRankDetail;
use backend\models\SearchAgencyRank;
use backend\models\SearchAgencyRankDetail;
use common\models\Category;
use common\models\Subcategory;
use common\models\ProductMaster;
use common\models\ProductSubcategory;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use \yii\helpers\ArrayHelper;

/**
 * AgencyRankController
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/AgencyRankController.php $
 * $Id: AgencyRankController.php
 *
 */
class AgencyRankController extends BaseController
{
    /**
     * Lists all AgencyRankController models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => AgencyRank::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AgencyRankController model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $target='viewSubcategory')
    {
        if (method_exists($this, $target))
            return $this->$target($id);


        return $this->render('view', [
            'rank' => $this->findModel($id),
        ]);
    }

    public function viewSubCategory($id)
    {
        return $this->render('view', [
            'viewFile'     => 'view/_subcategory',
            'rank'     => $this->findModel($id),
            'dataProvider' => SearchAgencyRankDetail::searchSubCategories($id),
        ]);

    }

    public function viewProduct($id)
    {
        return $this->render('view', [
            'viewFile'     => 'view/_product',
            'rank'     => $this->findModel($id),
            'dataProvider' => SearchAgencyRankDetail::searchProducts($id),
        ]);

    }

    public function actionSearch()
    {
        $params         = Yii::$app->request->get();
        $options        = null;
        $products       = [];
        $productArray   = [];
        $target         = ArrayHelper::getValue($params, 'target', null);
        $category_id    = ArrayHelper::getValue($params, 'category_id', null);
        $subcategory_id = ArrayHelper::getValue($params, 'subcategory_id', null);


/*
        if(Yii::$app->request->isAjax)
            return \yii\helpers\Json::encode($params);

        return $paramss;
*/
        if((null === $target) || (null === $category_id && null === $subcategory_id))
            return false;

        if ($category_id)
            $products = ProductMaster::find()
                                    ->andWhere(['category_id' => $category_id]);
                                    //->all();

        if ($subcategory_id)
            $products = ProductMaster::find()
                            ->leftJoin(['sp' => ProductSubcategory::tableName()], ProductMaster::tableName(). '.ean13=sp.ean13')
                            ->andWhere(['sp.subcategory_id' => $subcategory_id]);
                            //->all();     

        if ($products) {
            foreach ($products->batch() as $array) {
                foreach($array as $key => $product){
                  $productArray[$product->sku_id] = $product->sku_id.'&nbsp;&nbsp;'.$product->name;
                }
            }


            $options = ArrayHelper::merge(
                                [0 => '未選択'], 
                                $productArray
                            );            

        }
        if(Yii::$app->request->isAjax)
            return \yii\helpers\Json::encode($options);

        return $options;
    }

    public function actionEdit($id, $target)
    {
        $rank = $this->findModel($id);


        if ($target == 'subcategory')
            return $this->updateSubCategory($rank);

        if ($target == 'product')
            return $this->updateProduct($rank);

        throw new \yii\web\NotFoundHttpException("$target unknown");
    }

    private function updateSubCategory($rank) 
    {
        
        $get = Yii::$app->request->get();
       
        $agencyRankDetails = AgencyRankDetail::find()
                        ->andWhere(['rank_id' => $rank->rank_id])
                        ->andWhere(['subcategory_id' => $get['subcategory_id']])->one();
 

        if(! $agencyRankDetails)
            throw new NotFoundHttpException("対象のデータが見つかりません。初めからやり直してください。");

        if($agencyRankDetails->load(Yii::$app->request->post()) && $agencyRankDetails->save())
            return $this->redirect(['view', 'id' => $agencyRankDetails->rank_id, 'target'=>'viewSubCategory']);

        return $this->render('_form-subcategory', [
                'rank'   => $rank,
                'agencyRankDetails' => $agencyRankDetails,
                'subcategories' => Subcategory::find()->all(),
            ]);

    }

 
    private function updateProduct($rank) 
    {
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post('AgencyRankDetail');

        $agencyRankDetails = AgencyRankDetail::find()
                        ->andWhere(['rank_id' => $rank->rank_id])
                        ->andWhere(['sku_id' => $get['sku_id']])->one();

        if(! $agencyRankDetails)
            throw new NotFoundHttpException("対象のデータが見つかりません。初めからやり直してください。");

        if($agencyRankDetails->load(Yii::$app->request->post()) && $agencyRankDetails->save())
            return $this->redirect(['view', 'id' => $agencyRankDetails->rank_id, 'target'=>'viewProduct']);

        return $this->render('_form-product', [
                'rank'        => $rank,
                'agencyRankDetails' => $agencyRankDetails,
                'categories'      => Category::find()->all(),
                'subcategories'   => Subcategory::find()->all(),
            ]);

    }


    /**
     * Creates a new AgencyRankController model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AgencyRank();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', 'ランク情報を追加しました');
            return $this->redirect(['view', 'id' => $model->rank_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AgencyRankController model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', 'ランク情報を編集しました');
            return $this->redirect(['view', 'id' => $model->rank_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        try {
            $transaction = Yii::$app->db->beginTransaction();

            $model = AgencyRank::findOne($id);
            AgencyRankDetail::deleteAll(['rank_id' => $model->rank_id]);

            if ($model->delete()) {
                Yii::$app->session->addFlash('success', 'ランク情報を削除しました');
            } else {
                Yii::warning($sales->errors);
                $transaction->rollBack();
                return false;
            }
                
        }
        catch (Exception $e)
        {
            Yii::warning($e->__toString(), $this->className().'::'.__FUNCTION__);
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();

        return $this->redirect(['index']);
    }

    public function actionAdd($id, $target)
    {
        $model = $this->findModel($id);

        if ($target == 'subcategory')
            return $this->addSubCategory($model);

        if ($target == 'product')
            return $this->addProduct($model);
    }

    private function addSubCategory($model) 
    {
        $agencyRankDetails = new AgencyRankDetail(['rank_id' => $model->rank_id]);

        if($agencyRankDetails->load(Yii::$app->request->post()) && $agencyRankDetails->save())
            $agencyRankDetails = new AgencyRankDetail(['rank_id' => $model->rank_id]);


        return $this->render('_form-subcategory', [
                'rank'        => $model,
                'agencyRankDetails' => $agencyRankDetails,
                'subcategories'   => Subcategory::find()->where(['company_id' => 2])->all(),
            ]);

    }

    private function addProduct($model) 
    {
        $agencyRankDetails = new AgencyRankDetail(['rank_id' => $model->rank_id]);
        if($agencyRankDetails->load(Yii::$app->request->post()) && $agencyRankDetails->save())
            $agencyRankDetails = new AgencyRankDetail(['rank_id' => $model->rank_id]);

        return $this->render('_form-product', [
                'rank'        => $model,
                'agencyRankDetails' => $agencyRankDetails,
                //'categories'      => Category::find()->all(),
                'subcategories'   => Subcategory::find()->where(['company_id' => 2])->all(),
            ]);

    }

    public function actionDel($id, $target)
    {
        $rank = $this->findModel($id);
        $get = Yii::$app->request->get();


        if ($target == 'subcategory') {
            $keyName  = 'subcategory_id';
            $viewName = 'viewSubCategory';
        }

        elseif ($target == 'product') {
            $keyName  = 'sku_id';
            $viewName = 'viewProduct';
        }

        else 
            throw new \yii\web\NotFoundHttpException("$target unknown");


        $agencyRankDetails = AgencyRankDetail::find()
                        ->andWhere(['rank_id' => $rank->rank_id])
                        ->andWhere([$keyName => Yii::$app->request->get($keyName)])
                        ->one();


        if(! $agencyRankDetails)
            throw new NotFoundHttpException("対象のデータが見つかりません。初めからやり直してください。");

        $agencyRankDetails->delete();
        
        return $this->redirect(['view', 'id' => $rank->rank_id, 'target' => $viewName]);
    }



    /**
     * Finds the AgencyRankController model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AgencyRankController the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = AgencyRank::findOne($id);

        if(! $model)
            throw new NotFoundHttpException("当該IDは見つかりません({$id})");

        return $model;

    }
}
