<?php

namespace frontend\modules\sodan\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/sodan/controllers/KaruteController.php $
 * $Id: KaruteController.php 1638 2015-10-11 14:40:16Z mori $
 */

use Yii;
use common\models\Membership;

class KaruteController extends \yii\web\Controller
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
                    // deny guest users
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    // allow only when user is inhouse homoeopath
                    [
                        'allow' => true,
                        'matchCallback' => function()
                        {
                            return Yii::$app->user->identity->isMemberOf(Membership::PKEY_CENTER_HOMOEOPATH);
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

        $this->view->params['breadcrumbs'][] = ['label'=>'カルテ','url'=>['index']];

        if('index' == $action->id)
            $this->view->params['breadcrumbs'][] = ['label'=>'一覧'];
        if('view' == $action->id)
            $this->view->params['breadcrumbs'][] = ['label'=>'詳細'];
        if('create' == $action->id)
            $this->view->params['breadcrumbs'][] = ['label'=>'追加'];
        if('update' == $action->id)
            $this->view->params['breadcrumbs'][] = ['label'=>'修正'];

        return true;
    }

    public function actionIndex()
    {
        $model = new \common\models\webdb20\Karute([
            'scenario'          => 'search',
            'syoho_homeopathid' => $this->module->homoeopathid,
        ]);
        $model->load(Yii::$app->request->queryParams);

        $dataProvider = $this->loadProvider($model);

        return $this->render('index',[
            'searchModel'  => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id,$target=null)
    {
        if('item' == $target)
            return $this->render('item',[
                'model' => $this->loadKaruteItem($id)
            ]);

        if('print' == $target)
            return $this->render('print',[
                'model' => $this->loadModel($id),
            ]);

        return $this->render('view',[
            'model' => $this->loadModel($id),
        ]);
    }

    private function loadKaruteItem($id)
    {
        $model = \common\models\webdb20\KaruteItem::find()
               ->where(['syoho_homeopathid' => $this->module->homoeopathid])
               ->andWhere(['syohoid' => $id])
               ->with('customer')
               ->one();

        if(! $model)
            throw new \yii\web\NotFoundHttpException('当該ＩＤは見つかりません');

        return $model;
    }

    private function loadModel($id)
    {
        $model = \common\models\webdb20\Karute::find()
               ->where(['syoho_homeopathid' => $this->module->homoeopathid])
               ->andWhere(['karuteid' => $id])
               ->with('customer')
               ->with('items')
               ->one();

        if(! $model)
            throw new \yii\web\NotFoundHttpException('当該ＩＤは見つかりません');

        return $model;
    }

    private function loadProvider(\common\models\webdb20\Karute $model)
    {
        $query = \common\models\webdb20\Karute::find()
               ->where(['syoho_homeopathid' => $this->module->homoeopathid])
               ->andFilterWhere(['AND',
                ['karuteid'          => $model->karuteid   ],
                ['customerid'        => $model->customerid ],
                ['like', 'karute_date', $model->karute_date],
                ['like', 'karute_syuso', mb_convert_encoding($model->karute_syuso, 'CP51932', 'UTF-8')],
               ])
               ->with('customer');

        if($model->customerid)
            $query->andWhere(['customerid'=>$model->customerid]);

        return new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => ['karuteid'=> SORT_DESC],
            ],
        ]);
    }
}
