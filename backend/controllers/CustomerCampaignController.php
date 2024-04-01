<?php

namespace backend\controllers;

use Yii;
use common\models\CustomerCampaign;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * CustomerCampaignController implements the CRUD actions for CustomerCampaign model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/CustomerCampaignController.php $
 * $Id: CustomerGradeController.php 1159 2015-07-16 05:38:53Z mori $
 *
 */
class CustomerCampaignController extends BaseController
{
    /**
     * Lists all CustomerCamapaign models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => CustomerCampaign::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}