<?php

namespace common\modules\sodan\controllers;

use Yii;
use common\models\sodan\Homoeopath;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Branch;

/**
 * HomoeopathController implements the CRUD actions for Homoeopath model.
 */
class HomoeopathController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        if($hpath = $this->module->homoeopath)
            if('view' !== $action->id)
                throw new \yii\web\ForbiddenHttpException();

        $this->initViewParams();

        return true;
    }

    private function initViewParams()
    {
        $label = 'ホメオパス';

        if($this->module->homoeopath)
            $this->view->params['breadcrumbs'][] = ['label' => $label];
        else
            $this->view->params['breadcrumbs'][] = ['label' => $label,'url' => ['index']];
    }

    /**
     * Lists all Homoeopath models.
     * @return mixed
     */
    public function actionIndex()
    {
        // 所属拠点でフィルタをかける、ログインユーザの所属拠点を絞り込み状態にする
        $param = Yii::$app->request->queryParams;
        if ($param) {
            $branch_id = isset($param['Homoeopath']['branch_id']) ? $param['Homoeopath']['branch_id'] : '';
        } else {
            $staff_role = (new \yii\db\Query())->select(['*'])->from('mtb_staff_role')->where(['staff_id' => Yii::$app->user->identity->attributes['staff_id']])->one();
            $branch_id = ($staff_role) ? $staff_role['branch_id'] : '';
            $center_branch = Branch::find()->center()->andwhere(['branch_id' => $branch_id])->one();
            $param['Homoeopath']['branch_id'] = ($center_branch) ? $branch_id : '';
        }

        // 初期状態は有効なホメオパスのみ表示させる
        if (!isset($param['Homoeopath']['del_flg'])) {
            $param['Homoeopath']['del_flg'] = '0';
        }

        $model = new Homoeopath();
        $model->load($param);
        $provider = $this->module->loadProvider($model);

        // 複数拠点対応:branch_id1～5のいずれかに該当する拠点を表示対象にする
        if (isset($param['Homoeopath']) && $param['Homoeopath']['branch_id']) {
            $provider->query->andFilterWhere(['or',
                                               ['branch_id' => $param['Homoeopath']['branch_id']],
                                               ['branch_id2' => $param['Homoeopath']['branch_id']],
                                               ['branch_id3' => $param['Homoeopath']['branch_id']],
                                               ['branch_id4' => $param['Homoeopath']['branch_id']],
                                               ['branch_id5' => $param['Homoeopath']['branch_id']],
                                             ]);
        }

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model
        ]);
    }

    /**
     * Displays a single Homoeopath model.
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
     * Creates a new Homoeopath model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Homoeopath();

        // ホメオパス存在チェック
        if ($homoeopath = Yii::$app->request->post('Homoeopath')) {
            $homoeopath_id = $homoeopath['homoeopath_id'];
            if ($homoeopath_id && Homoeopath::find()->where(['homoeopath_id' => $homoeopath_id])->one()) {
                Yii::$app->session->addFlash('error', '既に登録されているホメオパスです');
                $model->homoeopath_id = $homoeopath_id;
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->homoeopath_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionAddOpentime($hpath_id)
    {
        $branch_id = null;
        if (Yii::$app->id === 'app-backend') {
            $staff_role = (new \yii\db\Query())->select(['*'])->from('mtb_staff_role')->where(['staff_id' => Yii::$app->user->identity->attributes['staff_id']])->one();
            $branch_id = ($staff_role) ? $staff_role['branch_id'] : '';
        }

        $model = new \common\models\sodan\Open([
            'homoeopath_id' => $hpath_id
        ]);

        $error = false;
        if (Yii::$app->request->post()) {
            if (!Yii::$app->request->post('Open')['week_day']) {
                Yii::$app->session->addFlash('error', "曜日を１つ以上選択してください。");
                $error = true;
            }
        }

        if ($model->load(Yii::$app->request->post()) && !$error) {
            foreach (Yii::$app->request->post('Open')['week_day'] as $day) {
                $insert_model = new \common\models\sodan\Open([
                    'homoeopath_id' => $model->homoeopath_id,
                    'week_day'      => $day,
                    'start_time'    => $model->start_time,
                    'end_time'      => $model->end_time,
                ]);
                $insert_model->save();
            }
            Yii::$app->session->addFlash('success', "公開枠を設定しました。");
            return $this->redirect(['view', 'id' => $model->homoeopath_id]);
        } else {
            return $this->render('add-opentime', [
                'model' => $model,
                'branch_id' => $branch_id
            ]);
        }
    }

    public function actionDeleteOpentime($id)
    {
        $model = \common\models\sodan\Open::findOne($id);
        if ($model->delete()) {
            Yii::$app->session->addFlash('success', '公開枠情報を削除しました');
        }
        return $this->redirect(['view', 'id' => $model->homoeopath_id]);
    }

    /**
     * Updates an existing Homoeopath model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->homoeopath_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Homoeopath model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        $q = \common\models\CustomerMembership::find()->active()->where([
            'customer_id' => $id,
            'membership_id'  => \common\models\Membership::PKEY_CENTER_HOMOEOPATH,
        ]);
        foreach($q->all() as $mship)
            if(! $mship->expire())
                throw new \yii\web\BadRequestHttpException("何かがおかしいです。システム担当者へ連絡してください");

        Yii::$app->session->addFlash('success',"$model->name さんをホメオパス一覧から削除しました");

        return $this->redirect(['index']);
    }

    /**
     * Finds the Homoeopath model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Homoeopath the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Homoeopath::findOne($id);

        if(! $model)
        {
            $model = \common\models\Customer::find()->active()->member(\common\models\Membership::PKEY_CENTER_HOMOEOPATH)->andWhere(['dtb_customer.customer_id'=>$id])->one();

            if(! $model)
                throw new NotFoundHttpException('The requested page does not exist.');

            $model = new Homoeopath(['homoeopath_id' => $id]);
            $model->save();
        }

        return $model;
    }
}
