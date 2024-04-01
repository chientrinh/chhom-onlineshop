<?php

namespace backend\modules\casher\controllers;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use common\models\Branch;
use common\models\Company;
use common\models\Transfer;
use common\models\TransferForm;
use common\models\TransferItem;
use common\models\TransferStatus;
use common\widgets\doc\purchase\TransferDocument;

/**
 * TransferController implements the CRUD actions for Transfer model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/controllers/TransferController.php $
 * $Id: TransferController.php 3124 2016-12-01 05:51:48Z mori $
 */
class TransferController extends BaseController
{
    public function getViewPath()
    {
        if('search' == $this->action->id)
            return dirname(__DIR__) . '/views/default';

        return dirname(__DIR__) . '/views/transfer';
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => '店間移動', 'url'=> ['index'] ];

        return true;
    }

    public function actionSetup()
    {
        return $this->redirect(['default/setup']);
    }

    /**
     * Lists all Transfer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new Transfer();
        $dataProvider = $this->loadProvider($searchModel);
        
        $model = $this->initModel();
        $model->load(Yii::$app->request->post());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
            'model'        => $model,
            'itemModel'    => $model,
        ]);
    }

    /**
     * Displays a single Transfer model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $scenario=null)
    {
        $model = $this->findModel($id);
        $model->scenario = $scenario;

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionPrint($id,$format='pdf')
    {
        $model    = $this->findModel($id);
        $basename = sprintf('%06d.pdf',$id);

        $widget = new TransferDocument([
            'model' => $model,
            'cache' => true,
        ]);
        $html    = $widget->run();
        $pdffile = $widget->pdffile;

        if('pdf' != $format)
            return $html;

        Yii::$app->response->sendFile($pdffile, $basename, ['inline'=>false]);
        Yii::$app->response->send();
    }

    public function actionPrintBatch($format='pdf')
    {
        if((! $selection = Yii::$app->request->get('selection')) ||
           (! $models    = Transfer::findAll(['purchase_id' => $selection]))
        )
            throw new \yii\web\BadRequestHttpException('有効なIDが指定されていません');

        $basename = Yii::$app->request->get('basename', $this->module->getPrintBasename());

        $this->batchPrint($models, $basename, $format);

        foreach($models as $model)
        {
            if(! $model->posted_at)
                 $model->posted_at = new \yii\db\Expression('NOW()');

            if($model->status_id < TransferStatus::PKEY_POSTED)
               $model->status_id = TransferStatus::PKEY_POSTED;

            if($model->getDirtyAttributes() && ! $model->save())
                Yii::error(['could not save Transfer', $model->firstErrors, $model->attributes]);
        }

        return;
    }

    public function actionPrintCsv($id = null)
    {
        if(! $id && ! $selection = Yii::$app->request->get('selection'))
            throw new \yii\web\BadRequestHttpException('注文IDが指定されていません');
        if($id)
            $selection = [ $id, ];
        $basename = Yii::$app->request->get('basename', $this->module->getPrintBasename());

        $query    = Transfer::find()->where(['purchase_id'=>$selection]);

        foreach($query->all() as $model)
            echo $this->renderPartial('csv',['model'=>$model]);

        return;
    }

    public function actionPrintLabel($id = null, $target = 'remedy')
    {
        if(! $id && ! $selection = Yii::$app->request->get('selection'))
            throw new \yii\web\BadRequestHttpException('注文IDが指定されていません');
        if($id)
            $selection = [ $id, ];

        $models   = Transfer::findAll(['purchase_id'=>$selection]);
        $basename = Yii::$app->request->get('basename', $this->module->getPrintBasename());

        if('remedy' == $target)
            return $this->printRemedyLabel($models, $basename);

        elseif('price' == $target)
            return $this->printProductPrices($models, $basename);

        //elseif('sticker' == $target)
        return $this->printProductStickers($models, $basename);
    }

    public function actionCreate($print=null)
    {
        $model     = $this->initModel();
        $cookie_id = 'casher/transfer/create';

        if(Yii::$app->request->isPost)
        {
            $model->load(Yii::$app->request->post());

            $cookies = Yii::$app->response->cookies;
            $cookies->add(new \yii\web\Cookie([
                'name'  => $cookie_id,
                'value' => Json::encode($model->attributes),
                'path'  => Url::current(),
                'expire'=> time() + 24 * 3600, // 24 hour
            ]));
        }
        else
        {
            $cookies = Yii::$app->request->cookies;
            $json    = $cookies->getValue($cookie_id, null);

            if($json)
                $model->load(Json::decode($json), '');
        }
        $model->validate();

        if(null === $print)
            return $this->render('create',['model'=>$model]);

        $basename = Yii::$app->request->get('basename', $this->module->getPrintBasename());
        return $this->printProductPrices([$model], $basename);
    }

    public function actionDuplicate($id)
    {
        $orig      = $this->findModel($id);
        $cookie_id = 'casher/transfer/create';

        $cookies = Yii::$app->response->cookies;
        $cookies->add(new \yii\web\Cookie([
            'name'  => $cookie_id,
            'value' => Json::encode($orig->attributes),
            'path'  => Url::current(),
            'expire'=> time() + 24 * 3600, // 24 hour
        ]));

        foreach($orig->items as $k => $item)
            $item->item_id = null;

        $this->module->purchase->items = $orig->items;

        return $this->redirect(['create']);
    }

    public function actionFinish()
    {
        $model = $this->initModel();

        if($model->load(Yii::$app->request->post()))
        {
            $model->status_id = TransferStatus::PKEY_ASKED;
            if($model->validate() && $model->save())
            {
                $this->module->clearBuffer(); // forget everything in cart
                Yii::$app->session->addFlash('success',sprintf("移動ID(%06d) で発注しました",$model->purchase_id));

                $mailer = new \common\components\sendmail\TransferMail(['model'=>$model]);
                $mailer->thankyou();
            }
            else
                return $this->redirect(['create']);
        }

        return $this->redirect(['view', 'id' => $model->primaryKey ]);
    }

    /**
     * Updates an existing Transfer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $target, $value)
    {
        $model = $this->findModel($id);

        if('status_id' == $target)
            $model->updateStatus($model);

        $this->updateModel($model, $target, $value);
        
        return $this->redirect(['view', 'id'=>$id]);
    }

    public function actionUpdateItem($id, $target, $value)
    {
        if(! $model = TransferItem::findOne($id))
            throw new \yii\web\NotFoundHttpException();

        $this->updateModel($model, $target, $value);
        $model->transfer->save(false, ['update_date','updated_by']);
        
        return $this->redirect(['view', 'id'=>$model->purchase_id]);
    }

    private function batchPrint($models, $basename, $format)
    {
        if('html' == $format)
        {
            foreach($models as $model)
                TransferDocument::widget([
                'model' => $model,
                'cache' => true,
            ]);
            return;
        }

        if(! $pdffile = TransferDocument::getMergedPdf($models))
            throw new \yii\web\ServerErrorHttpException('application failed to generate united pdf');
        if(! is_file($pdffile))
            echo "$pdffile was not generated";

        $basename .= '.pdf';

        Yii::$app->response->sendFile($pdffile, $basename, ['inline'=>false]);
        Yii::$app->response->send();
    }

    private function updateModel($model, $target, $value)
    {
        if($model->hasAttribute($target))
        {
            $model->$target = $value;

            if(! $model->validate($target))
                throw new \yii\base\UserException(implode(';',$model->firstErrors));

            $model->save(false);
        }

        if($model->getDirtyAttributes())
            $this->failure("ごめんなさい、更新できませんでした。いまシステムの人が調査中です。しばらくおまちください。", $model);
        else
            Yii::$app->session->addFlash('success',sprintf("%s の %s を %s に更新しました",
                                                           ($model instanceof Transfer ? "伝票" : $model->name),
                                                           $model->getAttributeLabel($target),
                                                           $value));
    }

    private function failure($text, \yii\base\Model $model)
    {
        $msg = [
            'message'    => $text,
            'errors'     => $model->errors,
            'attributes' => $model->attributes,
            'location'   => __FILE__.__LINE__,
        ];

        Yii::error($msg);

        Yii::$app->session->addFlash('error', $message);
    }

    /**
     * @return mixed
     */
    public function actionCancel($id)
    {
        $model = $this->findModel($id);

        if($model->cancelate())
            Yii::$app->session->addFlash('success',sprintf('移動ID(%06d)はキャンセルされました',$model->purchase_id));
        else
            Yii::$app->session->addFlash('error',sprintf('移動ID(%06d)はキャンセルできませんでした',$model->purchase_id));
        $this->redirect(['view','id'=>$id]);
    }

    /**
     * @return 404 Not Found
     */
    public function actionDelete($id)
    {
        return new \yii\web\NotFoundException();
    }

    /**
     * Finds the Transfer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transfer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected static function findModel($id)
    {
        if($model = Transfer::findOne($id))
            return $model;

        throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
    }

    private function initModel()
    {
        $model = new TransferForm();

        if($branch = $this->module->branch)
            $model->dst_id = $branch->branch_id;

        if($this->module->purchase->getItemsOfCompany(Company::PKEY_TY))
            $model->src_id = Branch::PKEY_ROPPONMATSU;
        else
            $model->src_id = Branch::PKEY_ATAMI;

        if($items = $this->module->purchase->items)
            $model->items = $items;

        return $model;
    }

    private function loadProvider($model)
    {
        $model->load(Yii::$app->request->get());

        $query = Transfer::find()
               ->andFilterWhere([
                   'src_id'    => $model->src_id,
                   'dst_id'    => $model->dst_id,
                   'status_id' => $model->status_id,
               ])->andFilterWhere(['like','asked_at',$model->asked_at])
               ->andFilterWhere(['like','posted_at',$model->posted_at])
               ->andFilterWhere(['like','got_at',$model->got_at]);

        if('all' === Yii::$app->request->get('branch'))
            ;
        elseif($b = $this->module->branch)
            $query->andWhere(['or',
                              ['src_id' => $b->branch_id],
                              ['dst_id' => $b->branch_id]]);
        
        return new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['purchase_id'=>SORT_DESC]],
        ]);
    }

}
