<?php

namespace common\modules\sodan\controllers;

use Yii;
use \backend\models\Staff;
use \common\models\sodan\Interview;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/controllers/PurchaseController.php $
 * $Id: PurchaseController.php 4145 2019-03-29 06:20:34Z kawai $
 */

class PurchaseController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => "お会計", 'url' => ['index']];

        return true;
    }

    public function actionIndex()
    {
        return $this->redirect(['interview/index']);
    }

    public function actionView($id)
    {
        $model = $this->findmodel($id);
        if(! $model->purchase_id)
            throw new \yii\web\NotFoundHttpException('お支払いは未定義なので、表示できません');

        // set cookie; required by \backend\modules\casher\Module::beforeAction()
        Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name'  => 'branch_id',
            'value' => $model->branch_id,
            'path'  => \yii\helpers\Url::base(),
            'expire'=> time() + 3600 * 24, // 24 hour
        ]));

        return $this->redirect(['/casher/default/view', 'id'=>$model->purchase_id]);
    }

    public function actionCreate($id)
    {
        $model = $this->findModel($id);

        if(! $model->validate())
            return $this->redirect(['interview/update','id'=>$id,'scenario'=>$model->scenario]);

        $form = new \common\models\sodan\PurchaseForm([
            'interview' => $model,
            'discount_rate' => Yii::$app->request->post('discount_rate')
        ]);
        if(!Yii::$app->request->post('change_rate') && $form->load(Yii::$app->request->post()) && $form->save()) {
            return $this->redirect(['interview/view','id'=>$model->itv_id]);
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->module->findModel($id);

        if($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view','id'=>$model->itv_id]);

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    private function findModel($id)
    {
        $model = $this->module->findModel($id);
        $model->scenario = $model::SCENARIO_PAY;

        return $model;
    }

}
