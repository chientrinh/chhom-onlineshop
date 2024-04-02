<?php

namespace common\modules\sodan\controllers;

use Yii;
use \yii\helpers\ArrayHelper;
use \yii\helpers\Json;
use \backend\models\Staff;
use \common\models\Branch;
use \common\models\sodan\Holiday;
use \common\models\sodan\Interview;
use \common\models\sodan\InterviewStatus;
use common\models\Customer;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/controllers/CalendarController.php $
 * $Id: CalendarController.php 4148 2019-03-29 07:54:49Z kawai $
 */

class CalendarController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only'  => ['create-interview','toggle-holiday'],
                'rules' => [
                    [
                        'allow' => true,
                        'verbs' => ['POST'],
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

        $param_branch_id = (Yii::$app->request->get('branch_id')) ? '?branch_id=' . Yii::$app->request->get('branch_id') : '';
        $this->view->params['breadcrumbs'][] = ['label' => "カレンダー", 'url' => ['index' . $param_branch_id]];

        return true;
    }

    /**
     * @brief show calendar (can add Interviews)
     */
    public function actionIndex($hpath_id=null)
    {
        if(! Yii::$app->user->identity instanceof Staff)
            $hpath_id = Yii::$app->user->id;

        return $this->render('index',['hpath_id'=>$hpath_id]);
    }

    /**
     * @brief show calendar (can add Interviews)
     */
    public function actionReserve($hpath_id=null)
    {
        if (!$start_date = Yii::$app->request->get('start_date'))
            $start_date = date('Y-m-d');

        if(! Yii::$app->user->identity instanceof Staff)
            $hpath_id = Yii::$app->user->id;

        $user  = Yii::$app->user->identity;
        $model = new Interview([
            'homoeopath_id'=> $user instanceof \common\models\Customer ? $user->id : null,
            'duration'     => Interview::DURATION_60,
            'client_id'    => Yii::$app->request->get('client_id'),
            'branch_id'    => Yii::$app->request->get('branch_id', 0)
        ]);

        if ($post = Yii::$app->request->post()) {
            if ($itv_id = $post['Interview']['itv_id']) {
                $model = Interview::findOne($itv_id);
            } else {
                $model = new Interview();
            }

            if($model->load($post) && $model->save()) {
                Yii::$app->session->addFlash('success', '相談会を保存しました');
                $this->redirect(['reserve', 'hpath_id' => $hpath_id, 'start_date' => $start_date, 'client_id' => Yii::$app->request->get('client_id')]);
            }
        }
        return $this->render('reserve',['hpath_id' => $hpath_id, 'start_date' => $start_date, 'client_id' => Yii::$app->request->get('client_id'), 'model' => $model]);
    }

    /**
     * @brief show calendar (can add Interviews)
     */
    public function actionDelete($hpath_id=null)
    {
        if(! Yii::$app->user->identity instanceof Staff)
            $hpath_id = Yii::$app->user->id;

        if (Yii::$app->request->post()) {
            $itv_id_list = Yii::$app->request->post('itv_id');
            foreach ($itv_id_list as $itv_id) {
                if ($model = Interview::findOne($itv_id)) {
                    $model->delete();
                }
            }
            Yii::$app->session->addFlash('success', '選択した相談枠を削除しました。');
        }

        return $this->render('delete', ['hpath_id' => $hpath_id]);
    }

    /**
     * @brief show calendar (can add/remove Holidays)
     */
    public function actionHoliday($hpath_id=null)
    {
        return $this->render('holiday',['hpath_id'=>$hpath_id]);
    }

    public function actionHolidaySetting($id=null, $hpath_id=null)
    {
        $branch_id = null;
        if (Yii::$app->id === 'app-backend') {
            $staff_role = (new \yii\db\Query())->select(['*'])->from('mtb_staff_role')->where(['staff_id' => Yii::$app->user->identity->attributes['staff_id']])->one();
            $branch_id = ($staff_role) ? $staff_role['branch_id'] : '';
        }

        if ($id)
            $holiday = Holiday::findOne ($id);
        else
            $holiday = new Holiday([
                'date' => date('Y-m-d')
            ]);

        if($holiday->load(Yii::$app->request->post()) && $holiday->save()) {
            Yii::$app->session->addFlash('success', '休業日を設定しました');
            return $this->redirect(['holiday-setting']);
        }
        return $this->render('holiday-setting', ['hpath_id' => $hpath_id, 'model' => $holiday, 'branch_id' => $branch_id]);
    }

    /**
     * @brief  create Interview (called via Ajax)
     * @return JSON of fullcalendar/Event | Exception
     */
    public function actionCreateInterview()
    {
        $model = new Interview(['duration'=>60,'status_id'=>InterviewStatus::PKEY_VACANT]);

        if($model->load(Yii::$app->request->post(),'') && $model->save()) {
            return Json::encode(self::itv2event($model));
        } else {
            throw new \yii\web\BadRequestHttpException(implode(';', $model->firstErrors));
        }
    }

    public function actionFetchInterview()
    {
        if ($itv_id = Yii::$app->request->post('itv_id')) {
            $itv = Interview::find()->where(['itv_id' => $itv_id])->asArray()->one();
            $homoeopath = Customer::find()->where(['customer_id' => $itv['homoeopath_id']])->select(['homoeopath_name AS homoeopathname, CONCAT(name01, name02) AS homoeopath_name2'])->asArray()->one();
            $itv = array_merge($itv, $homoeopath);
            // 顧客情報をマージする
            if ($itv['client_id']) {
                $customer = Customer::find()->where(['customer_id' => $itv['client_id']])->asArray()->one() ? : array();
                $pref = \common\models\Pref::find()->where(['pref_id' => $customer['pref_id']])->select(['name AS pref_name'])->asArray()->one() ? : array();
                $itv = array_merge($itv, $customer, $pref);
            }
            if ($itv['product_id']) {
                $product = \common\models\Product::find()->where(['product_id' => $itv['product_id']])->select('name AS product_name')->asArray()->one();
                $itv = array_merge($itv, $product);
            }
            return Json::encode($itv);
        }

        if (!$client_id = Yii::$app->request->post('client_id'))
            return false;

        $client = \common\models\sodan\Client::findOne($client_id);
        $interviews = Interview::find()->active()->where(['client_id' => $client_id])->orderBy(['itv_date' => SORT_DESC])->limit(5)->all();

        if ($interviews) {
            $result = [];
            $result['parent'] = false;
            $result['charge_homoeopath'] = $client->homoeopath_id;
            if ($client->client->parent) {
                $result['parent'] = true;
                $result['parent_name'] = $client->client->parent->name;
                $result['parent_tel'] = $client->client->parent->tel;
            }
            foreach ($interviews as $key => $interview) {
                $result[$key] = [
                    'itv_date'    => "{$interview->itv_date} {$interview->itv_time}",
                    'homoeopath'  => $interview->homoeopath->homoeopathname,
                    'product_name'=> ($interview->product) ? $interview->product->name : '',
                    'status_name' => $interview->status->name
                ];
            }
            return Json::encode($result);
        }

        return [];
    }

    /**
     * @brief  fetch Interviews and Holidays
     * @return JSON of array(Calendar Event)
     */
    public function actionFetchData($start,$end,$branch_id=null,$hpath_id=null)
    {
        if(! Yii::$app->user->identity instanceof Staff)
            $hpath_id = Yii::$app->user->id;
        elseif(! $hpath_id) { $hpath_id = null; }

        $query = Interview::find()->active()
                         ->andWhere(['and',
                                     ['>=', 'itv_date', $start],
                                     ['<=', 'itv_date', $end],
                         ]);
        if($hpath_id)
            $query->andWhere(['homoeopath_id'=>$hpath_id]);

        $branches = ArrayHelper::map(Branch::find()->center()->all(), 'branch_id', 'name');
        if($branch_id &&
            ArrayHelper::keyExists($branch_id, $branches, false)
        )
            $query->andWhere(['branch_id' => $branch_id]);

        $rows = [];

        $query_results = $query->asArray()->all();
        $homoeopath_ids = ArrayHelper::getColumn($query_results, 'homoeopath_id');
        $client_ids = ArrayHelper::getColumn($query_results, 'client_id');
        $homoeopaths = \common\models\Customer::find()->where(['customer_id' => $homoeopath_ids])->asArray()->all();
        $homoeopath_list = ArrayHelper::map($homoeopaths, 'customer_id', function($element) { return $element; });
        $clients = \common\models\Customer::find()->where(['customer_id' => $client_ids])->asArray()->all();
        $client_list = ArrayHelper::map($clients, 'customer_id', function($element) { return $element; });

        foreach($query->asArray()->all() as $model)
        {
            $rows[] = self::itv2event($model, $branches, $homoeopath_list, $client_list);
        }

        $query = Holiday::find()
                        ->with('homoeopath')
                        ->active()
                        ->andWhere(['and',
                                    ['>=', 'date', $start],
                                    ['<=', 'date', $end],
                        ]);
        if($hpath_id)
            $query->andWhere(['or',
                              ['homoeopath_id'=> null ],
                              ['homoeopath_id'=> $hpath_id]]);

        foreach($query->all() as $model)
        {
            if (!$branch_id || !(isset($model->homoeopath->branch_id))) {
                $rows[] = self::holiday2event($model);
            } else if (isset($model->homoeopath->branch_id) && ($model->homoeopath->branch_id == $branch_id)) {
                $rows[] = self::holiday2event($model);
            }

        }
        return Json::encode($rows);
    }

    public function actionFetchCount($start, $end, $branch_id = null, $hpath_id = null)
    {
        if(! Yii::$app->user->identity instanceof Staff)
            $hpath_id = Yii::$app->user->id;
        elseif(! $hpath_id) { $hpath_id = null; }

        // 予約待ちを取得
        $query = Interview::find()
                         ->andWhere(['and',
                                     ['>=', 'itv_date', $start],
                                     ['<=', 'itv_date', $end],
                         ])
                         ->andWhere(['status_id' => InterviewStatus::PKEY_VACANT]);
        if($hpath_id)
            $query->andWhere(['homoeopath_id'=>$hpath_id]);

        $branches = ArrayHelper::map(Branch::find()->center()->all(), 'branch_id', 'name');
        if($branch_id &&
            ArrayHelper::keyExists($branch_id, $branches, false)
        )
            $query->andWhere(['branch_id' => $branch_id]);

        $query->select('count(*) as count, itv_date')
              ->groupBy('itv_date');

        $rows = [];
        foreach($query->asArray()->all() as $model)
        {
            if (strtotime($model['itv_date']) < time())
                $time = -1;
            else if (strtotime($model['itv_date']) > time())
                $time = 1;
            else
                $time = 0;

            // キャンセル待ちがあれば「★」を付ける
            $wait_list = \common\models\sodan\WaitList::find()->active()
                    ->where(['>=', 'expire_date', $end])
                    ->select(['count(*) AS count'])
                    ->groupBy(['expire_date']);

            if ($hpath_id)
                $wait_list->andWhere (['homoeopath_id' => $hpath_id]);

            $exist_waitlist = ($wait_list->exists()) ? '★' : null;

            $rows[] = [
                    'id'      => '',
                    'allDay'  => 1,
                    'start'   => $model['itv_date'],
                    'allDay' => '',
                    'title'  => '予約待ち:' . $model['count'] . '件' . " {$exist_waitlist}",
                    'url'    => \yii\helpers\Url::to(['interview/index?time=' . $time]),
                    'editable'=> false,
                    'color'       => 'white',
                    'textColor'   => 'black',
                    'borderColor' => 'black',
                ];
        }

        // 予約済みを取得
        $ready_query = Interview::find()
                         ->andWhere(['and',
                                     ['>=', 'itv_date', $start],
                                     ['<=', 'itv_date', $end],
                         ])
                         ->andWhere(['between', 'status_id', InterviewStatus::PKEY_READY, InterviewStatus::PKEY_KARUTE_DONE]);
        if($hpath_id)
            $ready_query->andWhere(['homoeopath_id'=>$hpath_id]);

        if($branch_id && ArrayHelper::keyExists($branch_id, $branches, false))
            $ready_query->andWhere(['branch_id' => $branch_id]);

        $ready_query->select('count(*) as count, itv_date')
                    ->groupBy('itv_date');

        foreach($ready_query->asArray()->all() as $model)
        {
            if (strtotime($model['itv_date']) < time())
                $time = -1;
            else if (strtotime($model['itv_date']) > time())
                $time = 1;
            else
                $time = 0;

            $rows[] = [
                    'id'      => '',
                    'allDay'  => 1,
                    'start'   => $model['itv_date'],
                    'allDay' => '',
                    'title'  => '予約済み:' . $model['count'] . '件',
                    'url'    => \yii\helpers\Url::to(['interview/index?time=' . $time]),
                    'editable'=> false,
                ];
        }

        // 休業日を取得
        $holiday_query = Holiday::find()->active();
        if($hpath_id)
            $holiday_query->andWhere("(homoeopath_id = {$hpath_id} OR homoeopath_id IS NULL)");

        foreach($holiday_query->asArray()->all() as $model)
        {
            $name = '';
            if ($model['homoeopath_id']) {
                $hpath = \common\models\sodan\Homoeopath::findOne($model['homoeopath_id']);
                $name = "：{$hpath->customer->homoeopathname}";
            }

            $rows[] = [
                    'id'      => '',
                    'allDay'  => $model['all_day'],
                    'start'   => "{$model['date']} {$model['start_time']}",
                    'end'     => "{$model['date']} {$model['end_time']}",
                    'title'   => $model['title'] ? $model['title'] . $name : '休業日' . $name,
                    'url'     => \yii\helpers\Url::to(['calendar/holiday-setting', 'id' => $model['id']]),
                    'editable'=> false,
                    'color'       => 'gray',
                    'textColor'   => 'white',
                    'borderColor' => 'white',
                ];
        }

        return Json::encode($rows);
    }

    /**
     * @brief  add or remove Holiday
     * @return JSON of a Calendar Event model
     */
    public function actionToggleHoliday()
    {
        $date = Yii::$app->request->post('date');
        $hid  = Yii::$app->request->post('hid', null);
        if(! $hid)
             $hid = null;

        if(! $model = Holiday::find()->where(['date'=>$date,'homoeopath_id'=>$hid])->one())
             $model = new Holiday(['date'=>$date,'homoeopath_id'=>$hid,'active'=>1]);

        if(! $model->isNewRecord)
            $model->active = $model->active ? 0 : 1;

        if(! $model->save())
            throw new \yii\web\BadRequestHttpException(implode(';',$model->firstErrors).implode(';',$model->attributes));

        return Json::encode(self::holiday2event($model));
    }

    /*
     * @brief convert a Holiday to Calendar Event
     */
    private function holiday2event(Holiday $model)
    {
        $param = [
            'id'      => $model->id,
            'allDay'  => $model->all_day,
            'title'   => $model->title,
            'start'   => "{$model->date} {$model->start_time}",
            'end'     => "{$model->date} {$model->end_time}",
            'editable'=> '', // false
            'color'       => '#ccccb3',
            'textColor'   => 'black',
            'overlap'     => 1,//true
            'rendering'   => 'background',
            'url'    => \yii\helpers\Url::to(['holiday-setting','id' => $model->id]),
        ];

        if($hpath = $model->homoeopath)
        {
            $param['title'] .= '：' . $hpath->customer->homoeopathname;
            $param['rendering'] = ''; // foreground
        }

        return $param;
    }

    /*
     * @brief convert an Interview to Calendar Event
     */
    private function itv2event($model, $branches, $homoeopaths, $clients)
    {
        $branch_id = $model['branch_id'];
        $homoeopath_id = $model['homoeopath_id'];
        $client_id = $model['client_id'];
        $itv_id = $model['itv_id'];
        $branch = preg_replace('/日本ホメオパシーセンター|総?本部/u','', $branches[$branch_id]) . ' ';
        $hpath  = $homoeopath_id  ?  $homoeopaths[$homoeopath_id]['homoeopath_name'] : null;
        $client = $client_id ? ' : '. "{$clients[$client_id]['name01']} {$clients[$client_id]['name02']}" 
                                    . " ({$clients[$client_id]['kana01']} {$clients[$client_id]['kana02']})"
                             : null;
        // Expiredかは、InterViewStatus::PKEY_CANCEL(8)か否かで判定できる
        $status_id = $model['status_id'];
        $expired = $status_id == 8 ? true : false;
        $itv_date = $model['itv_date'];
        $itv_time = $model['itv_time'];
        // InterView getEndTime()
        $sec = $model['duration'] * 60;
        $endTime = strtotime($itv_date .' '. $itv_time) + $sec;
        $title = "{$model['duration']}分\n{$branch}{$hpath}{$client}\n事務欄:{$model['officer_use']}";

        $param = [
            'id'     => $itv_id,
            'allDay' => '',
            'start'  => $itv_date .' '. $itv_time,
            'end'    => date('Y-m-d H:i:s', $endTime),
            'title'  => $title,
            'url'    => \yii\helpers\Url::to(['interview/view','id'=>$itv_id]),
            'editable'=> false,
            'status' => $status_id
        ];
        $option = [
            'expired' => [
                'color'       => 'gray',
                'textColor'   => 'white',
                'borderColor' => 'white',
                'editable'    => '', // false
            ],
            'vacant' => [
                'color'       => 'white',
                'textColor'   => 'black',
                'borderColor' => 'black',
            ],
            'occupied' => [
                'editable'    => '', // false
            ],
            'open' => [
                'color'       => 'yellow',
                'textColor'   => 'black',
                'borderColor' => 'black',
            ],
        ];
        if($expired)
           $param = ArrayHelper::merge($param, $option['expired']);
        elseif ($model['open_flg'])
           $param = ArrayHelper::merge($param, $option['open']);
        elseif(! $client)
           $param = ArrayHelper::merge($param, $option['vacant']);
        else
           $param = ArrayHelper::merge($param, $option['occupied']);

        return $param;
    }
}
