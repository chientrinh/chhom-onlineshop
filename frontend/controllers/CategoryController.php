<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/controllers/CategoryController.php $
 * $Id: CategoryController.php 4248 2020-04-24 16:29:45Z mori $
 */

namespace frontend\controllers;

use Yii;
use \yii\helpers\ArrayHelper;
use \common\models\Category;
use \common\models\ProductSubcategory;
use \common\models\Subcategory;

class CategoryController extends \yii\web\Controller
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
        if('echom-frontend' == Yii::$app->id)
            return $this->render('echom/index');

        return $this->render('index');
    }

    public function actionView($id, $grid=0)
    {
        $category = Category::findOne($id);
        if(! $category)
            throw new \yii\web\NotFoundHttpException();

        return $this->redirect(['viewbyname','name'=>$category->name,'grid'=>$grid]);
    }

    public function actionViewbyname($name, $grid=0, $subcategory_id=null)
    {
        if ($name === '書籍') {
            throw new \yii\web\NotFoundHttpException('書籍の取り扱いは終了しました。');
        }
        if ($name === 'ライブ配信') {
            throw new \yii\web\NotFoundHttpException('ご指定のページはありません');
        }
        if ($name === 'ライブ配信オプション') {
            throw new \yii\web\NotFoundHttpException('ご指定のページはありません');
        }

        $this->view->params['breadcrumbs'][] = ['label'=>"カテゴリー",'url'=>['/category']];
        $this->view->params['breadcrumbs'][] = ['label' => $name, 'url'=> \yii\helpers\Url::current()];

        $cache   = new \yii\caching\FileCache();
        $page_id = [
            \yii\helpers\Url::current(),
            Yii::$app->user->isGuest,
            ($user = Yii::$app->user->identity) ? $user->grade_id   : null,
            ($user = Yii::$app->user->identity) ? $user->isAgency() : null,
        ];
        $page_id = $cache->buildKey($page_id);

        if($html = $cache->get($page_id))
            return $this->renderContent($html);

        $categories   = $this->findAllByName($name);
        $searchModel = new \frontend\models\SearchProductMaster([
            'customer'    => Yii::$app->user->identity,
            'category_id' => ArrayHelper::getColumn($categories, 'category_id'),
        ]);
        $provider = $searchModel->search();
        $provider->query->andWhere(['or', ['vial_id' => null],
                                          ['<>','vial_id',\common\models\RemedyVial::DROP],
        ]);

        // 画面でH1タグに出力する名前。URLで入力した文字列では「雑」などとなるためカテゴリーから検索しているわけだが・・・
        // カテゴリ名の指定が「雑貨・衣類」のように「・」でつながっていない場合、かつ検索結果のカテゴリリストに含まれていない場合、検索結果の先頭を取る
        if(strpos($name, '・') === false && !in_array($name, ArrayHelper::getColumn($categories, 'name'))) 
            $name = array_shift(ArrayHelper::getColumn($categories, 'name'));

        if($subcategory_id)
        {
            $q1 = Subcategory::find()->orWhere(['subcategory_id'=>$subcategory_id])
                                                    ->orWhere(['parent_id'     =>$subcategory_id]);
            $q2 = ProductSubcategory::find()->where([
                'subcategory_id'=> $q1->select('subcategory_id')
            ]);
            $provider->query->andWhere(['mvtb_product_master.ean13'=>$q2->select('ean13')]);
        }

        $view = 'view';

        if('echom-frontend' == Yii::$app->id) {
            $view = 'echom/view';
        }
        
        $html = $this->renderPartial($view, [
            'title'       => sprintf('%s | %s | %s', $name, "カテゴリー", Yii::$app->name),
            'h1'          => $name,
            'searchModel' => $searchModel,
            'dataProvider'=> $provider,
            'categories'  => $categories,
            'grid'        => $grid,
        ]);



        // print($html);exit;

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
     * @return array
     */
    private function findAllByName($name)
    {
        $query = Category::find()
            ->from(Category::tableName().' t');

        // カテゴリ名の指定が「雑貨・衣類」のように「・」でつながっている場合、分割してINでWhere句を作成、分割前の名称も配列に加える
        if(strpos($name, '・') !== false) {
            $names = explode('・', $name);
            $names[] = $name;
            $query->where(['IN', 't.name', $names]);
        } else {
            $query->where(['like', 't.name', $name]);
        }

        if(! $query->count())
            throw new \yii\web\NotFoundHttpException(sprintf('not found category name: %s',$name));

        return $query->all();
    }

    /**
     * Cache の都合上 $this->render() より前でView->paramsを設定しておく
     */
    public function initViewParams($action)
    {
        $this->view->params['body_id'] = 'Search';

        if('viewbyname' == $action->id)
            $this->view->params['body_id'] = 'Search'; // in case cache is set, body_id must not be null

        if($cid = Yii::$app->request->get('category'))
            if($cat = Category::findOne($cid))
                $this->view->params['breadcrumbs'][] = ['label' => $cat->name, 'url'=>['/category/view','id'=>$cid]];

        if($keywords = Yii::$app->request->get('keywords'))
            $this->view->params['breadcrumbs'][] = ['label' => $keywords];
    }

}
