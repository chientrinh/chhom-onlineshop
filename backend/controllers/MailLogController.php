<?php

namespace backend\controllers;

use Yii;
use common\models\MailLog;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * MailLogController implements the CRUD actions for MailLog model.
 */
class MailLogController extends BaseController
{
    public $label = "メール送信履歴";

    public $crumbs = [
        'index' =>['label'=>"一覧",],
        'view'  =>['label'=>"詳細",],
    ];

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'] = [
            ['label' => $this->label, 'url' => [sprintf('/%s/%s', $this->id, $this->defaultAction)],],
            $this->crumbs[$action->id],
        ];
        
        return true;
    }

    /**
     * Lists all MailLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => MailLog::find(),
            'sort'  => [
                'defaultOrder' => ['date' => SORT_DESC ],
                ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MailLog model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the MailLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MailLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MailLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
