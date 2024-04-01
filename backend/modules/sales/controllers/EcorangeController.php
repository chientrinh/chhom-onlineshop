<?php

namespace backend\modules\sales\controllers;

use Yii;
use yii\web\Controller;
use common\models\ecorange\SearchOrderDetail;

class EcorangeController extends Controller
{
    public function actionIndex()
    {
        $searchModel  = new SearchOrderDetail();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDetail()
    {
        $query = Yii::$ecOrange->createCommand($cmd, [
            
        ]);
    }
}
