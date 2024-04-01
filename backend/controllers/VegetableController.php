<?php

namespace backend\controllers;

use Yii;
use common\models\Vegetable;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VegetableController implements the CRUD actions for Vegetable model.
 */
class VegetableController extends BaseController
{
    /**
     * @var PDF_MERGER absolute path of `pdfunite`
     * Caution: this class is completely dependent on this executable, no warranty without it
     */
    const PDF_MERGER  = '/usr/bin/pdfunite';

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => '野菜', 'url' => ['index']];

        return true;
    }

    /**
     * Lists all Vegetable models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new \backend\models\SearchVegetable();

        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Vegetable model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionPrint($id, $price, $qty=1)
    {
        $price = min(99999, (int)$price); // 上限 5桁
        $qty   = min(  100, (int)$qty);   // 上限 100枚
        $qty   = max($qty, 1);            // 下限   1枚

        $veg = $this->findModel($id);
        $veg->price = $price;

        $product = Vegetable::findByBarcode($veg->ean13);

        $widget = \common\widgets\doc\product\ProductLabel::begin(['model'=>$product]);
        $widget->layout = '{name}{price}{barcode}';

        $filename = $widget->renderPdf();
        $output   = Yii::getAlias(sprintf('@runtime/%sx%02d.pdf', $veg->ean13, $qty));
        $inline   = true;
        $mime     = 'application/pdf';

        if($qty <= 1) {
            Yii::$app->response->setDownloadHeaders(basename($output), $mime, $inline);
            return Yii::$app->response->sendFile($filename, $inline);
        }
        $files = [];
        for($i = 0; $i < $qty; ++$i) { $files[] = $filename; }

        $command = sprintf('%s %s %s', self::PDF_MERGER, implode(' ', $files), $output);
        system($command);
        if(! is_file($output))
            throw new \yii\base\Exception('failed to generate pdf by command: '.$command);

        Yii::$app->response->setDownloadHeaders(basename($output), $mime, $inline);
        return Yii::$app->response->sendFile($output, $inline);
    }

    /**
     * Creates a new Vegetable model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Vegetable(['is_other' => 0]);
        $model->validate(['veg_id']);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->veg_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Vegetable model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        
        $model = $this->findModel($id);
        if($model->load(Yii::$app->request->post()) && $model->validate() && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->veg_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Vegetable model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if($model->delete())
        {
            Yii::$app->session->addFlash('success', "{$model->name}({$id})を削除しました");
            return $this->redirect(['index']);
        }

        Yii::$app->session->addFlash('error', "{$model->name}を削除できません、システム担当者へ連絡してください");
        return $this->redirect(['view','id']);
    }

    /**
     * Finds the Vegetable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Vegetable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Vegetable::findOne($id);
        if(! $model)
            throw new NotFoundHttpException("当該IDは見つかりません({$id})");

        return $model;
    }
}
