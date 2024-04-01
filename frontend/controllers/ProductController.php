<?php

namespace frontend\controllers;

use Yii;
use common\models\Branch;
use common\models\Category;
use common\models\Company;
use common\models\Inventory;
use common\models\Product;
use common\models\ProductMaster;
use common\models\ProductSubcategory;
use common\models\Purchase;
use common\models\PurchaseItem;
use common\models\Subcategory;
use frontend\models\SearchProductMaster;
use common\models\Stock;

/**
 * ProductController implements the CRUD actions for Product model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/controllers/ProductController.php $
 * $Id: ProductController.php 4248 2020-04-24 16:29:45Z mori $
 */

class ProductController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->initViewParams($action);

        return true;
    }

    public function actionIndex()
    {
        return $this->redirect(['search']);
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionSearch($grid=null)
    {
        $this->view->title = sprintf('商品検索 | %s', Yii::$app->name);
        $this->view->params['body_id'] = 'Search';

        $cache   = new \yii\caching\FileCache();
        $page_id = [
            \yii\helpers\Url::current(),
            Yii::$app->user->isGuest,
            ($user = Yii::$app->user->identity) ? $user->grade_id   : null,
            ($user = Yii::$app->user->identity) ? $user->isAgency() : null,
        ];
        $page_id = $cache->buildKey($page_id);

       // ライブ配信チケットの場合はキャッシュは使わない 2020/04/24 : kawai
       if('echom-frontend' != Yii::$app->id && $html = $cache->get($page_id))
           return $this->renderContent($html);

        $searchModel  = new SearchProductMaster([
            'customer' => Yii::$app->user->identity,
            'keywords' => Yii::$app->request->get('keywords'),
        ]);

        $search = 'search';
        $searchTitle = "商品検索";

        if('echom-frontend' == Yii::$app->id)
            $search = 'echom/search';
            $searchTitle = "検索結果";


        if($cid = Yii::$app->request->get('category'))
        {
            $q = Yii::$app->db
               ->createCommand('SELECT category_id FROM mtb_category WHERE name = (SELECT name FROM mtb_category WHERE category_id = :cid)')
               ->bindValues([':cid'=>$cid]);

            $searchModel->category_id = $q->queryColumn();
        }
        $dataProvider = $searchModel->search([]);


        $html = $this->renderPartial($search, [
            'title'        => $searchTitle,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'grid'         => $grid,
            'categories'   => \common\models\Category::find()->where('category_id != 24')->andWhere('category_id != 26')->all(),
        ]);

        $duration   = 3600 * 24 * 365; // 365 days
        $dependency = new \yii\caching\DbDependency([
            'sql' =>
 'SELECT MAX(udate) FROM (
    SELECT MAX(update_date) AS udate FROM dtb_product_subcategory
    UNION
    SELECT MAX(update_date) AS udate FROM mvtb_product_master
  ) AS t'
        ]);

        $cache->set($page_id, $html, $duration, $dependency);

        return $this->renderContent($html);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
	// 過去に配布した配信チケットのURLから別URLへリダイレクトさせる
        if($id == 3014) {
            $id = 3037;
            return $this->redirect(['view','id'=>$id]);
        }

        // get model
        $model  = $this->findModel($id);

        // get additional info
        $search = new \common\models\SearchProductFavor([
            'branch'   => Branch::findOne(0),
            'customer' => Yii::$app->user->identity,
            'item'     => $model,
        ]);
        $search->validate();

        // 在庫数の取得
        $stock = Stock::getActualQty($id);

        if ($stock === false)
            $stockQty = Stock::ALERT_QTY;
        else {
            $stockQty = $stock;
            if ($stock === 0)
                $model->in_stock = 0; 
        }
           
        $isFavorite = ($user = Yii::$app->user->identity) &&
                       $user->getFavorites()->andWhere(['product_id'=>$model->product_id])->exists();

        $date = new \common\models\DeliveryDateTimeForm(['company_id'=>$model->company->company_id]);

        // setup cache
        $cache   = new \yii\caching\FileCache([
            'keyPrefix' => 'product-view-',
        ]);
        $page_id = $cache->buildKey([ $id,
                                      $search->discount->rate,
                                      $search->point->rate,
                                      $stockQty,
                                      $isFavorite,
                                      Yii::$app->request->get('magazine')
        ]);

        // ライブ配信チケットの場合はキャッシュは使わない 2020/04/24 kawai
        if('echom-frontend' != Yii::$app->id && $html = $cache->get($page_id))
            return $this->renderContent($html);

        // save cache
        $duration   = 3600 * 24 * 365; // 365 days
        $dependency = new \yii\caching\DbDependency([
            'sql'   => 'SELECT MAX(update_date) FROM mvtb_product_master',
        ]);

        $view = 'view';
        if('echom-frontend' == Yii::$app->id) {
            if($id == 2290 || $id == 2291) {
                $view = 'echom/view_210227';
            } else {
                $view = 'echom/view';
            }
        }
        if('echom-frontend' == Yii::$app->id) {
            if($id == 2342 || $id == 2343 || $id == 2344 || $id == 2345 || $id == 2346 || $id == 2347 || $id == 2348) {
                $view = 'echom/view_210417';
            } else {
                $view = 'echom/view';
            }
        }

        $html = $this->renderPartial($view, [
            'model'       => $model,
            'discountRate'=> $search->discount->rate,
            'pointRate'   => $search->point->rate,
            'stockQty'    => $stockQty,
            'isFavorite'  => $isFavorite,
            'date'        => $date,
        ]);

        $cache->set($page_id, $html, $duration, $dependency);

        return $this->renderContent($html);
    }

    /**
     * Displays images of a single Product model.
     * @param integer $id
     * @param string  $top
     * @return mixed
     */
    public function actionViewImage($id,$top=null)
    {
        $model  = $this->findModel($id);
        $images = $model->images;
        $buf    = [];

        if($top)
        foreach($images as $k => $image)
        {
            if($top == $image->basename)
                break;

            unset($images[$k]);
            $buf[] = $image;
        }
        $images = array_merge($images, $buf);

        $view = 'view-image';
        if('echom-frontend' == Yii::$app->id)
            $view = 'echom/view-image';

        return $this->render($view, [
            'model'  => $model,
            'images' => $images,
        ]);
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
        // 過去に配布した配信チケットのURLから別URLへ
        if($id == 3014) {
            $id = 3037;
        }


        $model = Product::findOne($id);

        //イベント参加者限定商品はチェック無し
        if ($model->subcategories) {
            $ecampaign = \common\models\EventCampaign::find()->active()
                    ->andWhere(['or', ['subcategory_id' => $model->subcategories->subcategory_id], ['subcategory_id2' => $model->subcategories->subcategory_id]])
                    ->one();
            if ($ecampaign) {
                return $model;
            }
        }

        // 2018/12/19 メルマガ特典の商品は公開区分を参照しない
        if ($model->subcategories && $model->subcategories->subcategory_id === Subcategory::PKEY_MAGAZINE_CAMPAIGN) {
            return $model;
        }

        if(! $model)
            throw new \yii\web\NotFoundHttpException('ご指定のページは見つかりません');

        if($model->isExpired())
            throw new \yii\web\NotFoundHttpException('ご指定の商品は販売を終了しました');

        if(\common\models\ProductRestriction::PKEY_INSTORE_ONLY <= $model->restrict_id)
            throw new \yii\web\NotFoundHttpException('ご指定のページは見つかりません');

        if ($model->category->seller_id === Company::PKEY_HP)
            throw new \yii\web\NotFoundHttpException('ご指定の商品は販売を終了しました');

        if($model->restrict_id)
        {
            $user = Yii::$app->user->identity;

            if(! $user)
                throw new \yii\web\ForbiddenHttpException('ご指定の商品は会員限定です。会員の方はログイン後、もう一度このページを開いてください');

            if($user->grade_id < $model->restrict_id)
            {
                // TODO 2018/09/05 指定顧客が購入できる商品を増やす対応、期間が終了したら削除する
                if ($user->id === 27321 && in_array($model->product_id, ['471', '474', '475'])) {}
                else {
                    $grade = \common\models\CustomerGrade::findOne($model->restrict_id);
                    throw new \yii\web\ForbiddenHttpException(sprintf('ご指定の商品は%s会員限定です。', $grade ? $grade->name : ''));
                }
                // -- ここまで --
//                $grade = \common\models\CustomerGrade::findOne($model->restrict_id);
//                throw new \yii\web\ForbiddenHttpException(sprintf('ご指定の商品は%s会員限定です。', $grade ? $grade->name : ''));
            }
        }

        return $model;
    }

    // 野菜セットMの理論在庫を割り出す
    // @return integer
    private static function getIdealQty($product_id)
    {
        $cid = ProductMaster::find()->where(['product_id' => $product_id])
                                    ->select('company_id')
                                    ->scalar();

        // 理論在庫、又は拠点が六本松になる商品以外の場合は除外
        if(! $cid || Company::PKEY_TY != $cid)
            return false; 

        return true;
    }

    /**
     * Cache の都合上 $this->render() より前でView->paramsを設定しておく
     */
    public function initViewParams($action)
    {
        $this->view->params['body_id'] = 'Search';

        if('view' == $action->id)
        {
            $model = $this->findModel(Yii::$app->request->get('id'));
            $this->view->title = sprintf('%s | %s | %s', $model->name, $model->company->name, Yii::$app->name);
            $this->view->params['body_id'] = 'Product';

            if('echom-frontend' == Yii::$app->id) {
                $this->view->params['breadcrumbs'] = [];

            } else {
                $this->view->params['breadcrumbs'][] = ['label' => $model->company->name, 'url' => [sprintf('/%s',$model->company->key)]];

                if($sub = ProductSubcategory::findOne(['ean13'=>$model->barcode]))
                    if($sub = $sub->subcategory)
                        $this->view->params['breadcrumbs'][] = ['label' => $sub->fullname, 'url' => [$model->company->key.'/subcategory','id'=>$sub->subcategory_id]];

                $this->view->params['breadcrumbs'][] = $model->name;
            }
            // settings for image slider
            $jscode = "
              $(document).ready(function(){
                $('.bxslider').bxSlider({
                  infiniteLoop: true,
                  hideControlOnEnd: true,
                  speed: 500,
                  useCSS: false,
                  controls: true,
                  captions: true
                });
              });
            ";
            $this->view->registerJs($jscode, \yii\web\View::POS_LOAD);
            $this->view->registerJsFile('@web/js/flexslider/jquery.bxslider.js', ['depends'=>['yii\web\YiiAsset','yii\bootstrap\BootstrapAsset']]);
        }
        elseif('search' == $action->id)
            $this->view->params['breadcrumbs'][] = ['label' => '商品検索', 'url' => ['search']];

        if($cid = Yii::$app->request->get('category'))
            if($cat = Category::findOne($cid))
                $this->view->params['breadcrumbs'][] = ['label' => $cat->name, 'url'=>['/category/view','id'=>$cid]];

        if($keywords = Yii::$app->request->get('keywords'))
            $this->view->params['breadcrumbs'][] = ['label' => $keywords];
    }

}
