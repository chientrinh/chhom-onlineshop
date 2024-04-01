<?php

namespace frontend\modules\profile\controllers;
use Yii;

use \common\models\sodan\BinaryStorage;

/**
 * CRUD for dtb_customer_addrbook
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/controllers/SodanController.php $
 * $Id: SodanController.php 3970 2018-07-13 08:46:33Z mori $
 */

class SodanController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->layout = '/bootstrap';

        return true;
    }

    /**
     * display upload form
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'model' => $this->findModel()
        ]);
    }

    /**
     * update customer's document
     */
    public function actionUpdate($id)
    {
        if(! in_array($id, ['agreement','questionnaire']))
            throw new \yii\web\NotFoundHttpException();

        $model = $this->findModel();

        if($this->uploadFile($model, $id))
            Yii::$app->session->addFlash('success','アップロードが完了しました');

        return $this->redirect(['index']);
    }

    public function actionView($id)
    {
        $fullpath = $this->viewPath . '/' . $id;

        if(! is_file($fullpath) || ! is_readable($fullpath))
            throw new \yii\web\NotFoundHttpException();

        $response = \Yii::$app->getResponse();
        $response->sendFile($fullpath, $id, ['inline'=>false]);
        return $response->send();
    }

    private function findModel()
    {
        $client = \common\models\sodan\Client::findOne(Yii::$app->user->id);
        if(! $client)
            $client = new \common\models\sodan\Client(['client_id' => Yii::$app->user->id]);
        //  throw new \yii\web\ForbiddenHttpException('本ページは本部の相談会へお問い合わせがあったお客様のみ閲覧できます');

        return $client;
    }

    /* @return bool */
    private function uploadFile($client, $property)
    {
        $form = new \common\models\FileForm();
        $form->tgtFile = \yii\web\UploadedFile::getInstance($form, 'tgtFile');
        if(! $form->validate())
        {
            Yii::$app->session->addFlash('error', implode(';',$form->firstErrors));
            return false;
        }

        $file  = $form->tgtFile;
        $model = new \common\models\BinaryStorage([
            'tbl_name' => $client->tableName(),
            'pkey'     => $client->client_id,
            'property' => $property,
            'basename' => $file->name,
            'type'     => $file->type,
            'size'     => $file->size,
            'data'     => file_get_contents($file->tempName),
        ]);
        $model->detach('staff_id');

        return $model->save();
    }

    public function actionCreate()
    {
        throw new \yii\web\NotFoundHttpException();
    }

    public function actionDelete()
    {
        throw new \yii\web\NotFoundHttpException();
    }

}
