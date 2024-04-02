<?php

namespace common\modules\event\controllers;

/*
 * $URL$
 * $Id$
 */

use Yii;
use yii\web\Controller;
use common\models\Event;
use common\models\EventVenue;
use common\models\EventAttendee;
use common\models\SearchProductFavor;


class DefaultController extends Controller
{
    public function actionIndex()
    {
        $model = new EventVenue();
        $model->load(Yii::$app->request->get(),'');
        $provider = $this->loadProvider($model);

        return $this->render('index',['dataProvider'=>new \yii\data\ArrayDataProvider()]);
    }

    public function actionView($id)
    {
        $model = Event::findOne($id);
        $query = EventVenue::find()->where(['product_id'=>$id]);//->andWhere('pub_date < NOW()');

        return $this->render('view',[
            'model' => $model,
        ]);
    }

    public function actionApply($id)
    {
        $venue = EventVenue::findOne($id);
        $event = Event::findOne(['product_id' => $venue->product_id]);
        $model = new EventAttendee([
            'venue_id'   => $id,
            'product_id' => $venue->product_id]);

        return $this->render('apply',[
            'event' => $event,
            'venue' => $venue,
            'model' => $model,
        ]);
    }

    private function findModel($id)
    {
    }

    private function loadProvider($model)
    {
        $query = EventVenue::find();
        
        return new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]);
    }

}
