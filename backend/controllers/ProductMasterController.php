<?php

namespace backend\controllers;

/**
 * ProductMasterController implements the CRUD actions for ProductMaster model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/ProductMasterController.php $
 * $Id: ProductMasterController.php 4237 2020-03-12 04:30:38Z kawai $
 */

use Yii;
use \yii\helpers\ArrayHelper;
use \common\models\ProductMaster;
use \yii\helpers\Url;

class ProductMasterController extends BaseController
{
    public $title = '商品＆レメディー 表示名＆表示順 管理';

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => '商品＆レメディー 表示名＆表示順 管理','url'=>['index']];

        $cid = Yii::$app->request->get('company_id');
        if(! Yii::$app->user->can('viewProduct',['company_id'=>$cid]))
        {
            if(($user = Yii::$app->user->identity) && ($cid != $user->company_id))
                // このままではもともと記載されていたパラメータをつぶしてしまうことになるので、活かしつつcompany_idを付加するよう制御
                $request = Yii::$app->request->url;
                $request = str_replace(Yii::$app->homeUrl, "", $request);
                if(count(Yii::$app->request->get()) > 0) {
                    $company = "&company_id=".$user->company_id;
                } else {
                    $company = "?company_id=".$user->company_id;
                }
                return Yii::$app->response->redirect(Url::to(Url::home().$request.$company));
//                return $this->redirect([$action->id, 'company_id' => $user->company_id]);

            throw new \yii\web\ForbiddenHttpException("アクセス権限がありません");
        }

        return true;
    }

    public function actionBatchUpdate()
    {
        $model  = new \common\models\FileForm();
        $parser = new \common\components\CsvReader();

        if(Yii::$app->request->isPost)
        {
            $msg     = null;
            $content = $this->uploadFile($model);

            if(! $parser->feed($content))
                $msg = 'CSVの読み込みに失敗しました';

            elseif(! $rows = $parser->getRows())
                $msg = 'CSVレコードがありません';

            else
                $html = $this->updateModels($rows);

            if($msg)
                Yii::$app->session->addFlash('error', $msg);

            if(isset($html) && is_string($html))
                return $this->renderContent($html);
        }

        return $this->render('upload', ['model'=>$model]);
    }

    public function actionIndex($pagesize=null, $company_id = null, $format='html')
    {
        $model = new ProductMaster();
        $model->load(Yii::$app->request->get());

        if(! Yii::$app->user->can('viewProduct')) // user is tenant
            $company_id = Yii::$app->user->identity->company_id;

        if($company_id)
            $model->company_id = $company_id;

        $provider = $this->loadProvider($model);
        if($pagesize)
            $provider->pagination->pagesize = (int)$pagesize;

        if('csv' == $format)
            return $this->actionCsv($provider);

        return $this->render('index',[
            'dataProvider'=> $provider,
            'searchModel' => $model,
        ]);
    }

    /**
     * ProductMaster::{name,dsp_priority}を１件更新する
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post()) && $model->save(true, ['name', 'kana', 'keywords', 'dsp_priority', 'restrict_id', 'update_date']))
        {
            Yii::$app->session->addFlash('success', "更新しました");
        }

        return $this->render('update',['model'=>$model]);
    }

    private function actionCsv(\yii\data\ActiveDataProvider $provider)
    {
        ini_set("memory_limit","1G"); // total 32GB memory @ arnica.toyouke.com

        $provider->query->limit = false;

        $this->layout = 'none';

        $html = $this->render('csv',[
            'dataProvider'=>$provider,
        ]);

        return $this->renderContent($html);
    }

    private static function loadProvider($model)
    {
        $query = $model->find()
                       ->with(['category','product','stock','stock.jancode','remedy','potency','vial','restriction'])
                       ->JoinWith(['product','stock','restriction','company']);

        foreach($model->attributes as $attr => $value)
        {
            if(! isset($value) || ('' === $value))
                continue;

            if(in_array($attr, ['kana']))
                $query->andWhere(['like', $model->tableName().'.'.$attr, $value]);
            else
                $query->andWhere([$model->tableName().'.'.$attr => $value]);
        }

        return new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function findModel($id)
    {
        $model = ProductMaster::findOne($id);
        if(! $model)
            throw new \yii\web\NotFoundHttpException("指定モデルは見つかりません");

        if(! Yii::$app->user->can('viewProduct',['company_id'=>$model->company_id]))
            throw new \yii\web\ForbiddenHttpException(sprintf(
                "指定モデルの所有権がありません [model.company_id != user.company_id] (%d != %d)",
                $model->company_id,
                Yii::$app->user->identity->company_id)
            );

        if(in_array($this->action->id, ['update','cancel','delete']))
            if(! Yii::$app->user->can('updateProduct',['company_id' => $model->company_id]))
                throw new \yii\web\ForbiddenHttpException(
                    "指定モデルを編集する権限がありません"
                );

        return $model;
    }

    /**
     * updateModels(): private subroutine for action batch-update
     * @param string $rows[]
     * @return string $html (rendered html)
     */
    private function updateModels($rows)
    {
        $html  = [];

        $transaction = Yii::$app->db->beginTransaction();
        $rollback    = false;

        foreach($rows as $row)
        {
            $ean13 = ArrayHelper::getValue($row, 'ean13');
            $model = ProductMaster::findOne(['ean13' => $ean13]);

            if(! $model)
            {
                $model = new ProductMaster();
                $model->addError('ean13', "対象レコードが見つかりません:$ean13");
                $rollback = true;
            }
            else
            foreach(['name','dsp_priority'] as $attr)
            {
                if( ($value = ArrayHelper::getValue($row, $attr)) &&
                    ($value != $model->getAttribute($attr))
                )
                    $model->$attr = $value;
            }
            if($model->dirtyAttributes && ! $model->save()){ $rollback = true; }

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
