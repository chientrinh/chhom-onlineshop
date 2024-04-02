<?php

namespace common\modules\sodan\controllers;

use Yii;
use common\models\sodan\Client;
use common\models\sodan\Interview;
use common\models\SearchMember;
use \common\models\FileForm;
use \common\models\BinaryStorage;
use common\models\Branch;
use common\models\sodan\Homoeopath;
use yii\helpers\ArrayHelper;

/**
 * ClientController implements the CRUD actions for Client model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/controllers/ClientController.php $
 * $Id: ClientController.php 4137 2019-03-28 05:00:59Z kawai $
 */
class ClientController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => "クライアント", 'url' => ['index']];

        return true;
    }

    /**
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex($format='html')
    {
        $param = Yii::$app->request->queryParams;
        if (isset($param['SearchMember']['branch_id'])) {
            $branch_id = $param['SearchMember']['branch_id'];
        } else {
            if (Yii::$app->id === 'app-backend') {
                $staff_role = (new \yii\db\Query())->select(['*'])->from('mtb_staff_role')->where(['staff_id' => Yii::$app->user->identity->attributes['staff_id']])->one();
                $branch_id = ($staff_role) ? $staff_role['branch_id'] : '';
                $center_branch = Branch::find()->center()->andwhere(['branch_id' => $branch_id])->one();
                $param['SearchMember']['branch_id'] = ($center_branch) ? $branch_id : '';
            }
        }

        $model = new SearchMember();
        $model->load($param);
        $provider = $this->loadProvider($model);
        $searchModel = (Yii::$app->id === 'app-backend') ? $model : [];

        if(('csv' == $format) && ('app-backend' == Yii::$app->id))
            return $this->renderCsv($provider->query);

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($client_id = null)
    {
        // クライアント存在チェック
        if ($client_id && Client::find()->where(['client_id' => $client_id])->one()) {
            $client_id = null;
            Yii::$app->session->addFlash('error', '既に存在するクライアントです');
        }

        $branch_id = null;
        if (Yii::$app->id === 'app-backend') {
            $staff_role = (new \yii\db\Query())->select(['*'])->from('mtb_staff_role')->where(['staff_id' => Yii::$app->user->identity->attributes['staff_id']])->one();
            $branch_id = ($staff_role) ? $staff_role['branch_id'] : '';
        }

        $user  = Yii::$app->user->identity;
        $model = new Client([
            'client_id'     => $client_id,
            'branch_id'     => $branch_id,
            'created_by'    => $user->attributes['staff_id'],
            'create_date'   => date('Y/m/d H:i:s')
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view', 'id' => $model->client_id]);

        return $this->render('create', [
            'model' => $model,
            'branch_id' => $branch_id,
        ]);
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
     * Displays a single Client model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $target=null)
    {
        if('file' == $target)
            return $this->sendContentAsFile($id);

        if('interview' == $target)
            return $this->redirect(['interview/view','id'=>$id]);

        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
            'img' => $this->imageData($model)
        ]);
    }
    
    /**
     * binary_storageテーブルからクライアントの写真データを取得する
     * ない場合は no imageファイルを性別、動物を判定して表示させる
     * @param type $client
     * @return string
     */
    private function imageData($client)
    {
        $data = BinaryStorage::find()->where(['tbl_name' => 'dtb_sodan_client', 'property' => 'photo', 'pkey' => $client->client_id])->orderBy(['create_date' => SORT_DESC])->one();
        if (!$data) {
            if ($client->animal_flg) {
                $basename = 'animal.png';
            } else {
                $basename = ($client->customer->sex->sex_id == '2') ? 'woman.png' : 'man.png';
            }
            $filename = $this->viewPath . '/' . $basename;
            $binary   = file_get_contents($filename);
        } else {
            $filename = $data->basename;
            $binary = $data->data;
        }
        $type     = pathinfo($filename, PATHINFO_EXTENSION);
        $ascii    = 'data:image/' . $type . ';base64,' . base64_encode($binary);
        return $ascii;
    }

    public function actionBook($id)
    {
        $param = Yii::$app->request->queryParams;
        if(! $client = Client::findOne($id)) {
            $client = new Client(['client_id' => $id]);
        }

        $model = new Interview();

        if (!isset($param['Interview']['homoeopath_id']) && $client = Client::findOne($id)) {
            $param['Interview']['homoeopath_id'] = $client->homoeopath_id;
        }

        if (!isset($param['Interview']['branch_id'])) {
            $param['Interview']['branch_id'] = $client->branch_id;
        }
        $branch_id = isset($param['Interview']['branch_id']) ? $param['Interview']['branch_id'] : $client->branch_id;

        $model->load($param);
        $provider = new \yii\data\ActiveDataProvider([
            'query' => Interview::find()->active()
                                        ->vacant()
                                        ->future()
                                        ->andFilterWhere($model->getDirtyAttributes()),
            'sort' => ['defaultOrder'=>['itv_date'=> SORT_ASC, 'itv_time'=> SORT_ASC]],
        ]);

        return $this->render('book', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
            'model'        => $client,
            'branch_id'    => $branch_id
        ]);
    }

    /**
     * @param integer $id
     * @param string  $page
     * @param string  $format
     * @return mixed
     */
    public function actionPrint($id, $page='print', $format='html', $only = '', $itv_id = null)
    {
        $model = $this->findModel($id);

        if('pdf' == $format)
        {
            $this->layout = '/none';
            $html = $this->render($page, ['model' => $model, 'only' => $only, 'itv_id' => $itv_id]);
            $mpdf = new \mPDF('ja', 'A4', 0, '', 5, 5, 5, 5, 0, 0, '');
            $mpdf->writeHtml($html);
            $mpdf->output();
            return;
        }

        return $this->render($page, [
            'model' => $model,
            'only'  => $only
        ]);
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $ret   = null;

        if('skype' == Yii::$app->request->get('attribute'))
            $ret = $model->load(Yii::$app->request->post()) && $model->save();

        elseif('agreement' == Yii::$app->request->get('attribute'))
            $ret = $this->uploadFile($model, 'agreement');

        elseif('binary-storage' == Yii::$app->request->get('attribute'))
            $ret = $this->uploadFile($model, Yii::$app->request->post('property', null));

        elseif(Yii::$app->request->get('attribute'))
            $ret = $this->uploadFile($model, 'questionnaire');

        if($ret)
            return $this->redirect(['view','id'=>$id,'target'=>'client']);

        return $this->render('update',['model' => $model]);
    }

    /**
     * update customer's document
     */
    public function actionFileupload($id, $client_id)
    {
        if(! in_array($id, ['agreement','questionnaire', 'report', 'binaries', 'photo']))
            throw new \yii\web\NotFoundHttpException();

        $model = $this->findModel($client_id);

        if($this->uploadFile($model, $id))
            Yii::$app->session->addFlash('success','アップロードが完了しました');

        return $this->redirect(["{$client_id}"]);
    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    private function findModel($id)
    {
        if($hpath = $this->module->homoeopath)
        {
            $itv = new Interview(['homoeopath_id'=> $hpath->id,
                                  'client_id'    => $id,]);

            // 相談会がなくてもクライアント詳細画面は表示させる
//            if(! $itv->hadMetBefore())
//                throw new \yii\web\NotFoundHttpException('このクライアントはユーザとの関連付けがまだありません');
        }

        if($model = Client::findOne($id))
            return $model;

        $model = new Client(['client_id' => $id]);
        if($model->waitlist || $model->recipes || $model->interviews)
            $model->save();

        return $model;
    }

    private function loadProvider(SearchMember $model)
    {
        $query = Client::find()
                       ->from(Client::tableName() . ' c')
                       ->with('interviews');

        // userが本部ホメオパスなら、自分のクライアントのみ抽出する
        if(Yii::$app->user->identity instanceof \common\models\Customer)
            $query->andWhere(['homoeopath_id' => Yii::$app->user->id]);

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => ['client_id' => SORT_DESC],
            ],
        ]);

        if($id = $model->homoeopath_id) {
            $query->andWhere(['c.homoeopath_id' => $id]);
        }

        if ($model->branch_id) {
            $query->andWhere(['c.branch_id' => $model->branch_id]);
        }

        // 公開クライアント抽出条件
        if (Yii::$app->request->get('client') === 'open') {
            $query->andWhere(['c.ng_flg' => 0]);
        } else if (Yii::$app->request->get('client') === 'close') {
            $query->andWhere(['c.ng_flg' => 1]);
        }

        if(! $model->validate()) // no dirty atttibutes
            return $provider;

        $temp = $model->loadProvider();
        $query->andwhere(['c.client_id' => $temp->query->select('customer_id')]);

        return $provider;
    }

    /**
     * output html as if in csv format
     */
    private function renderCsv($query)
    {
        $widget = \common\widgets\CsvView::begin([
            'charset' => Yii::$app->charset,
            'eol'     => "<br>\n",
            'query'      => $query,
            'header'     => ['client_id','kana','name','branch_id','zip','addr','tel','email','date','hpath'],
            'attributes' => [
                'client_id',
                'customer.kana',
                'customer.name',
                'branch.name',
                'customer.zip',
                'customer.addr',
                'customer.tel',
                'customer.email',
                'itv_date' => function($data)
                {
                    $q = $data->getInterviews()
                              ->active()
                              ->past()
                              ->orderBy(['itv_date'=>SORT_DESC]);

                    if($model = $q->one())
                        return $model->itv_date;
                },
                'hpath' => function($data)
                {
                    return ($data->homoeopath) ? $data->homoeopath->homoeopathname : null;
                },
            ],
        ]);

        ini_set("memory_limit","1G"); // allow to consume up to 1GB for this process
        $widget->run();

        return;
    }

    /*
     * @param integer $file_id
     * @return mixed
     */
    private function sendContentAsFile($file_id)
    {
        if(! $model = BinaryStorage::findOne($file_id))
            throw new \yii\web\NotFoundHttpException();

        $inline = true;
        Yii::$app->response->setDownloadHeaders($model->basename, $model->type, $inline);
        return Yii::$app->response->sendContentAsFile($model->data, $inline);
    }

    /* @return bool */
    private function uploadFile(Client $client, $property)
    {
        $form = new FileForm();
        $form->tgtFile = \yii\web\UploadedFile::getInstance($form, 'tgtFile');
        if(! $form->validate()) {
            Yii::$app->session->addFlash('error', implode(';',$form->firstErrors));
            return false;
        }

        $file = $form->tgtFile;
        $model = new BinaryStorage([
            'tbl_name' => $client->tableName(),
            'pkey'     => $client->client_id,
            'property' => $property,
            'basename' => $file->name,
            'type'     => $file->type,
            'size'     => $file->size,
            'data'     => file_get_contents($file->tempName),
        ]);

        return $model->save();
    }

}
