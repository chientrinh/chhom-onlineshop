<?php

namespace common\modules\invoice\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/invoice/controllers/AdminController.php $
 * $Id: AdminController.php 3848 2018-04-05 09:12:44Z mori $
 */

use Yii;
use \common\models\Invoice;
use \backend\models\AuthForm;
use \yii\helpers\ArrayHelper;

class AdminController extends \backend\controllers\BaseController
{

    public function actionAuth()
    {
        if(Yii::$app->session['invoice'])
            return $this->goBack();


        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new AuthForm();
        if ($model->load(Yii::$app->request->post()) && $model->login())
        {
            Yii::$app->session['invoice'] = true;
            return $this->goBack();
        }
        return $this->render('auth', [
            'model' => $model,
        ]);
    }

    public function setViewOption($action)
    {
        return;
    }

    public function actionIndex($year=null,$month=null)
    {
        \yii\helpers\Url::remember();

        if(!Yii::$app->session['invoice']) {
             return $this->redirect(['admin/auth']);
        }

        if(!$year)
            $year = (1 == date('m')) ? (date('Y') -1) : date('Y');
        if(!$month)
            $month = (1 == date('m')) ? 12 : (date('m') -1);

        $searchModel = new Invoice();
        $searchModel->load(Yii::$app->request->queryParams);

        if (Yii::$app->request->get('format') === 'csv') {
            return $this->renderCsv($year, $month);
        }

        return $this->render('index',[
            'searchModel'  => $searchModel,
            'dataProvider' => $this->loadProvider($year, $month, $searchModel),
            'year'         => $year,
            'month'        => $month,
        ]);
    }

    public function actionCreate($year, $month)
    {
        \yii\helpers\Url::remember();

        $ret = $this->module->initialize($year, $month);

        if(null === $ret)
            Yii::$app->session->addFlash('error',"請求対象はありませんでした");
        elseif($ret)
            Yii::$app->session->addFlash('success',"発行できました");
        else
            Yii::$app->session->addFlash('error',"発行できませんでした。システム管理者に問い合わせてください。");

        return $this->redirect(['index','year'=>$year,'month'=>$month]);
    }

    public function actionPrint($id, $format='html')
    {
        \yii\helpers\Url::remember();

        $model = $this->findModel($id);

        if('pdf' == $format) // generate pdf
            return self::renderPdf($model);

        $html  = \common\widgets\doc\invoice\InvoiceDocument::widget([
            'model' => $model,
        ]);
        $this->layout = '/none';

        $this->view->title  = sprintf('%s | %s | %s | %s', $model->customer->name, date('Y-m',strtotime($model->target_date)), '請求書', Yii::$app->name);

        return $this->renderContent($html);
    }

    public function actionPrintAll($year, $month)
    {

        $controller = new \console\controllers\InvoicePrintController(Yii::$app->controller->id, Yii::$app);
        $controller->actionPrintPdf($year, $month);
/*
        $searchModel = new Invoice();
        $searchModel->load(Yii::$app->request->queryParams);
        $models = $this->loadProvider($year, $month, $searchModel)->query->all();
        $total_count = count($models);
        $success = 0;

        foreach($models as $model)
        {
            if($this->renderPdf($model, false))
                $success++;
        }

        Yii::$app->session->addFlash('success',sprintf("PDF一括出力完了　総数：%s  成功：%s",$total_count, $success));
*/
        return $this->redirect(['index','year'=>$year,'month'=>$month]);
    }

    public function actionSearch($year, $month, $customer_id)
    {
        \yii\helpers\Url::remember();

        $id = Invoice::find()
              ->active()
              ->year($year)
              ->month($month)
              ->andwhere(['customer_id'=>$customer_id])
              ->select('invoice_id')
              ->scalar();

        if(! $id)
            throw new \yii\web\NotFoundHttpException();

        return $this->redirect(['view','id'=>$id]);
    }

    public function actionSendmail($id)
    {
        $model = $this->findModel($id);

        if(\common\components\sendmail\InvoiceMail::notify($model))
        {
            $model->save(false,['updated_by','update_date']);

            if(Yii::$app->request->isAjax)
                return 'ok';
            Yii::$app->session->setFlash('success',"{$model->invoice_id}を送信済みに更新しました");
        }
        else
            Yii::$app->session->setFlash('error',"送信できませんでした");

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionUpdate($year, $month, $customer_id)
    {
        if(true === $this->module->update($year, $month, $customer_id))
            Yii::$app->session->setFlash('success',"更新しました");
        else
            Yii::$app->session->setFlash('error',"更新できませんでした（または更新の必要はありませんでした）。システム管理者に問い合わせてください。");

        return $this->redirect(['search','year'=>$year,'month'=>$month,'customer_id'=>$customer_id]);
    }

    public function actionView($id)
    {
        \yii\helpers\Url::remember();

        $model = $this->findModel($id);

        $this->view->title  = sprintf('%s | %s | %s | %s', $model->customer->name, date('Y-m',strtotime($model->target_date)), '請求書', Yii::$app->name);
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('%04d-%02d', $model->year, $model->month), 'url' => ['index','year'=>$model->year,'month'=>$model->month]];;
        $this->view->params['breadcrumbs'][] = ['label' => $model->customer->name];

        if($model->hasErrors() || $model->isVoid())
            return $this->render('view',['model'=>$model]);

        return $this->renderViaCache('view',[
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        $model = Invoice::findOne($id);
        if(! $model)
            throw new \yii\web\NotFoundHttpException();

        $model->validate();

        return $model;
    }

    protected function loadProvider($year, $month, $searchModel)
    {
        $query = Invoice::find()->active()->andFilterWhere([
            'EXTRACT(YEAR  FROM target_date)' => $year,
            'EXTRACT(MONTH FROM target_date)' => $month,
        ])->with('customer','customer.memberships')
        ->andFilterWhere([
            'AND',
            ['customer_id' => $searchModel->customer_id],
            ['invoice_id'  => $searchModel->invoice_id],
            ['like','due_total',$searchModel->due_total],
            ['like','due_purchase',$searchModel->due_purchase],
            ['like','due_pointing',$searchModel->due_pointing],
            ['like','create_date',$searchModel->create_date],
            ['like','update_date',$searchModel->update_date],
            ['created_by' => $searchModel->created_by],
            ['updated_by' => $searchModel->updated_by],
            ['status' => $searchModel->status],
            ['payment_id' => $searchModel->payment_id],
        ]);
        if($searchModel->is_agency)
        {
            $query->leftJoin('dtb_customer_membership', 'dtb_customer_membership.customer_id = '.Invoice::tableName().'.customer_id');

            if(2 == $searchModel->is_agency) {

               $query->andFilterWhere(['in', 'dtb_customer_membership.membership_id', [\common\models\Membership::PKEY_AGENCY_HJ_A,\common\models\Membership::PKEY_AGENCY_HJ_B,\common\models\Membership::PKEY_AGENCY_HE,\common\models\Membership::PKEY_AGENCY_HP]])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'));

            } else if (1 == $searchModel->is_agency) {

               $query2 = clone $query;

               $query2->andWhere(['in', 'dtb_customer_membership.membership_id', [\common\models\Membership::PKEY_AGENCY_HJ_A,\common\models\Membership::PKEY_AGENCY_HJ_B,\common\models\Membership::PKEY_AGENCY_HE,\common\models\Membership::PKEY_AGENCY_HP]])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))->distinct();

                $query->andFilterWhere(['not in', Invoice::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($query2->asArray()->all(), 'customer_id')]);
            }
            $query->distinct();
        }

        return new \yii\data\ActiveDataProvider([
            'query'=>$query,
        ]);
    }

    private function renderPdf($model, $inline = true)
    {
        \yii\helpers\Url::remember();

        ini_set("memory_limit", "3G");
        set_time_limit(0);

        $widget = \common\widgets\doc\invoice\InvoiceDocument::begin([
            'model' => $model,
        ]);
        $filename = $widget->renderPdf();

        if(!$inline)
            return $filename;


        $inline = true;
        $mime   = 'application/pdf';
        Yii::$app->response->setDownloadHeaders(basename($filename), $mime, $inline);

        return Yii::$app->response->sendFile($filename, $inline);
    }

    protected function renderViaCache($viewFile, $params)
    {

        if($this->view->beginCache($this->id, [
            'dependency' => [
                'class' => \yii\caching\DbDependency::className(),
                'sql'   => '(SELECT MAX(update_date) FROM rtb_invoice ) UNION '
                         . '(SELECT MAX(update_date) FROM dtb_purchase) UNION '
                         . '(SELECT MAX(update_date) FROM dtb_pointing) ',
            ],
            'duration' => 3600, // 1 hour
            'variations' => [
                Yii::$app->request->queryParams,
                \yii\helpers\Json::encode($params),
            ],
        ]))
        {
            // ... generate content here ...
            echo $this->render($viewFile, $params);
            $this->view->endCache();
        }
    }

    public function renderCsv($year, $month)
    {
        \yii\helpers\Url::remember();

        if(!Yii::$app->session['invoice']) {
             return $this->redirect(['admin/auth']);
        }

        $searchModel = new Invoice();
        $searchModel->load(Yii::$app->request->queryParams);

        $provider = $this->loadProvider($year, $month, $searchModel);
        $basename = "invoice_{$year}{$month}.csv";

        return $this->printStatCsv($provider->query->all(), $basename);
    }

    public function printStatCsv($models, $basename)
    {
        $csv = [];
        $widget = new \common\widgets\doc\invoice\InvoiceCsv();

        $csv[]  = implode(',', $widget->header) . $widget->eol;

        foreach($models as $model)
            $csv[] = $widget::widget([
                    'model' => $model,
            ]);

        $csv = implode('', $csv);

        if(0 === strlen($csv))
            $csv = "指定された期間に売上データがありませんでした: \n"
                    . implode(';', \yii\helpers\ArrayHelper::getColumn($models,'purchase_id'));

        $csv = mb_convert_encoding($csv, 'SJIS-WIN', 'UTF-8');

        Yii::$app->response->charset = 'shift_jis';
        Yii::$app->response->sendContentAsFile($csv, $basename, ['inline'=>false, 'mimeType'=>'application/csv']);
        Yii::$app->response->send();
    }
}
