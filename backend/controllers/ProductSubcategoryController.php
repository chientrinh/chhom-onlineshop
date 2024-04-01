<?php

namespace backend\controllers;

/**
 * Product Subcategory Controller
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/ProductSubcategoryController.php $
 * $Id: ProductSubcategoryController.php 2017 2016-01-28 03:53:34Z mori $
 */

use Yii;
use \yii\helpers\ArrayHelper;
use \common\models\ProductSubcategory;

class ProductSubcategoryController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'create' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $sid = Yii::$app->request->post('subcategory_id');
        $ean = Yii::$app->request->post('ean13');

        $model = new ProductSubcategory(['subcategory_id' => $sid,
                                         'ean13'          => $ean]);

        if($model->save())
            return 'ok';
    }

    public function actionDelete()
    {
        $sid = Yii::$app->request->post('subcategory_id');
        $ean = Yii::$app->request->post('ean13');

        $model = ProductSubcategory::find()->where(['subcategory_id' => $sid,
                                                    'ean13'          => $ean])
                                           ->one();
        if(! $model)
            throw new \yii\web\NotFoundHttpException("Target not found (sid: $sid, ean: $ean)");

        if($model->delete())
            return 'ok';

        throw new \yii\web\BadRequestHttpException(implode(';',$model->firstErrors));
    }

}
