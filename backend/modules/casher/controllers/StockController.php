<?php

namespace backend\modules\casher\controllers;

use Yii;
use common\models\Branch;
use \common\models\Category;
use common\models\Product;
use common\models\ProductMaster;
use common\models\Stock;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\db\StaleObjectException;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/cashar/controllers/StockController.php $
 * $Id: StockController.php 821 2015-03-25 05:22:56Z mori $
 */

class StockController extends DefaultController
{

    public function beforeAction($action)
    {
        return true;
    }

    public function actionIndex()
    {
        $branch_id = $this->module->branch ? $this->module->branch->branch_id : Branch::PKEY_ROPPONMATSU;
        $query = Stock::find()->andWhere(['=', 'branch_id', $branch_id]);
            
        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder'=>['stock_id'=>SORT_ASC]],
            'pagination'=> ['pageSize' => 20 ],
        ]);

        return $this->render('index', [
                'stockModel' => $query->all(),
                'provider' => $provider
        ]);
    }

    public function actionCreate()
    {
        $model = $this->initModel();

        $transaction = Yii::$app->db->beginTransaction();
        $request = Yii::$app->request;

        if($request->isPost) {

            try {
                $model->load($request->post());
                // バーコード（ean13）部分を抜き取り
                preg_match('/[0-9]+$/', Yii::$app->request->post('name'), $ean13);
                $model->ean13 = $ean13[0];
                $product = ProductMaster::find()->andWhere(['ean13'=>$ean13])->one();
                if($product)
                    $model->product_id = $product->product_id;

                if ($model->save()) {

                    $q = Yii::$app->db
                    ->createCommand('update mvtb_product_master set update_date = NOW() where ean13 = :ean13')
                    ->bindValues([':ean13'=> $model->ean13]);
                    $q->execute();

                    $transaction->commit();
                    Yii::$app->session->addFlash('success', sprintf("%s を登録しました。", $model->products[0]->name));
                } else {
                    $transaction->rollback();
                    Yii::$app->session->addFlash('danger', sprintf("在庫登録に失敗しました。お手数ですが、はじめからやり直して下さい。"));
                }
            } catch (StaleObjectException $e) {
                // 衝突を解決するロジック
                $transaction->rollback();
                Yii::$app->session->addFlash('danger', sprintf("%s は既に登録されています。", $model->products[0]->name));
            }

            return $this->redirect('index');

        }

        return $this->render('create', [
            'model' => $model,
            'branchs' => \yii\helpers\ArrayHelper::map(\common\models\Branch::find()->wareHouse()->all(), 'branch_id', 'name'),
        ]);
    }

    /**
     * 在庫数更新
     * @param unknown $id
     * @param string $status
     * @throws ForbiddenHttpException
     */
    public function actionUpdate($id)
    {

        $transaction = Yii::$app->db->beginTransaction();
        $request = Yii::$app->request;

        if($request->isPost) {

            try {
                // 在庫model
                $model = $this->findModel($id);

                $diff_qty = $request->post('actual_qty') - $model->actual_qty;

                $model->actual_qty = $request->post('actual_qty');
                $model->version = $request->post('version');
                $model->updated_by = Yii::$app->user->id;

                if ($model->save()) {

                    $q = Yii::$app->db
                        ->createCommand('update mvtb_product_master set update_date = NOW() where product_id = :product_id')
                        ->bindValues([':product_id'=> $model->product_id]);                    
                    $q->execute();

                    $transaction->commit();
                    Yii::$app->session->addFlash('success', sprintf("%s の在庫数を %s に更新しました。(変動数： %s )", $model->products[0]->name, $model->actual_qty, $diff_qty));
                } else {
                    $transaction->rollback();
                    Yii::$app->session->addFlash('danger', sprintf("%s の在庫数の更新に失敗しました。<br>再度入力し直してください。", $model->products[0]->name));
                }
            } catch (StaleObjectException $e) {
                // 衝突を解決するロジック
                $transaction->rollback();
                Yii::$app->session->addFlash('danger', sprintf("%s の在庫数が既に変動しています。<br>再度入力し直してください。", $model->products[0]->name));
            }

            return $this->redirect('index');

        }
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            Yii::$app->session->addFlash('success', '在庫情報を削除しました');
        }
        return $this->redirect(['index']);
    }

    /**
     * 在庫管理商品登録用　商品名検索
     * @param str キーワード（商品名での検索条件）
     * @return json 商品名
     */
    public function actionSearch()
    {
        $query = ProductMaster::find()
            // 商品名のみを表示したい場合は不要
            ->select([
                'product_id',
                'concat('.ProductMaster::tableName().'.name," ",ean13) as product_name'
            ])
            ->leftJoin(Category::tableName().' c',
                ProductMaster::tableName().'.category_id=c.category_id')
            ->andWhere(['seller_id' => Category::TOYOUKE])
            ->andWhere(['like', ProductMaster::tableName().'.name', Yii::$app->request->post('name')]);

        if(! $query->exists())
            return [];

        // 商品名の箇所に「商品名　バーコード」で表示する用
        $data = \yii\helpers\ArrayHelper::map($query->all(), 'product_id', 'product_name');
        echo json_encode($data);
    }

    protected function initModel()
    {
        return new Stock(['version' => Stock::INIT_VERSION, 'updated_by' => Yii::$app->user->id]);
    }


    /**
     * Finds the Transfer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transfer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected static function findModel($stock_id)
    {
        if(!$model = Stock::findOne($stock_id))
            throw new NotFoundHttpException('The requested page does not exist.');

        return $model;
    }

    /**
     * Finds the Transfer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transfer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected static function findModelWithBranchId($branch_id)
    {
        if(!$model = Stock::find()->andWhere(['=', 'branch_id', $branch_id])->all())
            throw new NotFoundHttpException('The requested page does not exist.');

        return $model;
    }

    private function loadProvider($model)
    {
        $query = $model::find()->andWhere(['branch_id' => $this->module->branch->branch_id ]);

        return new ActiveDataProvider([
                'query' => $query,
                'sort' => ['defaultOrder' => ['update_date'=>SORT_DESC]],
                ]);
    }

    private function updateModels($id, $matrix)
    {
        $html  = [];

        $transaction = Yii::$app->db->beginTransaction();
        $rollback    = false;

        foreach($matrix as $row)
        {
            if(! $model = InventoryItem::find()->where(['inventory_id' => $id,
                    'iitem_id'     => $row['iitem_id'],
                    'ean13'        => $row['ean13']])
                    ->one())
            {
                $model = new InventoryItem($row);
                $model->addError('ean13',"iitem_idとバーコード(ean13)が一致しません");
            }
            $model->actual_qty = $row['actual_qty'];

            if($model->hasErrors() || ! $model->save())
            {
                $rollback = true;
            }

            $html[] = $this->renderPartial('_row', ['model'=>$model,'row'=>$row]);
        }
        $model = $this->findModel($id);
        if(! $model->save() ){ var_dump($model->firstErrors); $rollback = true; }

        if($rollback)
        {
            $transaction->rollback();
            Yii::$app->session->addFlash('error', "エラーにより更新を中止しました");
        }
        else
        {
            $transaction->commit();
            Yii::$app->session->addFlash('success', "対象レコードを以下の通り更新しました");
        }

        return implode('', $html);
    }


}
