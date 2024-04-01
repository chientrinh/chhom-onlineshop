<?php

namespace backend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\Facility;
use common\models\Zip;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/FacilityController.php $
 * $Id: FacilityController.php 3987 2018-08-17 02:30:40Z mori $
 *
 */
class FacilityController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'提携施設','url'=>['index']];

        return true;
    }

    public function actionIndex()
    {
        $query = Facility::find();
        $model = new Facility();
        $model->load(Yii::$app->request->get());

        foreach($model->getDirtyAttributes() as $attr => $value)
            $query->andFilterWhere(['like', $attr, $value]);

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index',[
            'provider' => $provider,
            'model'    => $model
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view',['model'=>$model]);
    }

    public function actionCreate()
    {
        $this->view->params['breadcrumbs'][] = ['label'=>'作成'];
        $model = new Facility(['customer_id' => Yii::$app->request->get('id'),
                                   'private'     => 1,
                                   'pub_date'    => date('Y-m-d'),]);

        if('zip2addr' == Yii::$app->request->post('scenario'))
        {
            $model->load(Yii::$app->request->post());
            $this->zip2addr($model);
        }

        elseif($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->save();
            Yii::$app->session->addFlash('success',"{$model->name}を保存しました");
            return $this->redirect(['view', 'id' => $model->facility_id]);
        }

        return $this->render('update', [
            'model'      => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post()))
        {
            if(! $this->zip2addr($model) && $model->save())
            {
                Yii::$app->session->addFlash('success',"{$model->name}を保存しました");
                return $this->redirect(['view','id'=>$model->facility_id]);
            }
        }

        return $this->render('update',['model'=>$model]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if(! $model->isNewRecord && ! $model->delete())
            Yii::$app->session->addFlash('success',"提携施設を削除できませんでした。システム担当者へご連絡ください");
        else
            Yii::$app->session->addFlash('success',"{$model->name}を削除しました");

        if(strpos(Yii::$app->request->referrer, 'index.php/customer') !== false) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->redirect(['index']);
        }
    }

    private function findModel($id)
    {
        $model = Facility::findOne($id);

        if(! $model)
            $model = new Facility(['customer_id' => $id,
                                   'private'     => 1,
                                   'pub_date'    => date('Y-m-d'),
            ]);
        else
            $this->view->params['breadcrumbs'][] = [
                'label'=> $model->name,
                'url'  => ['view', 'id' => $model->facility_id]
            ];

        return $model;
    }

    private function zip2addr($model)
    {
        if('zip2addr' !== Yii::$app->request->post('scenario'))
            return false;

        if(! $hint = Zip::zip2addr($model->zip01, $model->zip02))
        {
            Yii::$app->session->addFlash('warning',"郵便番号({$model->zip})に対応する市区町村が見つかりません");
            return true;
        }

        $model->pref_id = $hint->pref_id;
        $model->addr01  = array_shift($hint->addr01);

        return true;
    }

}
