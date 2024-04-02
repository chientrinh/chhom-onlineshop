<?php

namespace common\modules\sodan\controllers;

use Yii;
use common\models\Customer;
use common\models\sodan\WaitList;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use backend\models\Staff;
use common\models\Branch;
use common\models\sodan\Homoeopath;
use yii\helpers\ArrayHelper;

/**
 * WaitListController implements the CRUD actions for WaitList model.
 */
class WaitListController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => 'キャンセル待ち', 'url' => ['index']];
        return true;
    }

    /**
     * Lists all WaitList models.
     * @return mixed
     */
    public function actionIndex($itv_id = null)
    {
        // 所属拠点でフィルタをかける、ログインユーザの所属拠点を絞り込み状態にする
        // ※センター拠点でない場合は全件出力する
        $param = Yii::$app->request->queryParams;
        if (isset($param['WaitList']['branch_id'])) {
            $branch_id = $param['WaitList']['branch_id'];
        } else {
            $staff_role = (new \yii\db\Query())->select(['*'])->from('mtb_staff_role')->where(['staff_id' => Yii::$app->user->identity->attributes['staff_id']])->one();
            $branch_id = ($staff_role) ? $staff_role['branch_id'] : '';
            $center_branch = Branch::find()->center()->andwhere(['branch_id' => $branch_id])->one();
            $param['WaitList']['branch_id'] = ($center_branch) ? $branch_id : '';
        }

        // キャンセル待ちから登録する場合、相談枠のホメオパスで絞られた状態にする
        if ($itv_id) {
            $itv = \common\models\sodan\Interview::findOne($itv_id);
            $param['WaitList']['homoeopath_id'] = $itv->homoeopath_id;
        }

        $model = new WaitList();
        $model->load($param);
        $provider = $this->loadProvider($model);

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
        ]);
    }

    private function loadProvider($model)
    {
        if($keyword = $model->client_id)
            $model->client_id = null; // andFilterWhere()で無視されるようNULL代入

        $query = WaitList::find();
        if (Yii::$app->request->get('expire') !== 'all') {
            $query->active();
        }
        $query->andFilterWhere($model->dirtyAttributes);

        if($keyword)
        {
            $keyword = mb_convert_kana($keyword, 's');
            $keyword = \common\components\Romaji2Kana::translate($keyword, 'hiragana');
            $keyword = trim($keyword);
            $keyword = explode(' ', $keyword);

            $q2 = Customer::find()->active();
            $q2->andWhere(['or',
                           ['like','CONCAT(name01,name02)', $keyword],
                           ['like','CONCAT(kana01,kana02)', $keyword]]);

            $query->andWhere(['client_id' => $q2->select('customer_id')]);

            $model->client_id = implode(' ', $keyword);
        }

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * Displays a single WaitList model.
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
     * Creates a new WaitList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($itv_id = null)
    {
        $user  = Yii::$app->user->identity;
        $branch_id = null;
        $client_id = null;
        $homoeopath_id = null;

        if (Yii::$app->user->identity instanceof Staff) {
            $staff_role = (new \yii\db\Query())->select(['*'])->from('mtb_staff_role')->where(['staff_id' => Yii::$app->user->identity->attributes['staff_id']])->one();
            $staff_branch = ($staff_role) ? $staff_role['branch_id'] : null;
            $center_branch = Branch::find()->center()->andwhere(['branch_id' => $staff_branch])->one();
            $branch_id = ($center_branch) ? $staff_branch : null;
        }

        if ($itv_id) {
            $itv = \common\models\sodan\Interview::findOne($itv_id);
            $client_id = $itv->client_id;
            $homoeopath_id = $itv->homoeopath_id;
            $branch_id = $itv->branch_id;
        }

        if ($user instanceof Customer) {
            $homoeopath_id = $user->id;
        }

        $model = new WaitList([
            'homoeopath_id'=> $homoeopath_id,
            'expire_date'  => date('Y-m-d', strtotime('+365 day')),
            'client_id'    => $client_id,
            'branch_id'    => $branch_id
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view', 'id' => $model->wait_id]);

        return $this->render('create', [
            'model' => $model,
            'staff_branch' => $branch_id
        ]);
    }

    /**
     * Updates an existing WaitList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $client_id=null)
    {
        $model = $this->findModel($id);

        if($client_id)
            $model->client_id = $client_id;

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view', 'id' => $model->wait_id]);

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionCancelate($id)
    {
        $model = $this->findModel($id);
        $model->cancelate();

        return $this->redirect(['view','id'=>$id]);
    }

    /**
     * ホメオパスリストを動的に作成する
     * @brief  fetch Homoeopath
     * @return JSON of array(Homoeopaths)
     */
    public function actionFetchHomoeopath()
    {
        $branch_id = Yii::$app->request->post('branch_id');
        $query = ($branch_id) ? Homoeopath::find()->active()->multibranch($branch_id) : Homoeopath::find()->active();

        $homoeopaths = ArrayHelper::merge(['' => ''], ArrayHelper::map($query->all(), 'homoeopath_id', 'customer.homoeopathname'));
        return \yii\helpers\Json::encode($homoeopaths);
    }

    /**
     * Finds the WaitList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WaitList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = WaitList::findOne($id);
        if(! $model)
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');

        return $model;
    }
}
