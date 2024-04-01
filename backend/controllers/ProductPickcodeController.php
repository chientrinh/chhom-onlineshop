<?php

namespace backend\controllers;

use Yii;
use common\models\ProductPickcode;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

/**
 * ProductPickcodeController implements the CRUD actions for ProductPickcode model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/ProductPickcodeController.php $
 * $Id: ProductPickcodeController.php 3442 2017-06-21 11:57:57Z mori $
 */
class ProductPickcodeController extends BaseController
{

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'商品','url'=>['/product/index']];
        $this->view->params['breadcrumbs'][] = ['label'=>'ピックコード','url'=>['index']];

        return true;
    }

    /**
     * Lists all ProductPickcode models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->request->get('csv', null))
            return $this->actionCsv();

        $searchModel  = new \backend\models\SearchProductPickcode();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCsv()
    {
        $searchModel  = new \backend\models\SearchProductPickcode();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->pagination = false;
        Yii::$app->formatter->nullDisplay = "<span>(なし)</span>";

        return $this->render('index_csv', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProductPickcode model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ProductPickcode model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProductPickcode();

        if($model->load(Yii::$app->request->get()) && $model->save())
            return $this->redirect(['view', 'id' => $model->pickcode]);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Delete a model
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if($model->delete())
            Yii::$app->session->addFlash('success', "$id を削除しました");
        else
            Yii::$app->session->addFlash('error', "$id を削除できませんでした");

        return $this->redirect(['index']);
    }

    /**
     * Updates an existing ProductPickcode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->get()) && $model->save())
            return $this->redirect(['view', 'id' => $model->pickcode]);

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Updates ProductPickcode(s) at once
     * If update is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionBatchUpdate()
    {
        $model = new \common\models\FileForm();

        if( Yii::$app->request->isPost &&
            ($content = $this->uploadFile($model)   ) &&
            ($matrix  = $this->parseFile($content)  ) &&
            ($html    = $this->updateModels($matrix))
        ){
            return $this->renderContent($html);
        }

        return $this->render('upload', ['model'=>$model]);
    }

    /**
     * updateModels(): private subroutine for action batch-update
     * @param string $matrix[] (data rows)
     * @return string $html (rendered html)
     */
    private function updateModels($matrix)
    {
        $html  = [];

        $transaction = Yii::$app->db->beginTransaction();
        $rollback    = false;

        foreach($matrix as $row)
        {
            ProductPickcode::deleteAll('ean13 = :e OR product_code = :c OR pickcode = :p',[
                ':e'=>$row['ean13'],
                ':c'=>$row['product_code'],
                ':p'=>$row['pickcode']
            ]);

            $model = new ProductPickcode([
                'ean13'        => $row['ean13'],
                'product_code' => $row['product_code'],
                'pickcode'     => $row['pickcode']
            ]);

            if(! $model->save()){ $rollback = true; }

            $html[] = $this->renderPartial('_row', ['model'=>$model,'row'=>$row]);
        }
        if($rollback)
        {
            $transaction->rollback();
            Yii::$app->session->addFlash('error', "エラーにより更新を中止しました");
        }
        else
        {
            $transaction->commit();
            Yii::$app->session->addFlash('success', "対象レコードを以下の通り更新しました");
        }

        return implode('', $html);
    }

    /**
     * parseFile(): private subroutine for action batch-update
     * @param string $content (csv formatted text)
     * @return string $matrix[] (data rows)
     */
    private function parseFile($content)
    {
        $matrix = [];
        $buff   = explode("\n", $content);
        $header = explode(",", trim(array_shift($buff)));

        $validator = new \yii\validators\RangeValidator([
            'range' => ['ean13','product_code','pickcode','model.name']
        ]);
        foreach($header as $value)
        if(! $validator->validate($value, $message))
        {
            Yii::$app->session->addFlash('error', $message);
            return false;
        }

        foreach($buff as $line)
        {
            if(! $line){ continue; }

            $row   = [];
            $chunk = explode(',', trim($line));

            if(count($chunk) < count($header))
                Yii::$app->session->addFlash('error', "不正な行を検出しました: $line");

            foreach($header as $idx => $label)
                $row[$label] = ArrayHelper::getValue($chunk, $idx);

            $matrix[] = $row;
        }
        foreach(['pickcode','ean13','product_code'] as $label)
        {
            $col = ArrayHelper::getColumn($matrix, $label);
            if(count(array_unique($col)) < count($col))
            {
                Yii::$app->session->addFlash('error', "$label に重複があります");
                return false;
            }
        }

        return $matrix;
    }

    /**
     * uploadFile(): private subroutine for action batch-update
     * @param FileForm $model
     * @return false | string
     */
    private function uploadFile($model)
    {
        $file = $model->tgtFile = \yii\web\UploadedFile::getInstance($model, 'tgtFile');

        $validator = new \yii\validators\FileValidator([
            'mimeTypes'  => ['text/csv', 'text/plain'],
            'minSize'    => 1,
            'maxSize'    => 10 * 1000 * 1000, // 10 MB
        ]);

        if(! $validator->validate($file, $error))
        {
            $model->addError('tgtFile', $error);
            Yii::$app->session->addFlash('error', implode(';',$model->firstErrors));
            return false;
        }
        Yii::$app->session->addFlash('success', "ファイルを取得しました: {$file->name}");

        return file_get_contents($file->tempName);
    }

    /**
     * Finds the ProductPickcode model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ProductPickcode the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = ProductPickcode::findOne($id);

        if(! $model)
            throw new NotFoundHttpException('ページが見つかりません');

        return $model;
    }

}
