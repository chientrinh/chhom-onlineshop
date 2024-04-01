<?php

namespace backend\controllers;

use Yii;
use common\models\Product;
use common\models\RemedyStock;
use common\models\ProductImage;
use backend\models\ImageForm;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductController implements the CRUD actions for Product model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/ProductImageController.php $
 * $Id: ProductImageController.php 2248 2016-03-13 08:33:07Z mori $
 */

class ProductImageController extends BaseController
{
    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionAdd($id)
    {
        $product = $this->findModel($id);

        $model = new \backend\models\ImageForm();

        if (Yii::$app->request->isPost)
        {
            $model->imageFile = \yii\web\UploadedFile::getInstance($model, 'imageFile');

            if ($filename = $model->upload())
            { // file is uploaded successfully
                $img = new \common\models\ProductImage([
                    'ean13'   => $product->barcode,
                    'ext'     => $model->imageFile->extension,
                    'caption' => $product->name,
                    'created_at' => time(),
                    'created_by' => Yii::$app->user->id,
                ]);

                if($img->importContent($filename) && $img->save())
                {
                    Yii::$app->session->addFlash('success', sprintf("%s に画像を追加しました", $product->name));

                    if($product instanceof Product)
                        return $this->redirect(['/product/update', 'id'=>$id]);

                    return $this->redirect([
                        '/remedy-stock/view',
                        'remedy_id' =>$product->remedy_id,
                        'potency_id'=>$product->potency_id,
                        'vial_id'   =>$product->vial_id,
                    ]);
                }
            }
        }

        return $this->render('upload', ['model' => $model]);
    }

    public function actionUpdate($id, $weight)
    {
        if(! $model = ProductImage::findOne($id))
            throw new \yii\web\NotFoundHttpException('対象は見つかりません');

        $model->weight = $weight;

        if($model->save())
            Yii::$app->session->addFlash('success',"上位に移動しました");
        else
            Yii::$app->session->addFlash('error',"上位に移動できませんでした");

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionDelete($id)
    {
        $model = ProductImage::findOne($id);
        if($model->delete())
            Yii::$app->session->addFlash('success', "画像を削除しました");
        else
            Yii::$app->session->addFlash('error', "画像の削除に失敗しました");

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(! $model = Product::findOne($id))
             $model = RemedyStock::findByCode($id);

        if(! $model)
            throw new \yii\base\Exception('sorry, 対象商品を code または id から取得できませんでした');

        return $model;
    }
}
