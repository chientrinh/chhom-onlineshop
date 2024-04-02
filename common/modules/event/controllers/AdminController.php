<?php

namespace common\modules\event\controllers;

/*
 * $URL$
 * $Id$
 */

use Yii;
use yii\web\Controller;
use common\models\EventVenue;


class AdminController extends Controller
{
    public function actionIndex()
    {
        $model = new EventVenue();
        $model->load(Yii::$app->request->get(),'');
        $provider = $this->loadProvider($model);

        return $this->render('index',[
            'searchModel' => $model,
            'dataProvider'=> $provider,
        ]);
    }

    public function actionView($id)
    {
        $model = EventVenue::findOne($id);

        return $this->render('view',['model'=>$model]);
    }

    public function actionCreate()
    {
        $model = new EventVenue();

        if($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view','id'=>$model->venue_id]);

        return $this->render('create',['model'=>$model]);
    }
    
    public function actionUpdate($id)
    {
        $model = EventVenue::findOne($id);

        if($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view','id'=>$model->venue_id]);

        return $this->render('update',['model'=>$model]);

    }

    private function loadProvider($model)
    {
        $query = EventVenue::find();
        
        return new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]);
    }
}
