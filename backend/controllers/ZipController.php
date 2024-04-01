<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/ZipController.php $
 * $Id: ZipController.php 2667 2016-07-07 08:26:14Z mori $
 */

namespace backend\controllers;

use Yii;
use common\models\Zip;
use common\models\ZipSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * ZipController implements the CRUD actions for Zip model.
 */
class ZipController extends BaseController
{
    /**
     * Lists all Zip models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ZipSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCsv()
    {
        $model    = new ZipSearch();
        $provider = $model->search(Yii::$app->request->queryParams);
        $query    = $provider->query;

        $row = new Zip();
        echo implode(',', array_keys($row->attributes)), "<br>";

        foreach($query->each() as $row)
        {
            echo implode(',', $row->attributes), "<br>";
        }
        return;
    }

    /**
     * Displays a single Zip model.
     * @param integer $region
     * @param string $zipcode
     * @param integer $pref_id
     * @param string $city
     * @param string $town
     * @return mixed
     */
    public function actionView($region, $zipcode, $pref_id, $city, $town)
    {
        return $this->render('view', [
            'model' => $this->findModel($region, $zipcode, $pref_id, $city, $town),
        ]);
    }

    /**
     * Creates a new Zip model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Zip();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'region' => $model->region, 'zipcode' => $model->zipcode, 'pref_id' => $model->pref_id, 'city' => $model->city, 'town' => $model->town]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Zip model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $region
     * @param string $zipcode
     * @param integer $pref_id
     * @param string $city
     * @param string $town
     * @return mixed
     */
    public function actionUpdate($region, $zipcode, $pref_id, $city, $town)
    {
        $model = $this->findModel($region, $zipcode, $pref_id, $city, $town);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'region' => $model->region, 'zipcode' => $model->zipcode, 'pref_id' => $model->pref_id, 'city' => $model->city, 'town' => $model->town]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionBatchUpdate()
    {
        $this->view->params['breadcrumbs'][] = ['label'=>'Zip','url'=>['index']];
        $model = new \common\models\FileForm();

        if( Yii::$app->request->isPost &&
            ($content = $this->uploadFile($model)       ) &&
            ($matrix  = $this->parseFile($content)      ) &&
            ($html    = $this->updateModels($matrix))
        ){
            return $this->renderContent($html);
        }

        return $this->render('upload', ['model'=>$model]);
    }

    /**
     * Deletes an existing Zip model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $region
     * @param string $zipcode
     * @param integer $pref_id
     * @param string $city
     * @param string $town
     * @return mixed
    public function actionDelete($region, $zipcode, $pref_id, $city, $town)
    {
        $this->findModel($region, $zipcode, $pref_id, $city, $town)->delete();

        return $this->redirect(['index']);
    }
     */

    /**
     * Finds the Zip model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $region
     * @param string $zipcode
     * @param integer $pref_id
     * @param string $city
     * @param string $town
     * @return Zip the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($region, $zipcode, $pref_id, $city, $town)
    {
        if (($model = Zip::findOne(['region' => $region, 'zipcode' => $zipcode, 'pref_id' => $pref_id, 'city' => $city, 'town' => $town])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * parseFile(): private subroutine for action batch-update
     * @param string $content (csv formatted text)
     * @return string $matrix[] (data rows)
     */
    private function parseFile($content)
    {
        $matrix = [];
        $column = [];
        $buff   = explode("\n", $content);
        $header = array_shift($buff);

        $header = explode(",", $header);
        foreach($header as $idx => $value)
        {
            $value = trim($value);
            $column[$value] = $idx;
        }

        if(false === mb_detect_encoding($content, Yii::$app->charset))
        {
            Yii::$app->session->addFlash('error', sprintf("文字コードは %s を使用してください", Yii::$app->charset));
            return false;
        }
        $validator = new \yii\validators\NumberValidator(['min'=>4]);
        if(! $validator->validate(count($column), $message))
        {
            Yii::$app->session->addFlash('error', $message);
            var_dump(__LINE__);
            return false;
        }
        $validator = new \yii\validators\RangeValidator(['range'=> array_keys((new Zip)->getAttributes())]);
        foreach($column as $key => $v)
        if(! $validator->validate($key, $message))
        {
            Yii::$app->session->addFlash('error', $message);
            return false;
        }

        foreach($buff as $line)
        {
            if(! $line || ('#' === $line[0])){ continue; }

            $row   = [];
            $chunk = explode(',', trim($line));

            if(count($chunk) < count($header))
                Yii::$app->session->addFlash('error', "不正な行を検出しました: $line");

            foreach($column as $label => $idx)
                $row[$label] = trim(ArrayHelper::getValue($chunk, $idx));

            $matrix[] = $row;
        }

        return $matrix;
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
            if($model = Zip::find()->where(['zipcode' => $row['zipcode'],
                                            'pref_id' => $row['pref_id'],
                                            'city'    => $row['city'],
                                            'town'    => $row['town']])
                                     ->one())
            {
                $model->load($row, '');
            }
            else
            {
                $model = new Zip($row);
                $model->addError('zipcode',"レコードが見つかりませんでした");
            }

            if($model->hasErrors() || ! $model->save())
            {
                $rollback = true;
            }

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
}
