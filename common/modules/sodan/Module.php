<?php
namespace common\modules\sodan;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/Module.php $
 * $Id: Module.php 4115 2019-02-08 07:48:28Z kawai $
 */

use Yii;
use yii\db\ActiveQuery;
use \common\models\sodan\Client;
use \common\models\sodan\Homoeopath;
use \common\models\sodan\Interview;
use \common\models\sodan\InterviewStatus;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'common\modules\sodan\controllers';
    public $homoeopath;

    public function init()
    {
        parent::init();

        Yii::$app->formatter->nullDisplay = '<span class="not-set">(セットされていません)</span>';

        if(Yii::$app->user->identity instanceof \common\models\Customer)
            $this->homoeopath = Yii::$app->user->identity;
    }

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
                        'allow'   => false,
                        'roles'   => ['?'], // deny guest
                    ],
                    [
                        'allow' => true,
                        'matchCallback' => function($rule,$action)
                        {
                            $user = Yii::$app->user->identity;
                            return(($user instanceof \backend\models\Staff &&
                                    $user->hasRole(['reception','manager','wizard'])) ||
                                   ($user instanceof \common\models\Customer &&
                                    $user->isMemberOf(\common\models\Membership::PKEY_CENTER_HOMOEOPATH)));
                        },
                        'denyCallback' => function ($rule, $action)
                        {
                            if('app-backend' === Yii::$app->id)
                                throw new \yii\web\ForbiddenHttpException("アクセス権限(any of reception,manager,wizard)がありません。担当者に連絡してください");
                            throw new \yii\web\NotFoundHttpException();
                        },
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->initViewParams();

        // void past vacant interviews
        $q = Interview::find()->vacant()->past()->select('itv_id');
        if($q->exists())
        {
            $tbl = Interview::tableName();
            $db  = Interview::getDb();
            $db->createCommand()
               ->update($tbl, ['status_id' => InterviewStatus::PKEY_VOID], ['itv_id' => $q->column()])
               ->execute();
        }

        // add unlisted homoeopaths
        $q = Interview::find()->active()
                              ->future()
                              ->select('homoeopath_id')
                              ->distinct(true)
                              ->where(['not in', 'homoeopath_id', Homoeopath::find()->select('homoeopath_id')]);
        if($q->exists())
        {
            $rows = [];
            foreach($q->column() as $value)
                $rows[] = [ $value ];

            $tbl = Homoeopath::tableName();
            $db  = Homoeopath::getDb();
            $db->createCommand()
               ->batchInsert($tbl, ['homoeopath_id'], $rows)
               ->execute();
        }

        return true;
    }

    private function initViewParams()
    {
        $label = '健康相談';

        if($hpath = $this->homoeopath)
            $label .= sprintf(' (%s ホメオパス)', $this->homoeopath->homoeopathname);

        Yii::$app->controller->view->params['breadcrumbs'][] = ['label' => $label,'url' => ["/$this->id/$this->defaultRoute"]];

        $csscode = '
@media print {
  body,h1,h2,h3,td {
      font-size: 10pt;
      padding: 0;
  }
  a[href]:after {
    content: "";
  }
  .breadcrumb, .btn, footer {
      display:none;
  }
}';
        Yii::$app->controller->view->registerCss($csscode);

    }

    public function initModel()
    {
        return new Interview([
            'homoeopath_id' => ($hpath = $this->homoeopath) ? $hpath->id : null,
        ]);
    }

    public function findModel($id)
    {
        $model = Interview::findOne($id);
        if(! $model)
            throw new \yii\web\NotFoundHttpException("相談会が存在しません");

        // 2019/02/08 フロントで担当が変わった場合にも見られない問題のためコメントアウト
//        if(($hpath = $this->homoeopath) && ($hpath->id != $model->homoeopath_id))
//            throw new \yii\web\ForbiddenHttpException("指定された相談会は別のホメオパスが担当しています");

        if($model->client_id && ! Client::findOne($model->client_id))
        {
            $record = new Client(['client_id'=>$model->client_id]);
            $record->save();
        }

        return $model;
    }

    public function loadProvider($model)
    {
        if($model instanceof Homoeopath && isset($model->dirtyAttributes['homoeopath_id'])) {
            $query = $model->find()
                ->joinWith('customer')
                ->andFilterWhere(['or',
                                    ['like', 'CONCAT(dtb_customer.name01,dtb_customer.name02)', preg_replace("/( |　)/", "", $model->dirtyAttributes['homoeopath_id'] )],
                                    ['like', 'dtb_customer.homoeopath_name', preg_replace("/( |　)/", "", $model->dirtyAttributes['homoeopath_id'] )],
                                    ['homoeopath_id' => $model->dirtyAttributes['homoeopath_id']]
                                 ])
                ->andFilterWhere(['schedule' => $model->dirtyAttributes['schedule']])
                ->andFilterWhere(['del_flg' => $model->dirtyAttributes['del_flg']]);
        } else if($model instanceof Interview && !is_null($model->dirtyAttributes['homoeopath_id']) && isset($model->dirtyAttributes['client_id'])) {
            $query = $model->find()
                ->joinWith(['client' => function(ActiveQuery $q) {
                    $q->from(['client' => 'dtb_customer']);
                }])
                ->joinWith(['homoeopath' => function(ActiveQuery $q) {
                    $q->from(['homoeopath' => 'dtb_customer']);
                }]);

                if (Yii::$app->id === 'app-backend')
                    $query->andFilterWhere(['or',
                                        ['like', 'CONCAT(homoeopath.name01,homoeopath.name02)', preg_replace("/( |　)/", "", $model->dirtyAttributes['homoeopath_id'] )],
                                        ['like', 'homoeopath.homoeopath_name', preg_replace("/( |　)/", "", $model->dirtyAttributes['homoeopath_id'] )],
                                        ['like', 'homoeopath.homoeopath_name', $model->dirtyAttributes['homoeopath_id']],
                                        ['homoeopath_id' => $model->dirtyAttributes['homoeopath_id']]
                                     ]);

                $query->andFilterWhere(['or',
                                    ['like', 'CONCAT(client.name01, client.name02)', preg_replace("/( |　)/", "", $model->dirtyAttributes['client_id'] )],
                                    ['dtb_sodan_interview.client_id' => $model->dirtyAttributes['client_id']]
                                 ])
                ->andFilterWhere(['dtb_sodan_interview.branch_id' => $model->dirtyAttributes['branch_id']])
                ->andFilterWhere(['like', 'itv_date', $model->dirtyAttributes['itv_date']])
                ->andFilterWhere(['product_id' => $model->dirtyAttributes['product_id']])
                ->andFilterWhere(['status_id' => $model->dirtyAttributes['status_id']]);

                // 事務欄とお会計はバックヤードのみ
                if (Yii::$app->id === 'app-backend') {
                    $query->andFilterWhere(['like', 'officer_use', $model->dirtyAttributes['officer_use']])
                           ->andFilterWhere(['purchase_id' => $model->dirtyAttributes['purchase_id']]);
                }
        } else {
            if (Yii::$app->id === 'app-backend')
                $query = $model->find()
                           ->andFilterWhere($model->dirtyAttributes);
            else
                $query = $model->find();
        }

        if($hpath = $this->homoeopath)
            if($model->hasAttribute('homoeopath_id'))
                $query->joinWith(['sodanclient'])->andWhere(['dtb_sodan_client.homoeopath_id' => $model->dirtyAttributes['homoeopath_id']]);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => [implode(',',$model->primaryKey()) => SORT_DESC],
            ],
        ]);

        return $dataProvider;
    }

}
