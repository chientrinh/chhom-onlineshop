<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/PurchaseController.php $
 * $Id: PurchaseController.php 3286 2017-05-11 08:21:22Z kawai $
 */

namespace backend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\Purchase;
use backend\models\SearchPurchase;
use backend\models\RefundForm;

use \common\widgets\doc\purchase\ChainstoreDocument;
use \common\widgets\doc\purchase\PurchaseDocument;

/**
 * PurchaseController implements the CRUD actions for Purchase model.
 */
class PurchaseController extends BaseController
{
    const CSV_NAME_YUPACK    = '\common\widgets\doc\purchase\YuPackPrintCsv';
    const CSV_NAME_YAMATO    = '\common\widgets\doc\purchase\YamatoCsv';

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        return true;
    }

    /**
     * Lists all Purchase models.
     * @return mixed
     */
    public function actionIndex($company_id=null)
    {
        $model  = new SearchPurchase();

        $params = Yii::$app->request->queryParams;

        if(! Yii::$app->user->can('viewSales')) // user is tenant
            $company_id = Yii::$app->user->identity->company_id;

        if($company_id)
        {
            $model->company_id = $company_id;
            ArrayHelper::remove($params, sprintf('%s[company_id]',$model->formName()));
        }

        $provider = $model->search($params);

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
        ]);
    }

    /**
     * Displays a single Purchase model.
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
     * Cancel a Purchase
     * @param integer $id
     * @return mixed
     */
    public function actionCancel($id)
    {
        $model = $this->findModel($id);
        $model->cancelate();

        if(0 == $model->branch_id) // オンラインショップで注文された
        {
            $mailer = new \common\components\sendmail\PurchaseMail(['model'=>$model]);
            $mailer->canceled();
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionRefund($id)
    {
        $model = $this->findModel($id);
        $input = new RefundForm(['purchase_id' => $id]);
        $input->validate(['purchase_id']);

        if(Yii::$app->request->isPost)
        {
            $input->quantity = ArrayHelper::getValue(Yii::$app->request->bodyParams, 'quantity');
            $input->note     = ArrayHelper::getValue(Yii::$app->request->bodyParams, 'note');

            if($purchase_id = $input->save())
            {
                Yii::$app->session->addFlash('success', "返品を起票しました");
                return $this->redirect(['view', 'id' => $purchase_id]);
            }
        }

        return $this->render('refund',['model'=>$model,'input'=>$input]);
    }

    public function actionSendmail($id)
    {
        $model = $this->findModel($id);
        $user  = Yii::$app->user->identity;
        $form  = new \common\components\sendmail\MailForm([
            'recipient'=> $model->email,
            'subject'  => sprintf("豊受モール ご注文について[%06d]", $model->purchase_id),
            'sender'   => $user ? $user->email : null,
            'table'    => $model->tableName(),
            'pkey'     => $id,
        ]);

        if($form->load(Yii::$app->request->post()))
        {
            if(true != $form->send())
                break;

            Yii::$app->session->addFlash('success', "メールを送信しました");

            $model->save(false, ['update_date']);
            return $this->redirect(['view','id'=>$id,'#'=>'mail-log']);
        }

        return $this->render('sendmail',['purchase'=>$model,'model'=>$form]);
    }

    /**
     * Update a Purchase status
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        // POSTの取得
        $post = Yii::$app->request->post();
        
        if($model->load($post))
        {
            // 発送状態の変更で「1 : 発送済」とされた際に、発送日の有無をチェック、初期値（NULL）ならセットする
            if(isset($post['Purchase']['shipped']) && $post['Purchase']['shipped'] == 1 && $model->shipping_date == null) {
                $model->shipping_date = date('Y-m-d H:i:s');
            }
            $model->total_charge = $model->subtotal
                                 + $model->tax
                                 + $model->postage
                                 + $model->handling
                                 - $model->point_consume
                                 - $model->discount;

            if(! $model->validate()) // 注意喚起のため、どこがおかしいか表示する
                Yii::$app->session->addFlash('error', \yii\helpers\Html::errorSummary($model));

            if($model->save(false)) // エラーがあっても、とにかく保存する
                Yii::$app->session->addFlash('success','更新しました');
        }

        // return to the last page the user was on.
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Delete a Purchase
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = \common\models\PurchaseStatus::PKEY_VOID;
        $model->save(false);

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Displays a single Purchase model.
     * @param integer $id
     * @return mixed
     */
    public function actionReceipt($id)
    {
        $html = \common\widgets\doc\purchase\Receipt::widget([
            'model' => $this->findModel($id),
        ]);

        $this->layout = '/none';
        return $this->renderContent($html);
    }

    public function actionPrint($id = null, $format='html', $target='auto')
    {
        if(! $id && ! $selection = Yii::$app->request->get('selection'))
            throw new \yii\web\BadRequestHttpException('注文IDが指定されていません');

        if($id) // print single model
        {
            $model  = $this->findModel($id);
            $widget = new PurchaseDocument(['model'=>$model, 'target'=>$target]);

            if('chainstore'== $target)
                $widget = ChainstoreDocument::begin(['model'=>$model]);

            $html   = $widget->run();
            $pdf    = $widget->pdffile;

            if(('pdf' == $format) && is_file($pdf))
                return Yii::$app->response->sendFile($pdf, basename($pdf), ['inline'=>true]);

            $this->layout = '/none';
            return $this->renderContent($html);
        }

        $models   = \common\models\Purchase::findAll(['purchase_id'=>$selection]);
        $basename = Yii::$app->request->get('basename', \backend\modules\casher\Module::getPrintBasename());

        $this->batchPrint($models, $basename);
    }
     
    public function actionPrintCsv($id = null)
    {
        if(! $id && ! $selection = Yii::$app->request->get('selection'))
            throw new \yii\web\BadRequestHttpException('注文IDが指定されていません');
        if($id)
            $selection = [ $id, ];

        $models   = \common\models\Purchase::findAll(['purchase_id'=>$selection]);
        $basename = Yii::$app->request->get('basename', \backend\modules\casher\Module::getPrintBasename());

        return $this->printYamatoCsv($models, $basename);
    }

    /**
     * 注文一覧画面のCSV出力機能（ゆうプリ用）
     * 
     * @param  array  $id　注文ID
     * @return 出力CSV
     */
    public function actionPrintCsvForYuPrint($id = null)
    {
        if(! $id && ! $selection = Yii::$app->request->get('selection'))
            throw new \yii\web\BadRequestHttpException('注文IDが指定されていません');
        if($id)
            $selection = [ $id, ];

        $models   = \common\models\Purchase::findAll(['purchase_id'=>$selection]);
        $basename = Yii::$app->request->get('basename', \backend\modules\casher\Module::getPrintBasename());

        return $this->printYuPrintCsv($models, $basename);
    }

    public function actionPrintLabel($id = null, $target='remedy')
    {
        if(! $id && ! $selection = Yii::$app->request->get('selection'))
            throw new \yii\web\BadRequestHttpException('注文IDが指定されていません');
        if($id)
            $selection = [ $id, ];

        $models   = \common\models\Purchase::findAll(['purchase_id'=>$selection]);
        $basename = Yii::$app->request->get('basename', \backend\modules\casher\Module::getPrintBasename());

        if('remedy' == $target)
            return $this->printRemedyLabel($models, $basename);

        elseif('price' == $target)
            return $this->printProductPrices($models, $basename);

        //elseif('sticker' == $target)
        return $this->printProductStickers($models, $basename);
    }

    private function batchPrint($models, $basename)
    {
        if(! $pdffile = \common\widgets\doc\purchase\PurchaseDocument::getMergedPdf($models))
            throw new \yii\web\ServerErrorHttpException('application failed to generate united pdf');
        if(! is_file($pdffile))
            echo 'was not generated';

        $basename .= '.pdf';

        Yii::$app->response->sendFile($pdffile, $basename, ['inline'=>false]);
        Yii::$app->response->send();
    }

    protected function printProductPrices($models, $basename)
    {
        $widget  = new \common\widgets\doc\purchase\ProductStickers([
            'models'      => $models,
            'fieldConfig' => ['layout' => '{name}{price}{barcode}'],
        ]);
        $output  = $widget->run();

        $inline  = true;
        $mime    = 'application/pdf';
        Yii::$app->response->setDownloadHeaders(basename($basename), $mime, $inline);
        return Yii::$app->response->sendFile($output, $inline);
    }

    private function printProductStickers($models, $basename)
    {
        $widget  = new \common\widgets\doc\purchase\ProductStickers(['models'=>$models]);
        $output  = $widget->run();

        $inline  = true;
        $mime    = 'application/pdf';
        Yii::$app->response->setDownloadHeaders(basename($basename), $mime, $inline);
        return Yii::$app->response->sendFile($output, $inline);
    }

    private function printRemedyLabel($models, $basename)
    {
        $basename .= '.html';
        $html      = [];

        foreach($models as $model)
            $html[] = \common\widgets\doc\purchase\RemedyLabels::widget([
                'model' => $model,
            ]);

        $html = implode('', $html);

        if(0 === strlen($html))
            $html = "指定された注文IDに滴下レメディーはありませんでした: \n"
                  . implode(';', ArrayHelper::getColumn($models,'purchase_id'));

        Yii::$app->response->sendContentAsFile($html, $basename, ['inline'=>false]);
        Yii::$app->response->send();
    }

    private function printYamatoCsv($models, $basename)
    {
        $basename .= '.csv';
        $csv = [];

        $widget = new \common\widgets\doc\purchase\YamatoCsv();
        $csv[]  = implode(',', $widget->header) . $widget->eol;

        foreach($models as $model)
            $csv[] = \common\widgets\doc\purchase\YamatoCsv::widget([
                'model' => $model,
            ]);

        $csv = implode('', $csv);

        if(0 === strlen($csv))
            $csv = "指定された注文IDのすべてに配送先の指定がありませんでした: \n"
                  . implode(';', ArrayHelper::getColumn($models,'purchase_id'));

        $csv = mb_convert_encoding($csv, 'SJIS-WIN', 'UTF-8');

        Yii::$app->response->charset = 'shift_jis';
        Yii::$app->response->sendContentAsFile($csv, $basename, ['inline'=>false, 'mimeType'=>'text/csv']);
        Yii::$app->response->send();
    }

    private function printYuPrintCsv($models, $basename)
    {
        $this->setCsv(self::CSV_NAME_YUPACK, $models, $basename);
    }

    private function setCsv($widgetName, $models, $basename) 
    {
        // var_dump($this);exit;
        // 内部的な受け渡しにもかかわらず想定外の指定が来た場合はシステムエラーとする
        if (! in_array($widgetName, [self::CSV_NAME_YAMATO, self::CSV_NAME_YUPACK])) 
            throw new \yii\web\ServerErrorHttpException('システムエラーが発生しました。システム管理者にお問い合わせください。');

        $basename .= '.csv';
        $csv = [];

        $widget = new $widgetName();
        
        if(0 < strlen(Yii::$app->request->get('header'))) 
            $csv[]  = implode(',', $widget->header) . $widget->eol;

        foreach($models as $model)
            $csv[] = $widget::widget([
                'model' => $model,
            ]);

        $csv = implode('', $csv);

        if(0 === strlen($csv))
            $csv = "指定された注文IDのすべてに配送先の指定がありませんでした: \n"
                  . implode(';', \yii\helpers\ArrayHelper::getColumn($models,'purchase_id'));

        $csv = mb_convert_encoding($csv, 'SJIS-WIN', 'UTF-8');

        Yii::$app->response->charset = 'shift_jis';
        Yii::$app->response->sendContentAsFile($csv, $basename, ['inline'=>false, 'mimeType'=>'text/csv']);
        Yii::$app->response->send();
    }


    public function actionUpdateDelivery($id, $save = false)
    {
        $model = $this->findModel($id);
        // POSTの取得
        $post = Yii::$app->request->post();

        if(! Yii::$app->request->isPost)
            return $this->render('update-delivery',['model' => $model->delivery]);

        $delivery = $model->delivery;
        $delivery->load(Yii::$app->request->post());
        if(! $delivery->expect_time)
             $delivery->expect_time = null;

        if(! $delivery->expect_date)
             $delivery->expect_date = null;

        if(('zip2addr' == Yii::$app->request->post('scenario')) &&
           ($param = \common\models\Zip::zip2addr($delivery->zip01, $delivery->zip02)))
        {
            $delivery->pref_id = $param->pref_id;
            $delivery->addr01  = array_shift($param->addr01);
        }
        $delivery->validate();
        $delivery->clearErrors('purchase_id');

        if($delivery->hasErrors())
            return $this->render('update-delivery',['model' => $delivery]);

        if(Yii::$app->request->post('save')) {
           $form = \common\models\PurchaseForm::findOne($id);
           $form->delivery = $delivery;
           $form->save(false);
           Yii::$app->session->addFlash('success', 'お届け先情報が更新されました');
           return $this->redirect(['view', 'id' => $id]);  
        }
        return $this->render('update-delivery',['model' => $delivery]);
//        return $this->redirect(['view', 'id' => $id]);

    }


    /**
     * Finds the Purchase model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Purchase the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(! $model = Purchase::findOne($id))
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');

        if(! Yii::$app->user->can('viewSales',['company_id'=>$model->company_id]))
            throw new \yii\web\ForbiddenHttpException(sprintf(
                "指定モデルの所有権がありません [model.company_id != user.company_id] (%d != %d)",
                $model->company_id,
                Yii::$app->user->identity->company_id)
            );

        if(in_array($this->action->id, ['update','cancel','delete']))
            if(! Yii::$app->user->can('updateSales',['company_id' => $model->company_id]))
                throw new \yii\web\ForbiddenHttpException(
                    "指定モデルを編集する権限がありません"
                );

        return $model;
    }
}
