<?php

namespace common\modules\sodan\controllers;

use Yii;
use \common\models\sodan\Interview;
use \common\models\sodan\InterviewStatus;
use \common\models\sodan\BookTemplate;
use \backend\models\Staff;
use \common\models\Branch;
use yii\helpers\ArrayHelper;
use common\models\sodan\Homoeopath;
use common\models\BinaryStorage;
use common\models\CustomerFamily;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/controllers/InterviewController.php $
 * $Id: InterviewController.php 4142 2019-03-28 08:39:08Z kawai $
 */

class InterviewController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => "相談会", 'url' => ['index?time=0']];

        return true;
    }

    public function actionIndex($time = null, $bill = null, $branch_id = null)
    {
        // 所属拠点でフィルタをかける、ログインユーザの所属拠点を絞り込み状態にする
        // ※センター拠点でない場合は全件出力する
        $param = Yii::$app->request->queryParams;
        if (isset($param['Interview']['branch_id'])) {
            $branch_id = $param['Interview']['branch_id'];
        } else {
            if (Yii::$app->id === 'app-backend') {
                $staff_role = (new \yii\db\Query())->select(['*'])->from('mtb_staff_role')->where(['staff_id' => Yii::$app->user->identity->attributes['staff_id']])->one();
                $branch_id = ($staff_role) ? $staff_role['branch_id'] : '';
                $center_branch = Branch::find()->center()->andwhere(['branch_id' => $branch_id])->one();
                $param['Interview']['branch_id'] = ($center_branch) ? $branch_id : '';
            }
        }

        $user  = Yii::$app->user->identity;
        $model = new Interview([
            'homoeopath_id' => $user instanceof \common\models\Customer ? $user->id : null,
        ]);
        $model->load($param);
        $provider = $this->module->loadProvider($model);

        if ($branch_id) {
            $provider->query->andWhere("dtb_sodan_interview.branch_id = {$branch_id} ");
        }

        if (!isset($param['Interview']['status_id']) || $param['Interview']['status_id'] === '') {
            $provider->query->andWhere(['<>', 'status_id', '9']);
        }

        if($time < 0)
            $provider->query->andWhere(' itv_date <= (DATE_FORMAT(NOW(),"%Y/%m/%d 00:00:00") - INTERVAL 1 day) '); // 履歴
        if('0' === $time)
            $provider->query->today(); // 本日
        if(0 < $time)
            $provider->query->andWhere(' itv_date >= (DATE_FORMAT(NOW(),"%Y/%m/%d 00:00:00") + INTERVAL 1 day) '); // 予定

        if ($bill === 'on') {
            $provider->query->andWhere('( status_id = 3 OR status_id = 4 )');
        }

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
            'branch_id'    => $branch_id
        ]);
    }

    public function actionView($id, $cancel_set = null)
    {
        $model = $this->module->findmodel($id);

        // 「主訴」と「質問票」は予約済になったタイミングで相談終了した最新の相談会からコピーして表示する
        if ($model->client_id && $model->status_id == InterviewStatus::PKEY_READY) {
            $previous_itv = Interview::find()
                    ->where(['client_id' => $model->client_id])
                    ->andWhere(['in', 'status_id', [InterviewStatus::PKEY_DONE, InterviewStatus::PKEY_KARUTE_DONE]])
                    ->orderBy(['itv_date' => SORT_DESC, 'itv_time' => SORT_DESC])
                    ->one();

            if ($previous_itv) {
                $model->complaint = (!$model->complaint) ? $previous_itv->complaint : $model->complaint;
                $model->questionaire = (!$model->questionaire) ? $previous_itv->questionaire : $model->questionaire;
            }
        }

        if ($model->client_id && !$model->product_id) {
            Yii::$app->session->addFlash('success', "相談種別を選択してください");
        }

        return $this->render('view', [
            'model' => $model,
            'cancel_set' => $cancel_set,
            'img'   => ($model->client) ? $this->imageData($model) : ''
        ]);
    }

    /**
     * binary_storageテーブルからクライアントの写真データを取得する
     * ない場合は no imageファイルを性別、動物を判定して表示させる
     * @param type $model
     * @return string
     */
    private function imageData($model)
    {
        $data = BinaryStorage::find()->where(['tbl_name' => 'dtb_sodan_client', 'property' => 'photo', 'pkey' => $model->client_id])->orderBy(['create_date' => SORT_DESC])->one();
        if (!$data) {
            if ($model->sodanclient->animal_flg) {
                $basename = 'animal.png';
            } else {
                $basename = ($model->client->sex->sex_id == '2') ? 'woman.png' : 'man.png';
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

    public function actionTemplateSelect($id, $page='welcome', $format='html')
    {
        $model = new BookTemplate();
        $model->load(Yii::$app->request->queryParams);
        $provider = $this->module->loadProvider($model);

        return $this->render('template-select', [
            'dataProvider' => $provider,
            'searchModel' => $model,
            'id'     => $id,
            'page'   => $page,
            'format' => $format
        ]);
    }

    public function actionPrint($id, $page='welcome', $format='html')
    {
        $model = $this->module->findmodel($id);

        $widget = \common\widgets\doc\sodan\SodanDocument::begin(['model'=>$model]);
        // 予約表印刷
        if ($format === 'pdf' && $page === 'reserve') {
            return $widget->reserve();
        }

        if(! $model->isExpired() && (InterviewStatus::PKEY_DONE != $model->status_id))
        {
            $model->status_id = InterviewStatus::PKEY_DONE;
            $model->update();
        }

        if('pdf' == $format) {
            return $widget->pdf();
        } else {
            return $widget->run();
        }
    }
    
    /**
     * @param integer $id
     * @param string  $page
     * @param string  $format
     * @return mixed
     */
    public function actionPrintKarute($id, $page='print-karute', $format='html')
    {
        $model = $this->module->findModel($id);

        if('pdf' == $format)
        {
            $this->layout = '/none';
            $html = $this->render($page, ['model' => $model]);
            $mpdf = new \mPDF('ja', 'A4', 0, '', 5, 5, 5, 5, 0, 0, '');
            $mpdf->writeHtml($html);
            $mpdf->output();
            return;
        }

        return $this->render($page, [
            'model' => $model
        ]);
    }

    public function actionCancelate($id)
    {
        $model = $this->module->findmodel($id);

        if(! $model->product_id)
            Yii::$app->session->addFlash('error', "相談会種別が指定されていないため、キャンセルできません");
        else
            $model->cancelate();

        return $this->redirect(['view','id'=>$id]);
    }

    public function actionCreate($client_id=null)
    {
        $branch_id = null;
        if (Yii::$app->id === 'app-backend') {
            $staff_role = (new \yii\db\Query())->select(['*'])->from('mtb_staff_role')->where(['staff_id' => Yii::$app->user->identity->attributes['staff_id']])->one();
            $branch_id = ($staff_role) ? $staff_role['branch_id'] : '';
        }

        $user  = Yii::$app->user->identity;
        $model = new Interview([
            'homoeopath_id'=> $user instanceof \common\models\Customer ? $user->id : null,
            'duration'     => Interview::DURATION_60,
            'client_id'    => $client_id,
            'branch_id'    => $branch_id
        ]);

        if($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view','id'=>$model->itv_id]);

        return $this->render('create', [
            'model' => $model,
            'branch_id' => $branch_id
        ]);
    }

    public function actionUpdate($id, $client_id=null)
    {
        $model = $this->module->findModel($id);

        if($client_id)
            $model->client_id = $client_id;

        if($cmd = Yii::$app->request->post('command'))
        {
            if('finish' == $cmd)
                $model->status_id = InterviewStatus::PKEY_DONE;

            if('edit'   == $cmd)
                $model->status_id = InterviewStatus::PKEY_ONGOING;
        }

        if($scenario = Yii::$app->request->get('scenario', null))
        {
            if(! in_array($scenario, array_keys($model->scenarios())))
                throw new \yii\base\UserException('invalid scenario');

            $model->scenario = $scenario;
            $model->validate();
        }

        if($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->post('done_flg')) {
                $model->status_id = InterviewStatus::PKEY_KARUTE_DONE;
            } else if (Yii::$app->request->post('save_flg') && $model->status_id == InterviewStatus::PKEY_KARUTE_DONE) {
                $model->status_id = InterviewStatus::PKEY_DONE;
            }
            if ($model->save()) {
                if (Yii::$app->request->post('recipe_flg')) {
                    return $this->redirect(['admin/create-recipe','id'=>$model->itv_id]);
                }
                $flash_msg = (Yii::$app->request->post('done_flg')) ? 'カルテ作成が完了しました' : 'カルテを保存しました';
                Yii::$app->session->addFlash('success', $flash_msg);
                return $this->redirect(['view','id'=>$model->itv_id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = Interview::findOne($id);
        if ($model->delete()) {
            Yii::$app->session->addFlash('success', '相談会を削除しました');
        }
        return $this->redirect(['index?time=0']);
    }

    /**
     * 相談種別リストを動的に作成する
     * @brief  fetch Products
     * @return JSON of array(Products)
     */
    public function actionFetchProducts()
    {
        $client_id = Yii::$app->request->post('client_id');
        $query = \common\models\Product::find()->sodanProduct()->orderBy(['dtb_product.kana' => SORT_ASC]);

        if ($client_id) {
            $client = \common\models\sodan\Client::findOne($client_id);
            if($client->isAnimal())
                $query->andWhere(['not like','dtb_product.name','小人'])->andWhere(['not like','dtb_product.name','大人']);
            else {
                $query->andWhere(['not like','dtb_product.name','動物']);
                $age = $client->customer->getAge(Yii::$app->request->post('itv_date'));
                if(null === $age) { }
                elseif(13 <= $age) { $query->andWhere(['not like','dtb_product.name','小人']); }
                elseif($age < 13) { $query->andWhere(['not like','dtb_product.name','大人']); }
            }
        }
        $products = ArrayHelper::merge(['' => ''], ArrayHelper::map($query->all(), 'product_id', 'name'));
        return \yii\helpers\Json::encode($products);
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
     * 公開枠情報取得
     * @brief  fetch Open
     * @return JSON of array(Homoeopaths)
     */
    public function actionFetchOpentime()
    {
        $homoeopath_id = Yii::$app->request->post('homoeopath_id');
        $opentime = \common\models\sodan\Open::find()->where(['homoeopath_id' => $homoeopath_id])->asArray()->all();

        return \yii\helpers\Json::encode($opentime);
    }

    public function actionFetchTicket()
    {
        $customer_id = Yii::$app->request->post('client_id');
        $subQuery1 = CustomerFamily::find()->select('parent_id')
                                                   ->where(['child_id'  => $customer_id]);
        $subQuery2 = CustomerFamily::find()->select('child_id')
                                           ->where(['parent_id' => $customer_id]);
        $customer_query = \common\models\Customer::find()
                       ->active()
                       ->andWhere(['OR',
                                   ['customer_id' => $customer_id],
                                   ['customer_id' => $subQuery1],
                                   ['customer_id' => $subQuery2],
                       ])
                       ->select('customer_id')
                       ->distinct();
        $customer_arr = $customer_query->column();
        $results = \common\models\DiscountProductLog::find()->active()->andWhere(['in', 'customer_id', $customer_arr])->andWhere(['used_flg' => 0])->all();

        $tickets = [];
        if ($results) {
            foreach ($results as $result) {
                $tickets[$result->ticket_id] = sprintf('%05d', $result->ticket_id) . ':' . $result->discountProduct->product->name . "(有効期限：" . date('Y/m/d', strtotime($result->expiredate)) . ")";
            }
        }

        return \yii\helpers\Json::encode($tickets);
    }

    /**
     * 定期予定作成画面
     * @return type
     */
    public function actionCreateRegular()
    {
        $model = new Interview([
            'duration' => Interview::DURATION_60
        ]);

        $branch_id = null;
        if (Yii::$app->id === 'app-backend') {
            $staff_role = (new \yii\db\Query())->select(['*'])->from('mtb_staff_role')->where(['staff_id' => Yii::$app->user->identity->attributes['staff_id']])->one();
            $branch_id = ($staff_role) ? $staff_role['branch_id'] : '';
            $model->branch_id = $branch_id;
        }

        $post = Yii::$app->request->post();
        if ($post) {
            $model->load($post);
            $error_flg = false;
            // 曜日未選択はエラー
            if (!isset($post['week_d_list']) && !isset($post['day_list'])) {
                Yii::$app->session->addFlash('error', "作成日または曜日を１つ以上選択してください。");
                $error_flg = true;
            }
            if (!isset($post['itv_time_check'])) {
                Yii::$app->session->addFlash('error', "相談会開始時間を１つ以上選択してください。");
                $error_flg = true;
            }
            if (!$post['Interview']['branch_id']) {
                Yii::$app->session->addFlash('error', "拠点を選択してください。");
                $error_flg = true;
            }
            if (!$post['Interview']['homoeopath_id']) {
                Yii::$app->session->addFlash('error', "ホメオパスを選択してください。");
                $error_flg = true;
            }
            // 確定した相談会（予約済み・相談終了・カルテ完了）が該当月にある場合は一括作成できない
            if (isset($post['week_d_list'])) {
                $check_month = date('Y-m-d', strtotime("first day of {$post['create_m']} {$post['create_y']}"));
                $check_itv = Interview::find()
                                 ->where([
                                     "DATE_FORMAT(itv_date, '%Y/%m')" => date('Y/m', strtotime($check_month)),
                                     'homoeopath_id' => $post['Interview']['homoeopath_id']
                                 ])
                                 ->andWhere(['not in', 'status_id', ['0', '8', '9']])
                                 ->all();
                if ($check_itv) {
                    Yii::$app->session->addFlash('error', "このホメオパスは確定した相談会が既に存在するため、予定を作成できません。");
                    $error_flg = true;
                }
            }
            // 時間帯重複チェック
            if (isset($post['itv_time_check']) && !$this->validateItvTime($post)) {
                $error_flg = true;
            }

            if ($error_flg) {
                return $this->render('create-regular', [
                    'model' => $model,
                    'branch_id' => $branch_id
                ]);
            }

            $db = Yii::$app->db->beginTransaction();
            $rollback = false;
            // 該当ホメオパスの定期予定作成月の既存予定を削除する（予約待ち・予約キャンセルのみ）
            if (isset($post['week_d_list'])) {
                Interview::deleteAll("DATE_FORMAT(itv_date, '%Y/%m') = DATE_FORMAT(:date, '%Y/%m') AND homoeopath_id = :homoeopath_id AND status_id IN ('0', '8')", [
                    ':date' => $check_month,
                    ':homoeopath_id' => $post['Interview']['homoeopath_id'],
                ]);
            }

            // 選択した曜日に相談枠を作成する
            if (isset($post['week_d_list'])) {
                foreach ($post['week_d_list'] as $week_d) {
                    $first_date = date('Y-m-d', strtotime("first {$week_d} of {$post['create_m']} {$post['create_y']}"));
                    $insert_date = $first_date;
                    // 月が変わるまでinsert
                    while(date('m', strtotime($insert_date)) === date('m', strtotime($first_date))) {
                        foreach ($post['itv_time_check'] as $itv_time => $value) {
                            $insert_model = new Interview([
                                'branch_id'     => $post['Interview']['branch_id'],
                                'homoeopath_id' => $post['Interview']['homoeopath_id'],
                                'itv_date'      => $insert_date,
                                'itv_time'      => $post['itv_time_list'][$itv_time],
                                'open_flg'      => $post['open_flg'][$itv_time],
                                'status_id'     => InterviewStatus::PKEY_VACANT,
                                'duration'      => $post['duration_list'][$itv_time] ? : 60 // 未入力だった場合は60分にする
                            ]);
                            $insert_model->save();
                        }
                        $insert_date = date('Y-m-d', strtotime('+1 week', strtotime($insert_date)));
                    }
                }
            }
            
            // 選択した個別作成日に相談枠を作成する
            if (isset($post['day_list'])) {                    
                foreach ($post['day_list'] as $day) {
                    Interview::deleteAll("DATE_FORMAT(itv_date, '%Y/%m/%d') = DATE_FORMAT(:date, '%Y/%m/%d') AND homoeopath_id = :homoeopath_id AND status_id IN ('0', '8')", [
                        ':date' => $day,
                        ':homoeopath_id' => $post['Interview']['homoeopath_id'],
                    ]);
                    foreach ($post['itv_time_check'] as $itv_time => $value) {
                        $day_model = new Interview([
                            'branch_id'     => $post['Interview']['branch_id'],
                            'homoeopath_id' => $post['Interview']['homoeopath_id'],
                            'itv_date'      => $day,
                            'itv_time'      => $post['itv_time_list'][$itv_time],
                            'open_flg'      => $post['open_flg'][$itv_time],
                            'status_id'     => InterviewStatus::PKEY_VACANT,
                            'duration'      => $post['duration_list'][$itv_time] ? : 60 // 未入力だった場合は60分にする
                        ]);
                        $day_model->save();
                    }
                }
            }

            // 登録完了できたらコミット
            if ($rollback) {
                $db->rollBack();
            } else {
                $db->commit();
                Yii::$app->session->addFlash('success', "定期予定を作成しました。");
            }
        }

        return $this->render('create-regular', [
            'model' => $model,
            'branch_id' => $branch_id
        ]);
    }

    /**
     * 登録しようとしている時間帯に重複がないかチェックする
     * @param type $post
     * @return boolean
     */
    private function validateItvTime($post)
    {
        $previous_start = '';
        $previous_end = '';
        foreach ($post['itv_time_check'] as $itv_time => $val) {
            $start_time = strtotime($post['itv_time_list'][$itv_time]);
            $end_time = strtotime("+{$post['duration_list'][$itv_time]} minutes", strtotime($post['itv_time_list'][$itv_time]));

            if ($previous_start && $previous_end && ($start_time < $previous_end && $previous_start < $end_time)) {
                Yii::$app->session->addFlash('error', "時間が重複している相談会があります。");
                return false;
            }
            $previous_start = $start_time;
            $previous_end = $end_time;
        }
        return true;
    }
}
