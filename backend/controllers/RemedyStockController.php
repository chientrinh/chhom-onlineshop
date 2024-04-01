<?php

namespace backend\controllers;

use Yii;
use common\models\ProductMaster;
use common\models\Remedy;
use common\models\RemedyStock;
use common\models\RemedyStockJan;
use common\models\RemedyVial;
use common\models\SearchRemedyStock;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/RemedyStockController.php $
 * $Id: RemedyStockController.php 4231 2020-02-05 05:34:50Z mori $
 *
 * RemedyStockController implements the CRUD actions for RemedyStock model.
 */
class RemedyStockController extends BaseController
{
    public $label = "既製レメディー";

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => "レメディー",   'url' => ['/remedy/index']];

        return true;
    }

    /**
     * Lists all RemedyStock models.
     * @return mixed
     */
    public function actionIndex($format='html')
    {
        $model    = new SearchRemedyStock();
        $provider = $model->search(Yii::$app->request->queryParams);

        if('csv' == $format)
        {
            $output = 'remedy-stock-index.csv';
            $inline = true;
            $mime   = 'text/csv';
            Yii::$app->response->setDownloadHeaders(basename($output), $mime, $inline);
            Yii::$app->response->send();
            return \common\widgets\CsvView::widget([
                'query'      => $provider->query,
                'charset'    => Yii::$app->charset,
                'eol'        => "<br>\n",
                'attributes' => [
                    'barcode',
                    'pickcode',
                    'remedy.abbr',
                    'potency.name',
                    'prange.name',
                    'vial.name',
                    'on_sale' => function($data){ return $data->remedy->on_sale ? "OK" : "NG"; },
                    'restriction.name',
                    'price',
                    'in_stock' => function($data){ return $data->in_stock ? "OK" : "NG"; },
                ],
            ]);
        }

        return $this->render('index', [
            'searchModel'  => $model,
            'dataProvider' => $provider,
        ]);
    }

    /**
     * Displays a single RemedyStock model.
     * @param integer $remedy_id
     * @param integer $potency_id
     * @param integer $vial_id
     * @return mixed
     */
    public function actionView($remedy_id, $potency_id, $vial_id)
    {
        $model = $this->findModel($remedy_id, $potency_id, $vial_id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new RemedyStock model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($remedy_id, $potency_id, $vial_id)
    {
        $model = RemedyStock::findOne([
                'remedy_id'  => $remedy_id,
                'potency_id' => $potency_id,
                'vial_id'    => $vial_id
            ]);

        if($model)
            throw new \yii\web\BadRequestHttpException(sprintf("すでに登録されています: %s, %s, %s",
                                                               $model->remedy->name,
                                                               $model->potency->name,
                                                               $model->vial->name)
            );

        $restrict_id = (int) Remedy::find()->where(['remedy_id' => $remedy_id])->select('restrict_id')->scalar();
        $prange_id   = (int) RemedyStock::find()->where(['remedy_id' => $remedy_id])->min('prange_id');
        $prange_id   = (int) RemedyStock::find()->where(['remedy_id' => $remedy_id,'potency_id'=>$potency_id])->min('prange_id');
        $model       = new RemedyStock(['remedy_id'  => $remedy_id,
                                        'potency_id' => $potency_id,
                                        'vial_id'    => $vial_id,
                                        'prange_id'  => $prange_id,
                                        'restrict_id'=> $restrict_id ]);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            self::productMasterSync($model);

            return $this->redirect(['view',
                                    'remedy_id'  => $model->remedy_id,
                                    'potency_id' => $model->potency_id,
                                    'vial_id'    => $model->vial_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing RemedyStock model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $remedy_id
     * @param integer $potency_id
     * @param integer $vial_id
     * @return mixed
     */
    public function actionUpdate($remedy_id, $potency_id, $vial_id)
    {
        $model = $this->findModel($remedy_id, $potency_id, $vial_id);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            self::productMasterSync($model);

            return $this->redirect(['view',
                                    'remedy_id'  => $model->remedy_id,
                                    'potency_id' => $model->potency_id,
                                    'vial_id'    => $model->vial_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing RemedyStock model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $remedy_id
     * @param integer $potency_id
     * @param integer $vial_id
     * @return mixed
     */
    public function actionDelete($remedy_id, $potency_id, $vial_id)
    {
        $transaction = Yii::$app->db->beginTransaction();

        $model = $this->findModel($remedy_id, $potency_id, $vial_id);
        $model->delete();

        // JANコードを紐付けた既製品を更新した場合、mtb_remedy_stock_janを削除し、ProductMasterのean13をSKU_IDに戻す
        if($model->sku_id != $model->barcode) {
            if($productMaster = ProductMaster::findOne(['ean13' => $model->barcode])) {
                $productMaster->ean13 = $model->sku_id;
                $productMaster->save();
            }

            $query = RemedyStockJan::find()->where(['sku_id' => $model->sku_id]);

            if((1 == $query->count())   &&
               ($remedy_jan = $query->one()) &&
               $remedy_jan->delete() ) {
            } else {
                $transaction->rollback();
                throw new \yii\web\NotFoundHttpException("JANコード削除時にエラーが発生しました。詳細はシステム部へお問い合わせください。".$model->errors);
            }

        }


        self::productMasterSync($model);

        $transaction->commit();

        return $this->redirect(['/remedy/view','id'=>$remedy_id]);
    }

    /**
     * Finds the RemedyStock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $remedy_id
     * @param integer $potency_id
     * @param integer $vial_id
     * @return RemedyStock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($remedy_id, $potency_id, $vial_id)
    {
        $model = RemedyStock::findOne([
            'remedy_id'  => $remedy_id,
            'potency_id' => $potency_id,
            'vial_id'    => $vial_id
        ]);

        if(! $model)
            throw new NotFoundHttpException('The requested page does not exist.');

        return $model;
    }

    /**
     * ProductMasterSyncronizer::updateRow(),deleteRow() の代わりの処理
     * @param $model
     */
    private function productMasterSync(RemedyStock $model)
    {
        $remedy = $model->remedy;

        ProductMaster::updateAll(['in_stock' => -2/*TEMP*/], ['remedy_id' => $model->remedy_id,
                                                              'in_stock'  => -1]);

        foreach($remedy->getProducts() as $stock)
        {
            if(false == $stock->isNewRecord)
                continue; // $stockが実在する時は ProductMasterSyncronizer が働いてくれるので以下を省略

            $master = ProductMaster::findOne([
                'remedy_id'  => $stock->remedy_id,
                'potency_id' => $stock->potency_id,
                'vial_id'    => $stock->vial_id
            ]);

            if(! $master) {
                $master = new ProductMaster([
                    'ean13'      => $stock->getBarcode(),
                    'company_id' => $stock->category->seller_id,
                    'category_id'=> $stock->category->category_id,
                    'product_id' => null,
                    'remedy_id'  => $stock->remedy_id,
                    'potency_id' => $stock->potency_id,
                    'vial_id'    => $stock->vial_id,
                    'restrict_id'=> $stock->restrict_id,
                    'name'       => $stock->name,
                    'kana'       => $stock->kana,
                    'price'      => $stock->price,
                ]);
            }

            $master->in_stock = -1; // 仮想在庫は -1 で表す
            $master->save();
        }

        ProductMaster::deleteAll(['remedy_id' => $model->remedy_id,
                                  'in_stock'  => -2/*TEMP*/]);
    }
}
