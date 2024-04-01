<?php

namespace backend\controllers;

use Yii;
use common\models\EventCampaign;
use backend\models\SearchEventCampaign;
use yii\web\NotFoundHttpException;

/**
 * EventCampaignController implements the CRUD actions for EventCampaign model.
 */
class EventCampaignController extends BaseController
{
    /**
     * @var PDF_MERGER absolute path of `pdfunite`
     * Caution: this class is completely dependent on this executable, no warranty without it
     */
    const PDF_MERGER  = '/usr/bin/pdfunite';

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => 'イベント参加者限定キャンペーン', 'url' => ['index']];

        return true;
    }

    /**
     * Lists all Vegetable models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchEventCampaign();

        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Vegetable model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'campaign' => $this->findModel($id)
        ]);      
    }

    /**
     * Creates a new Vegetable model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $campaign = new EventCampaign();
        if($campaign->load(Yii::$app->request->post()) && $campaign->save())
            return $this->redirect(['view', 'id' => $campaign->ecampaign_id]);


        return $this->render('create', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * Updates an existing Campaign model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $campaign = $this->findModel($id);
        if($campaign->load(Yii::$app->request->post()) && $campaign->save())
            return $this->redirect(['view', 'id' => $campaign->ecampaign_id]);


        return $this->render('update', [
            'campaign' => $campaign,
        ]);   
    }

    /**
     * Deletes an existing Vegetable model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $campaign = $this->findModel($id);

        if($campaign->delete())
            return $this->redirect(['index']);
        
        Yii::$app->session->addFlash('error', "{$campaign->campaign_code}を削除できません、システム担当者へ連絡してください");
        return $this->redirect(['view', 'id' => $campaign->ecampaign_id]);
    }

    /**
     * Finds the Vegetable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Vegetable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $campaign = EventCampaign::findOne($id);

        if(! $campaign)
            throw new NotFoundHttpException("当該IDは見つかりません({$id})");

        return $campaign;
    }
}
