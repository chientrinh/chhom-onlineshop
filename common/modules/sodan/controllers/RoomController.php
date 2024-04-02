<?php

namespace common\modules\sodan\controllers;

use Yii;
use \backend\models\Staff;
use \common\models\sodan\Interview;
use \common\models\sodan\InterviewStatus;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/controllers/RoomController.php $
 * $Id: RoomController.php 3851 2018-04-24 09:07:27Z mori $
 */

class RoomController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => "空き状況", 'url' => ['index']];

        return true;
    }

    public function actionIndex()
    {
        $model = new Interview();
        $model->load(Yii::$app->request->queryParams);
        $provider = $this->loadProvider($model);

        if($client = \common\models\Customer::findOne(Yii::$app->request->get('client_id')))
            $provider->query->andWhere(['client_id'=>null]);

        $date  = new \common\models\DateForm();
        if($date->load(Yii::$app->request->get()))
        {
            if($date->year)
                $provider->query->year($date->year);

            if($date->month)
                $provider->query->month($date->month);

            if($date->day)
                $provider->query->day($date->day);

            if($date->wday)
                $provider->query->wday($date->wday);
        }
        if($model->itv_date)
        {
            $date->year  = date('Y', strtotime($model->itv_date));
            $date->month = date('m', strtotime($model->itv_date));
            $date->day   = date('d', strtotime($model->itv_date));
            $date->wday  = date('w', strtotime($model->itv_date));
        }
        
        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
            'dateModel'    => $date,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->module->findmodel($id);
        if($model->client)
            return $this->redirect(['interview/view','id'=>$id]);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionBook($client_id,$itv_id)
    {
        $model = $this->module->findmodel($itv_id);

        if(! $model->client_id)
        {
            $model->client_id = $client_id;
            if($model->update())
                Yii::$app->session->addFlash('success',"相談会を予約しました");
            else
                Yii::$app->session->addFlash('error',"book failed:".implode(';',$model->firstErrors));
        }
        elseif($client_id != $model->client_id)
            Yii::$app->session->addFlash('error',"すでに別のクライアントが予約しています");
        
        return $this->redirect(['interview/view','id'=>$itv_id]);
    }

    public function actionCreate()
    {
        return $this->redirect(['interview/create']);
    }

    public function actionUpdate($id)
    {
        return $this->redirect(['interview/update','id'=>$id]);
    }

    /* @brief search Client */
    public function actionSearch($id, $target)
    {
        if(! in_array($target,['toranoko','wait-list']))
            throw new \yii\base\UserException();

        $model = $this->module->findModel($id);

        $cookies = Yii::$app->response->cookies;
        $cookies->add(new \yii\web\Cookie([
            'name'  => 'ebisu-intra-request-json',
            'value' => \yii\helpers\Json::encode([
                'route' => \yii\helpers\Url::to(['apply-client','id'=>$model->itv_id]),
                'attribute' => 'customer_id',
                'title'=> sprintf('相談会(%s %s %s)を予約します', $model->itv_date, $model->itv_time, $model->homoeopath ? $model->homoeopath->name : "未定"),
            ]),
        ]));

        if('toranoko' == $target)
            return $this->redirect(['/member/toranoko/index']);
        
        return $this->redirect(['wait-list/index']);
    }

    public function actionApplyClient($id,$customer_id)
    {
        $model = $this->module->findModel($id);

        if(! $customer_id)
            self::addFlash('error','クライアントの指定がありません、または不正なリクエストです');

        elseif($customer_id == $model->client_id)
            self::addFlash('success','クライアントは登録済みです');

        elseif(0 < $model->client_id)
            self::addFlash('error','別のクライアントが登録済みです');

        elseif($model->client_id = $customer_id)
        {
            $model->status_id = InterviewStatus::PKEY_READY;

            if($model->validate(['client_id']))
                $model->save(false);

            if($model->hasErrors())
                self::addFlash('error',"クライアントを指定できません：".$model->getFirstError('client_id'));
            else
                self::addFlash('success',"クライアントを指定しました");

            $cookies = Yii::$app->response->cookies;
            $cookies->remove('ebisu-intra-request-json');
        }
        else
            self::addFlash('error',"クライアントが指定できませんでした");

        return $this->redirect(['view','id'=>$id]);
    }

    private function loadProvider($model)
    {
        $provider = $this->module->loadProvider($model);

        $provider->query->andWhere('NOW() <= itv_date')
                        ->andWhere(['client_id'=>null]);

        return $provider;
    }

    private function addFlash($flag, $message)
    {
        Yii::$app->session->addFlash($flag, $message);
    }
}
