<?php

namespace backend\controllers;

/**
 * Jancode Controller: handles following view and tables
 *  - vtb_jancode 
 *  - dtb_product_jan
 *  - mtb_remedy_stock_jan
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/JancodeController.php $
 * $Id: JancodeController.php 4231 2020-02-05 05:34:50Z mori $
 */

use Yii;
use common\models\Jancode;
use common\models\Product;
use common\models\ProductMaster;
use common\models\ProductJan;
use common\models\RemedyStock;
use common\models\RemedyStockJan;

class JancodeController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => 'JANコード', 'url' => ['index'] ];

        return true;
    }

    public function actionIndex($pagination='yes')
    {
        $model = new \common\models\SearchJancode();
        $model->load(Yii::$app->request->queryParams);

        $provider = $model->search();

        if('yes' !== $pagination)
            $provider->pagination = false;

        return $this->render('index',[
            'dataProvider'=> $provider,
            'searchModel' => $model,
        ]);
    }

    public function actionView($id)
    {
        if(! $model = Jancode::findOne(['jan' => $id]))
            throw new \yii\web\NotFoundHttpException("指定されたJANコードは存在しません: $id");

        return $this->render('view',['model'=>$model]);
    }

    public function actionCreate($id)
    {
        if(13 == strlen(trim($id)))
            $target = RemedyStock::findByBarcode($id);
        else
            $target = Product::findOne($id);

        if(! $target)
            throw new \yii\web\NotFoundHttpException("指定モデルは検出されませんでした。詳細はシステム部へお問い合わせください。");
        if($target instanceof RemedyStock)
            $model = new RemedyStockJan(['sku_id' => $id]);
        else
            $model = new ProductJan(['product_id'=>$id]);

        if(! Yii::$app->request->isPost)
            $model->validate();

        elseif($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view', 'id' => $model->jan]);

        return $this->render('create',['model'=>$model]);
    }

    public function actionUpdate($id)
    {
        if(13 == strlen(trim($id)))
            $model = RemedyStockJan::find()->where(['sku_id' => $id])->one();
        else
            $model = ProductJan::find()->where(['product_id'=>$id])->one();

        if(! $model)
            throw new \yii\base\UserException("指定モデルは検索できませんでした。詳細はシステム部へお問い合わせください。");

        if($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view', 'id' => $model->jan]);

        return $this->render('update',['model'=>$model]);
    }

    public function actionDelete($id)
    {
        if(! $master = Jancode::findOne(['jan' => $id]))
            throw new \yii\web\NotFoundHttpException("指定されたJANコードは存在しません: $id");

        if(0 < $master->product_id)
            $query = ProductJan::find()->where(['product_id' => $master->product_id]);
        else
            $query = RemedyStockJan::find()->where(['sku_id' => $master->sku_id]);


        $transaction = Yii::$app->db->beginTransaction(); 


        if($productMaster = ProductMaster::findOne(['ean13' => $master->jan])) {
            if($productMaster->product_id) {
                // 商品登録時に行なっているデフォルト値の生成を応用 common/models/Product.phpを参照 2020/01/24 kawai
                $code  = sprintf('%02d%010d',
                              Product::EAN13_PREFIX,
                              $master->product_id );
                $code .= \common\components\ean13\CheckDigit::generate($code);

                $productMaster->ean13 = $code;

                // Productのバーコード・値札に使用するdtb_product.codeを更新
                $product = Product::findOne(['product_id' => $master->product_id]);
                if($product) {
                    $product->code = $code;
                    $product->save();
                }

            } else {
                $productMaster->ean13 = $master->sku_id;
            }
            $productMaster->save();
        }
       
        if((1 == $query->count())   &&
           ($model = $query->one()) &&
           $model->delete() )
        {
            $transaction->commit();
            Yii::$app->session->addFlash('success',"JANコード({$id})を削除しました");
            return $this->redirect(['index']);
        } else {
            $transaction->rollback();
        }

        throw new \yii\web\NotFoundHttpException("JANコード定義エラー。詳細はシステム部へお問い合わせください。".$model->errors);
    }
}
