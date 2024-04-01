<?php
namespace backend\modules\casher\controllers;

use Yii;
use \yii\helpers\ArrayHelper;
use \yii\helpers\Url;
use common\models\Branch;
use common\models\Company;
use common\models\Inventory;
use common\models\InventoryItem;
use common\models\InventoryStatus;
use backend\models\Staff;

/**
 * TransferController implements the CRUD actions for Transfer model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/controllers/InventoryController.php $
 * $Id: InventoryController.php 2504 2016-05-13 01:52:43Z mori $
 */
class InventoryController extends BaseController
{
    public $crumbs = [
    ];

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => '棚卸', 'url'=> ['index'] ];

        return true;
    }

    /**
     * Lists all Transfer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model    = new Inventory();
        $model->load(Yii::$app->request->get());
        $provider = $this->loadProvider($model);
        
        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
        ]);
    }

    /**
     * Displays a single Inventory model in non-decorative html.
     * @param integer $id
     * @return mixed
     */
    public function actionPrint($id)
    {
        $model = $this->findModel($id);

        $this->layout = '/bootstrap';

        return $this->render('print', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Inventory model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $format='html')
    {
        if('csv' == $format)
            return $this->renderCsv($id);

        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    private function renderCsv($id)
    {
        $model = $this->findModel($id);
        $query = $model->getItems()
                       ->join('LEFT JOIN',
                              \common\models\ProductMaster::tableName().' m',
                              'dtb_inventory_item.ean13=m.ean13')
                       ->join('LEFT JOIN',
                              \common\models\ProductSubcategory::tableName().' ps',
                              'dtb_inventory_item.ean13=ps.ean13')
                       ->join('LEFT JOIN',
                              \common\models\Subcategory::tableName().' s',
                              'ps.subcategory_id=s.subcategory_id and s.subcategory_id not in (11,31)')
                       ->join('LEFT JOIN',
                              \common\models\Remedy::tableName().' r',
                              'm.remedy_id=r.remedy_id')
                       ->with('product.category',
                              'product.category.seller',
                              'product.category.vendor',
                              'product.subcategories')
                       ->orderBy([
                           new \yii\db\Expression('FIELD(s.subcategory_id, 123,32,30,29,28,25,26,27,24,7) DESC'),
                           'm.category_id'  => SORT_ASC,
                           's.parent_id'    => SORT_ASC,
                           's.weight'       => SORT_DESC,
                           'm.potency_id'   => SORT_ASC,
                           'm.vial_id'      => SORT_ASC,
                           'r.abbr'         => SORT_ASC,
                           'm.kana'         => SORT_ASC,
                       ])
                       ->distinct(true);

        $basename = sprintf('%s-%s.csv', $id, date('Ymd_His'));
        $charset  = 'SJIS-WIN';
        $eol      = "\r\n";
        $inline   = false;
        $mime     = 'text/csv';
        Yii::$app->response->setDownloadHeaders($basename, $mime, $inline);
        Yii::$app->response->send();

        Yii::setLogger(new \yii\log\Logger());
        // @see http://stackoverflow.com/questions/27420959/yii2-batchinsert-eats-all-server-memory

        $header = [
            implode(',', [$model->getAttributeLabel('istatus_id' ), ($s = $model->status)  ? $s->name :"不明"]),
            implode(',', [$model->getAttributeLabel('branch_id'  ), $model->branch->name]),
            implode(',', [$model->getAttributeLabel('create_date'), $model->create_date ]),
            implode(',', [$model->getAttributeLabel('update_date'), $model->update_date ]),
            implode(',', [$model->getAttributeLabel('created_by' ), ($c = $model->creator) ? $c->name : null]),
            implode(',', [$model->getAttributeLabel('updated_by' ), ($u = $model->updator) ? $u->name : null]),
            "品目,". $model->getItems()->count(),
            "総数,". $model->getItems()->sum('actual_qty'),
            "",
            "",
        ];

        $widget = new \common\widgets\CsvView([
            'query'      => $query,
            'header' => [
                'iitem_id',
                'ean13',
                'category_id','subcategory_id','remedy.abbr','potency','vial','品名','前回','入庫','出庫','売上','帳簿在庫','棚卸数(actual_qty)','+/-'],
            'attributes' => [
                'iitem_id',
                'ean13',
                function($data){if($p = $data->product) return $p->category->name; },
                function($data){
                    if(! $p = $data->product) return null;
                    if($model = $p->getSubcategories()->andWhere(['not in','subcategory_id',[11,31]])->orderBy([
                    new \yii\db\Expression('FIELD(subcategory_id, 123,32,30,29,28,25,26,27,24,7) DESC'),
                    'parent_id'=>SORT_ASC,
                    'weight'   =>SORT_DESC,
                    'subcategory_id' => SORT_ASC,
                ])->one())
                    return $model->name;
                },
                function($data)
                {
                    if(($p = $data->product) && ($s = $p->stock) && ($r = $s->remedy))
                        return $r->abbr .' '. $r->ja;
                },
                function($data)
                {
                    if(($p = $data->product) && ($s = $p->stock) && ($o = $s->potency))
                        return $o->name;
                },
                function($data)
                {
                    if(($p = $data->product) && ($s = $p->stock) && ($v = $s->vial))
                        return $v->name;
                },
                'kana','prev_qty','in_qty','out_qty','sold_qty','ideal_qty','actual_qty','diff_qty'],
        ]);

        // draw preceeding text
        echo mb_convert_encoding(implode($widget->eol, $header), $widget->charset, Yii::$app->charset);

        // draw CSV contents
        $widget->run();

        echo '# end of file';
        return;
    }

    /**
     * Create an Inventory model for particular Branch
     */
    public function actionCreate()
    {
        if(! $branch = $this->module->branch)
            return $this->redirect(['default/setup']);

        $model = new Inventory(['branch_id' => $branch->branch_id]);

        if($model->save())
            return $this->redirect(['update','id'=>$model->inventory_id]);

        throw new \yii\web\ForbiddenHttpException(implode(';',$model->firstErrors));
    }

    /**
     * Add an InventoryItem to existing Inventory model
     * @param integer $id
     * @param string  $ean13
     * @return mixed
     */
    public function actionCreateItem($id, $ean13)
    {
        $model   = $this->findModel($id);
        $finder  = new \common\components\ean13\ModelFinder(['barcode'=>$ean13]);

        if(! $product = $finder->getOne())
            throw new \yii\base\UserException('failed to find item: '.implode(';',$finder->attributes).implode(';',$finder->firstErrors));

        $pid  = $product->hasAttribute('product_id') ? $product->product_id : null;
        $kana = $product->hasAttribute('kana') ? $product->kana : $product->name;
        $item = new InventoryItem(['inventory_id' => $id,
                                   'product_id'   => $pid,
                                   'ean13'        => $ean13,
                                   'kana'         => $kana,
                                   'actual_qty'   => 0,
                                   'updated_by'   => Staff::PKEY_NOBODY,
        ]);
        $item->detachBehavior('staff');

        if($item->save())
            $model->save(false,['update_date','updated_by']) &&
            Yii::$app->session->addFlash('success',"{$product->name} を追加しました");
        else
            Yii::$app->session->addFlash('error', "{$product->name}: ". implode(';',$item->firstErrors));

        if(! $url = Url::previous($this->module->id))
             $url = ['update','id' => $id];

        return $this->redirect($url);
    }

    /**
     * Updates an existing Inventory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $status=null)
    {
        $model   = $this->findModel($id);
        $request = Yii::$app->request;

        if($model->isApproved())
            throw new \yii\web\ForbiddenHttpException("承認済みの棚卸を編集することはできません");

        if($request->isGet && isset($status))
        {
            $model->istatus_id = (int) $status;

            if($model->save() &&
               $model->isApproved()
            )
                return $this->redirect(['view','id'=>$id]);
        }
        elseif($request->isPost)
        {
            if($request->post('iitem_id'))
                return $this->actionUpdateItem($model);

            elseif($model->load($request->post()))
                $model->save(false,['create_date']); // only create_date could be posted
        }

        Url::remember(Url::current(), $this->module->id);

        return $this->render('update', ['model'=>$model]);
    }

    public function actionBatchUpdate($id)
    {
        $this->view->params['breadcrumbs'][] = ['label'=>$id,'url'=>['update','id'=>$id]];
        $model = new \common\models\FileForm();

        if( Yii::$app->request->isPost &&
            ($content = $this->uploadFile($model)       ) &&
            ($matrix  = $this->parseFile($content)      ) &&
            ($html    = $this->updateModels($id, $matrix))
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
    private function updateModels($id, $matrix)
    {
        $html  = [];

        $transaction = Yii::$app->db->beginTransaction();
        $rollback    = false;

        foreach($matrix as $row)
        {
            if(! $model = InventoryItem::find()->where(['inventory_id' => $id,
                                                        'iitem_id'     => $row['iitem_id'],
                                                        'ean13'        => $row['ean13']])
                                               ->one())
            {
                $model = new InventoryItem($row);
                $model->addError('ean13',"iitem_idとバーコード(ean13)が一致しません");
            }
            $model->actual_qty = $row['actual_qty'];

            if($model->hasErrors() || ! $model->save())
            {
                $rollback = true;
            }

            $html[] = $this->renderPartial('_row', ['model'=>$model,'row'=>$row]);
        }
        $model = $this->findModel($id);
        if(! $model->save() ){ var_dump($model->firstErrors); $rollback = true; }

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
        $column = [];
        $buff   = explode("\n", $content);
        $header = array_shift($buff);

        while(! preg_match('/^iitem_id/', $header))
            $header = array_shift($buff);

        $header = explode(",", $header);
        foreach($header as $idx => $value)
        {
            if(preg_match('/iitem_id/', $value))
                $column['iitem_id'] = $idx;

            if(preg_match('/ean13/', $value))
                $column['ean13'] = $idx;

            if(preg_match('/actual_qty/', $value))
                $column['actual_qty'] = $idx;
        }
        $validator = new \yii\validators\NumberValidator(['min'=>3, 'max'=>3]);
        if(! $validator->validate(count($column), $message))
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
                $row[$label] = ArrayHelper::getValue($chunk, $idx);

            $matrix[] = $row;
        }

        return $matrix;
    }

    /**
     * parseFile(): private subroutine for action batch-update
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
     * InventoryItemを再抽出し、再計算する
     */
    public function actionRefresh($id)
    {
        $model = $this->findModel($id);

       if($model->isSubmitted())
            throw new \yii\web\ForbiddenHttpException("入力済みの棚卸を編集することはできません");

        $cnt1 = $model->getItems()->count();

        $model->initItems(); // add more items if any
        $model->updateItems();

        $cnt2 = $model->getItems()->count();

        if($cnt1 < $cnt2)
            Yii::$app->session->addFlash('success',"対象品目が $cnt1 件から $cnt2 件になりました");
        else
            Yii::$app->session->addFlash('success',"再計算しました");

        return $this->redirect(['update','id'=>$id]);
    }

    /**
     * Try to update an existing InventoryItem model.
     * Do not care if update is successful or not.
     */
    private function actionUpdateItem(Inventory $model)
    {
        $id    = Yii::$app->request->post('iitem_id');
        $qty   = Yii::$app->request->post('actual_qty');
        $item  = $model->getItems()->andWhere(['iitem_id' => $id])->one();
        if(! $item || (null === $qty))
            throw new \yii\base\UserException('invalid parametor: inventory_id, iitem_id, actual_qty');

        $item->actual_qty = $qty;
        $item->updated_by = Yii::$app->user->id;
        $item->calcurate();

        if($item->save())
            $model->save(false,['update_date','updated_by']);

        if(Yii::$app->request->isAjax)
            return $this->renderPartial('items-grid',['model'=>$model]);

        if(! $url = Url::previous($this->module->id))
             $url = ['update', 'id'=>$model->inventory_id];

        return $this->redirect($url);
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
        if(!$model = Inventory::findOne($id))
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');

        if(($branch = Yii::$app->controller->module->branch) &&
            ! $branch->equals($model->branch)
        )
            throw new \yii\web\ForbiddenHttpException("指定の棚卸ID($id)は現在の拠点({$branch->name})と一致しません");

        return $model;
    }


    private function loadProvider($model)
    {
        $query = $model::find()->andWhere(['branch_id' => $this->module->branch->branch_id ]);
        
        return new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['create_date'=>SORT_DESC]],
        ]);
    }

}
