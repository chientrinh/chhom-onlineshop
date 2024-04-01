<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/RemedyPriceRangeItemController.php $
 * $Id: RemedyPriceRangeItemController.php 3282 2017-05-02 08:11:36Z kawai $
 */

namespace backend\controllers;

use Yii;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use common\models\RemedyVial;
use common\models\ProductMaster;
use common\models\DateManagement;
use common\models\RemedyPriceRangeItem;
use common\models\SearchRemedyPriceRangeItem;

/**
 * RemedyPriceRangeItemController implements the CRUD actions for RemedyPriceRangeItem model.
 */
class RemedyPriceRangeItemController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'商品','url'=>['/product']];
        $this->view->params['breadcrumbs'][] = ['label'=>'レメディー','url'=>['/remedy']];
        $this->view->params['breadcrumbs'][] = ['label'=>'価格設定','url'=>['index']];

        return true;
    }

    /**
     * Lists all RemedyPriceRangeItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchRemedyPriceRangeItem();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RemedyPriceRangeItem model.
     * @param integer $prange_id
     * @param integer $vial_id
     * @param integer $price
     * @param string $start_date
     * @return mixed
     */
    public function actionView($prange_id, $vial_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($prange_id, $vial_id),
        ]);
    }

    /**
     * Creates a new RemedyPriceRangeItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RemedyPriceRangeItem();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'prange_id' => $model->prange_id, 'vial_id' => $model->vial_id, 'price' => $model->price, 'start_date' => $model->start_date]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing RemedyPriceRangeItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $prange_id
     * @param integer $vial_id
     * @param integer $price
     * @param string $start_date
     * @return mixed
     */
    public function actionUpdate($prange_id, $vial_id)
    {
        $model = $this->findModel($prange_id, $vial_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'prange_id' => $model->prange_id, 'vial_id' => $model->vial_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing RemedyPriceRangeItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $prange_id
     * @param integer $vial_id
     * @param integer $price
     * @param string $start_date
     * @return mixed
    public function actionDelete($prange_id, $vial_id, $price, $start_date)
    {
        $this->findModel($prange_id, $vial_id, $price, $start_date)->delete();

        return $this->redirect(['index']);
    }
     */


    /**
     * 価格一括更新処理
     */
    public function actionReflectionPrice()
    {
        try{
            Yii::info("価格一括更新を開始します。", $this->className().'::'.__FUNCTION__);

            $data = ProductMaster::getTargetForUpdate();
            // 対象データが1件もない場合
            if ($data->count() < 1) {
                Yii::$app->session->addFlash('success',"価格一括更新が終了しました。<br />実行対象件数はありませんでした。");
              
                return $this->redirect('index');
            }

            // 正常更新件数
            $suc_total = 0;
            $skip_total = 0;
            $fatal_total = 0;

            // 対象のデータを1000件単位で
            foreach ($data->each(1000) as $remedy) {

                try {
                    $price = $remedy->remedyPrice; // 新価格の取得
                }
                catch (\console\exception\PriceNotFoundException $e) {
                    // エラーログ出力をする
                    Yii::error(
                        sprintf(
                            "価格が取得できませんでした。【remedy_id = %s, potency_id = %s, vial_id = %s】", 
                            $remedy->remedy_id, 
                            $remedy->potency_id, 
                            $remedy->vial_id
                            ), 
                        $this->className().'::'.__FUNCTION__
                        );
                    $fatal_total++;
                    // エラーを検知したデータは後続処理は行わずに次のデータ処理へ
                    continue;
                }

                // データ更新を行う
                $remedy->updateProductMasterPrice($suc_total, $skip_total, $price);  
            }

            $complete_msg = sprintf(
                "価格一括更新が終了しました。<br />\n実行対象件数：%s件<br />\n旧価格・新価格同一件数：%s件, 正常完了件数：%s件, 処理失敗件数:%s件", 
                $data->count(), 
                $skip_total,
                $suc_total,
                $fatal_total
                );
                
            Yii::info($complete_msg, $this->className().'::'.__FUNCTION__);

            Yii::$app->session->addFlash('success', $complete_msg);

        }
        catch (\yii\db\Exception $e)
        {
            Yii::error($e->__toString(), $this->className().'::'.__FUNCTION__);
            Yii::$app->session->addFlash('error',"価格一括反映にてシステムエラーが発生しました。システム管理者にご連絡ください。");
        }

        return $this->redirect('index');
        
    }

    /**
     * Finds the RemedyPriceRangeItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $prange_id
     * @param integer $vial_id
     * @param integer $price
     * @param string $start_date
     * @return RemedyPriceRangeItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($prange_id, $vial_id)
    {
        if (($model = RemedyPriceRangeItem::findOne(['prange_id' => $prange_id, 'vial_id' => $vial_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
