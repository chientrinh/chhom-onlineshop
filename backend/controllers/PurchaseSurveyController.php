<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/PurchaseSurveyController.php $
 * $Id: PurchaseSurveyController.php 2927 2016-10-06 06:14:16Z mori $
 */

namespace backend\controllers;

use Yii;
use \yii\helpers\ArrayHelper;
use \backend\models\PurchaseSurvey;

/**
 * PurchaseSurveyController implements the CRUD actions for PurchaseSurvey model.
 */
class PurchaseSurveyController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'統計','url'=>['index']];

        return true;
    }

    /**
     * Lists existence of PurchaseSurvey models per date.
     * @return mixed
     */
    public function actionIndex($year=null, $month=null)
    {
        if(null === $year)
            $year = date('Y');

        if(null === $month)
            $month = date('m');

        $query = PurchaseSurvey::find()
            ->where(['EXTRACT(YEAR  FROM target_date)' => $year,
                     'EXTRACT(MONTH FROM target_date)' => $month]);

        $model = new PurchaseSurvey();
        if($model->load(Yii::$app->request->get()))
            $query->andFilterWhere($model->attributes);

        return $this->render('index', [
            'year'  => $year,
            'month' => $month,
            'query' => $query,
            'searchModel' => $model,
        ]);
    }

    /**
     * (re)generate PurchaseSurvey for days of a month.
     * @param string $year
     * @param string $month
     * @return mixed
     */
    public function actionUpdate($year, $month)
    {
        $days = range(1, date('t', strtotime("$year-$month-01")));

        foreach($days as $day)
        {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

            $this->deleteModels($date);
            $this->createModels($date);
        }

        Yii::$app->session->addFlash('success', "<strong>{$year}年{$month}月</strong>分を更新しました");

        return $this->redirect(['index', 'year' => $year, 'month' => $month]);
    }

    /**
     * @param string $date
     * @return void
     */
    private function createModels($date)
    {
        if(! preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date))
            throw new \yii\base\Exception("$date の書式が不正です。yyyy-mm-ddで入力してください");

        foreach(\common\models\Branch::find()->select('branch_id')->column() as $branch_id)
        {
            $models = PurchaseSurvey::createModels($date, $branch_id);

            foreach($models as $model)
            {
                if($model->save())
                    continue;

                Yii::$app->session->addFlash('error', implode(';',$model->firstErrors));
            }
        }

        return;
    }

    /**
     * @param string $date
     * @return int
     */
    private function deleteModels($date)
    {
        if(! preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date))
            throw new \yii\base\Exception("$date の書式が不正です。yyyy-mm-ddで入力してください");

        return PurchaseSurvey::deleteAll('target_date = :d',[':d'=>$date]);
    }

}
