<?php

namespace common\modules\sodan\controllers;

use Yii;
use \backend\models\Staff;
use \common\models\statistics\SodanStatistic;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/controllers/StatController.php $
 * $Id: StatController.php 3851 2018-04-24 09:07:27Z mori $
 */

class StatController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow'   => true,
                        'matchCallback' => function($rule,$action)
                        {
                            $user = Yii::$app->user->identity;
                            return($user instanceof \backend\models\Staff);
                        },
                    ],
                    [
                        // ホメオパスは [view,id=>自分] のみ閲覧を許可
                        'actions' => ['view'],
                        'allow'   => true,
                        'matchCallback' => function($rule,$action)
                        {
                            $user = Yii::$app->user->identity;
                            return($user instanceof \backend\models\Staff ||
                                  ($user instanceof \common\models\Customer &&
                                      $user->id === (int) Yii::$app->request->get('id')));
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => "集計", 'url' => ['index']];

        return true;
    }

    public function actionIndex($year=null, $month=null)
    {
        $model = new SodanStatistic(['year'=>$year,'month'=>$month]);
        $model->load(Yii::$app->request->queryParams);
        $model->validate();

        $this->view->params['breadcrumbs'][] = ['label' => sprintf("%d年%02d月",$model->year,$model->month)];
        return $this->render('index', [
            'model'        => $model,
        ]);
    }

    public function actionView($id, $year, $month, $target='hpath')
    {
        $model = new SodanStatistic(['year'=>$year,'month'=>$month]);
        if('hpath' === $target)
        {
            $query  = $model->find()->andWhere(['i.homoeopath_id' => $id]);
            $target = \common\models\Customer::findOne($id);
        }
        elseif('branch' === $target)
        {
            $query  = $model->find()->andWhere(['i.branch_id'     => $id]);
            $target = \common\models\Branch::findOne($id);
        }

        return $this->render('view',[
            'model'        => $model,
            'target'       => $target,
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query'      => $query,
                'pagination' => false,
            ])
        ]);
    }

}
