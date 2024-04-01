<?php

namespace backend\controllers;

use Yii;
use common\models\Category;
use common\models\Company;
use common\models\Product;
use common\models\ProductMaster;
use backend\models\SearchProduct;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

/**
 * ProductController implements the CRUD actions for Product model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/ProductController.php $
 * $Id: ProductController.php 4237 2020-03-12 04:30:38Z kawai $
 */

class ProductController extends BaseController
{
    public function behavior()
    {
        return [
            'access' => [
                'class' => \yii\filters\accesscontrol::className(),
                'roles' => ['tenant','worker','manager'],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        if('index' === $action->id)
        {
            $cid = Yii::$app->request->get('company');

            if(! Yii::$app->user->can('viewProduct',['company_id'=>$cid]))
                throw new \yii\web\ForbiddenHttpException(sprintf(
                    "指定モデルの所有権がありません [model.company_id != user.company_id] (%d != %d)",
                    $cid,
                    Yii::$app->user->identity->company_id
                ));
        }

        $this->view->params['breadcrumbs'][] = ['label' => "商品", 'url' => ['index']];

        return true;
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex($company=0)
    {
        if(Yii::$app->request->get('csv', null))
            return $this->actionCsv($company);

        $model    = new SearchProduct(['company'=>$company]);
        $provider = $model->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $model,
            'dataProvider' => $provider,
        ]);
    }

    public function actionRecommend()
    {
        $model    = new SearchProduct();
        $provider = $model->search(Yii::$app->request->queryParams, true);

        return $this->render('recommend', [
            'searchModel'  => $model,
            'dataProvider' => $provider,
        ]);
    }

    /**
     * Show Products of specified properties
     */
    public function actionSearch($company=0)
    {
        $model  = new SearchProduct(['company'=>$company]);
        $provider = $model->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $model,
            'dataProvider' => $provider,
        ]);
    }

    public function actionCsv($company=0)
    {
        $model    = new SearchProduct(['company'=>$company]);
        $provider = $model->search(Yii::$app->request->queryParams);

        $provider->pagination = false;
        Yii::$app->formatter->nullDisplay = "<span>(なし)</span>";

        return $this->render('index_csv', [
            'searchModel'  => $model,
            'dataProvider' => $provider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $target='info')
    {
        $model = $this->findModel($id);

        if($model->isExpired())
            Yii::$app->session->addFlash('warning', sprintf("%s は販売が終了しています", $model->name));

        if('sales' == $target)
            return $this->render('sales', [
                'model' => $model,
            ]);

        if('offer' == $target)
            return $this->render('offer', [
                'model' => $model,
            ]);

        if('history' == $target)
            return $this->render('history', [
                'model' => $model,
            ]);

        if('inventory' == $target)
            return $this->actionInventory($model);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    private function actionInventory($model)
    {
        // 拠点ごとに最新のinventory_idを割り出す
        $command = Yii::$app->db->createCommand('
          SELECT i.inventory_id
          FROM dtb_inventory i
          INNER JOIN (
              SELECT branch_id, MAX(create_date) AS create_date
               FROM dtb_inventory GROUP BY branch_id
          ) AS MAX USING (branch_id, create_date);
        ');
        $query = \common\models\InventoryItem::find();
        $query->andWhere(['inventory_id' => $command->queryAll()])
              ->andWhere(['product_id'   => $model->product_id]);

        $models = $this->loadInventoryModels($model->product_id);

        return $this->render('inventory', [
            'model'    => $model,
            'query'    => $query,
            'allModels'=> $models,
        ]);
    }

    public function actionPrint($id, $target='price-tag')
    {
        $model = $this->findModel($id);

        $widget = \common\widgets\doc\product\ProductLabel::begin(['model'=>$model]);
        if('price-tag' == $target)
            $widget->layout = '{name}{price}{barcode}';

        $filename = $widget->renderPdf();
        $inline   = true;
        $mime     = 'application/pdf';
        Yii::$app->response->setDownloadHeaders(basename($filename), $mime, $inline);

        return Yii::$app->response->sendFile($filename, $inline);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();
        $model->applyDefaultValues();

        $user = Yii::$app->user->identity;
        if(Company::PKEY_TROSE == $user->company_id)
            $model->category_id = Category::findOne(['seller_id'=>Company::PKEY_TROSE])->category_id;

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view', 'id' => $model->product_id]);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $param = Yii::$app->request->post();
        if($param)
        {
            $model->load($param);

            if($model->isBook())
            {
                $book = new \common\models\Book();
                $book->product_id = $model->product_id;
                $book->load($param);
            }

        } else {
            $model->keywords = $model->hasOne(ProductMaster::className(), ['product_id' => 'product_id'])->one()->keywords;
        }
        if($param && $model->save())
        {
            if(isset($book)){ $book->save(); }

            Yii::$app->session->addFlash('success', sprintf("%s を更新しました", $model->name));
            return $this->redirect(['view', 'id' => $model->product_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionExpire($id)
    {
        $model = $this->findModel($id);

        if($model->isExpired())
        {
            Yii::$app->session->addFlash('warning', sprintf("%s は販売終了になっています", $model->name));
            return $this->redirect(['view', 'id'=>$id]);
        }

        $model->expire_date = new \yii\db\Expression('NOW()');

        if($model->save())
            Yii::$app->session->addFlash('success', sprintf("%s を販売終了にしました", $model->name));
        else
            Yii::$app->session->addFlash('error', sprintf("%s を販売終了にできませんでした", $model->name));

        return $this->redirect(['view', 'id'=>$id]);
    }

    public function actionActivate($id)
    {
        $model = $this->findModel($id);

        if(! $model->isExpired())
        {
            Yii::$app->session->addFlash('warning', sprintf("%s は販売再開になっています", $model->name));
            return $this->redirect(['view', 'id'=>$id]);
        }

        $model->expire_date = $model::DATETIME_MAX;

        if($model->save())
            Yii::$app->session->addFlash('success', sprintf("%s を販売再開にしました", $model->name));
        else
            Yii::$app->session->addFlash('error', sprintf("%s を販売再開にできませんでした", $model->name));

        return $this->redirect(['view', 'id'=>$id]);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (null === ($model = Product::findOne($id)))
            throw new NotFoundHttpException('The requested page does not exist.');

        if(! Yii::$app->user->can('viewProduct',['company_id'=>$model->company->company_id]))
            throw new \yii\web\ForbiddenHttpException(sprintf(
                "指定モデルの所有権がありません [model.company_id != user.company_id] (%d != %d)",
                $model->company->company_id,
                Yii::$app->user->identity->company_id)
            );

        if(in_array($this->action->id, ['update','expire','activate']))
            if(! Yii::$app->user->can('updateProduct',['company_id' => $model->company->company_id]))
                throw new \yii\web\ForbiddenHttpException(
                    "指定モデルを編集する権限がありません"
                );

        return $model;
    }

    /**
     * 拠点ごとに当該商品の在庫数を割り出す
     */
    private function loadInventoryModels($id)
    {
        $models = [];
        $template = new \common\models\Inventory();
        $branches = \common\models\Branch::find()->indexBy('branch_id')->all();
        foreach($branches as $branch_id => $branch)
        {
            $template->branch_id = $branch_id;

            $inbound  = $template->getTransferInboundItems()->andWhere(['product_id'=>$id])
                                 ->sum('qty_shipped');

            $outbound = $template->getTransferOutboundItems()->andWhere(['product_id'=>$id])
                                 ->sum('qty_shipped');

            $sold     = $template->getPurchaseItems()->andWhere(['product_id'=>$id])
                                 ->sum('quantity');

            if($prev  = $template->prev)
            $prev     = $prev->getItems()->andWhere(['product_id'=>$id])
                             ->sum('actual_qty');

            $idealQty = (int)$prev - (int)$sold - (int)$outbound + (int)$inbound;

            $models[$branch_id] = [
                'branch'   => $branch,
                'idealQty' => $idealQty,
            ];
        }

        return $models;
    }

}
