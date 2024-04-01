<?php

namespace backend\modules\sales\controllers;

use Yii;
use yii\web\Controller;
use common\models\ecorange\SearchOrderDetail;

class DefaultController extends Controller
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
}
