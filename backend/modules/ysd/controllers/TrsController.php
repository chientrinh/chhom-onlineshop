<?php

namespace backend\modules\ysd\controllers;

use Yii;
use common\models\ysd\TransferResponse;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use backend\models\CsvUploadMultiForm;
use yii\web\UploadedFile;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/views/rrs/view.php $
 * @version $Id: view.php 1961 2016-01-11 01:39:26Z mori $
 *
 * TrsController implements the CRUD actions for TransferResponse model.
 */
class TrsController extends \backend\controllers\BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'振替結果','url'=>['index']];

        return true;
    }

    /**
     * Lists all TransferResponse models.
     * @return mixed
     */
    public function actionIndex()
    {
        $csv_model = new CsvUploadMultiForm();

        if (Yii::$app->request->isPost) {
            $csv_model->csvFiles = UploadedFile::getInstances($csv_model, 'csvFiles');
            if ($csv_model->upload()) {
                // ファイルのアップロードが成功
                Yii::$app->session->addFlash('success',sprintf("%s件のファイルアップロードが完了しました",$csv_model->success_count));
                $result = "";
                $success_result = "";
                $error_result = "";
                $controller = new \console\controllers\YsdController(Yii::$app->controller->id, Yii::$app);

                foreach ($csv_model->csvFiles as $file) {
                    $result = $controller->actionBackendTrsImport(Yii::getAlias(sprintf('@runtime/%s.%s',$file->baseName, $file->extension)));
                    if(strpos($result,'error') !== false){
                        $error_result .= $result."<br />";
                    } else {
                        $success_result .= $result."<br />";
                    }
                }

                if(strlen($success_result) > 0)
                    Yii::$app->session->addFlash('success', sprintf("登録成功：<br />%s",$success_result));
                if(strlen($error_result) > 0)
                    Yii::$app->session->addFlash('error', sprintf("登録エラー：<br />%s",$error_result));

            } else {
                Yii::$app->session->addFlash('error',sprintf("%s件、不正なファイルが検出されました",$csv_model->error_count));                
            }
        }
        
        $model  = new TransferResponse();
        $model->load(Yii::$app->request->get());

        $provider = $this->loadProvider($model);

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
            'csvModel'    => $csv_model,
        ]);
    }

    /**
     * Displays a single TransferResponse model.
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
     * Finds the TransferResponse model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TransferResponse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TransferResponse::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function loadProvider(TransferResponse $model)
    {
        $query = TransferResponse::find()
            ->andFilterWhere($model->attributes);

        return new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'enableMultiSort' => true,
                'defaultOrder' => ['trs_id'=>SORT_DESC],
            ],
        ]);
    }
}
