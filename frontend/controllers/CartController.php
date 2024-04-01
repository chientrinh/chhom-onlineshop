<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductController implements the CRUD actions for Product model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/controllers/CartController.php $
 * $Id: CartController.php 956 2015-04-25 09:07:20Z mori $
 */
class CartController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionAdd()
    {
        $this->redirect('view');
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = $this->findModel();
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     */
    public function actionView()
    {
        return $this->render('view', [
            'model' => $this->findModel(),
        ]);
    }

    public function actionDemo()
    {
        return $this->render('demo', [
            'model' => $this->findModel(),
        ]);
    }

    protected function findModel($dummy=true)
    {
        $items    = \common\models\Product::findAll(1,2,3);
        $customer = \common\models\Customer::findOne(1);
        return ['items'=> $items,
                'customer'=>$customer];
    }
}
