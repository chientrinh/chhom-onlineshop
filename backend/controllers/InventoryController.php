<?php
namespace backend\controllers;

/**
 * InventoryController implements the CRUD actions for Inventory model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/InventoryController.php $
 * $Id: InventoryController.php 2323 2016-03-27 09:18:25Z mori $
 */

use Yii;
use common\models\Inventory;

class InventoryController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only'  => ['print'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['wizard'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => '棚卸', 'url' => ['index']];

        return true;
    }

    /**
     * Lists all Inventory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Inventory();
        $model->load(Yii::$app->request->queryParams);
        $provider = $this->loadProvider($model);

        return $this->render('index', [
            'searchModel'  => $model,
            'dataProvider' => $provider,
        ]);
    }

    /**
     * Displays a single Inventory model.
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
     * Creates a new Inventory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Inventory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->inventory_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Inventory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post()) &&
           $model->save())
        {
            return $this->redirect(['view', 'id' => $model->inventory_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Inventory model with ProductCost.
     * 取扱注意: 経理・役員・システム担当者のみアクセス可能とすること!!
     *
     * @param integer $id
     * @return mixed
     */
    public function actionPrint($id, $format='html')
    {
        $model     = $this->findModel($id);
        $totalCost = 0;

        foreach($model->items as $item)
        {
            if($c = $item->cost)
                $totalCost += ($item->actual_qty * $c->cost);
        }
        if('html' == $format)
            return $this->render('print',[
                'model' => $model,
                'totalCost' => $totalCost,
            ]);

        // else: 'csv' == format
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

        $basename = sprintf('%06d-%s.csv', $id, date('Ymd_His'));
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
            "原価総額,". $totalCost,
            "",
            "",
        ];
        $widget = new \common\widgets\CsvView([
            'query'      => $query,
            'header' => [
                'category_id','subcategory_id','ean13','品名','前回','入庫','出庫','売上','帳簿在庫','棚卸数','+/-',
                '@原価', '小計'
            ],
            'attributes' => [
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
                'ean13',
                'kana','prev_qty','in_qty','out_qty','sold_qty','ideal_qty','actual_qty','diff_qty',
                function($data){
                    if($c = $data->cost) { return sprintf('%.2f',$c->cost); }
                    return '未定義';
                },
                function($data){
                    if($c = $data->cost) { return sprintf('%.2f',$c->cost * $data->actual_qty); }
                    return 0;
                },
            ],
        ]);

        // draw preceeding text
        echo mb_convert_encoding(implode($widget->eol, $header), $widget->charset, Yii::$app->charset);

        // draw CSV contents
        $widget->run();

        echo 'end of file';
        return;
    }

    /**
     * Finds the Inventory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Inventory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Inventory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function loadProvider($model)
    {
        $query = $model::find();

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['create_date'=>SORT_DESC]],
        ]);

        if (! $model->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'inventory_id' => $model->inventory_id,
            'branch_id'    => $model->branch_id,
            'created_by'   => $model->created_by,
            'updated_by'   => $model->updated_by,
        ])
              ->andFilterWhere(['like','create_date',$model->create_date])
              ->andFilterWhere(['like','update_date',$model->update_date]);

        return $dataProvider;
    }
}
