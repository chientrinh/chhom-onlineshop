<?php

namespace frontend\controllers;
use Yii;
use \common\models\Company;
use \common\models\Product;
use \common\models\Subcategory;
use \common\models\Category;

/**
 * @link $URL: https://tarax.toyouke.com/svn/MALL/frontend/controllers/CompanyController.php $
 * @version $Id: CompanyController.php 4067 2018-11-28 08:10:14Z kawai $
 *
 * Abstract class for frontend/controllers/{TY,HP,HE,HJ}Controller
 *
 */

abstract class CompanyController extends \yii\web\Controller
{
    public $defaultAction = 'product';

    /**
     * @var Company|null
     */
    public $company  = null;

    /**
     * @var ActiveQuery of Product | null
     */
    public $provider = null;

    /**
     * @brief set current Company
     */
    public function init()
    {
        parent::init();

        $company = Company::findOne(['key'=>$this->id]);
        if(! $company)
            throw new \yii\web\ServerErrorHttpException();

        $this->company = $company;

    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->initViewParams($action);

        return true;
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only'  => ['wholesale'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user->isAgencyOf($this->company); // allow agency only
                        },
                    ],
                    // everything else is denied for 'wholesale' action
                ],
            ],
            // no restriction for other actions
        ];
    }

    /**
     * render companies
     */
    public function actionIndex()
    {
        return $this->render('index', [ 'company' => $this->company ]);
    }

    public function actionView($page)
    {
        try
        {
            if(preg_match('/(jpg|png)$/',$page))
                return Yii::$app->response->sendFile($this->viewPath . '/' . $page);

            return $this->render($page);
        }
        catch (InvalidParamException $e)
        {
            throw new HttpException(404);
        }
    }

    /**
     * display all products belong to the company
     */
    public function actionProduct($grid=null)
    {
        $this->view->params['breadcrumbs'][] = ['label' => '商品一覧', 'url'=> ['product']];
        $this->view->title = sprintf('商品一覧 | %s | %s', $this->company->name, Yii::$app->name);

        $cache   = new \yii\caching\FileCache();
        $page_id = [
            \yii\helpers\Url::current(),
            Yii::$app->user->isGuest,
            ($user = Yii::$app->user->identity) ? $user->grade_id : null,
            ($user = Yii::$app->user->identity) ? $user->isAgencyOf($this->company->company_id) : null,
        ];
        $page_id = $cache->buildKey($page_id);

        if($html = $cache->get($page_id))
            return $this->renderContent($html);

        // limit categories to company's teritory
        $categories = \common\models\Category::find()->where(['seller_id'=>$this->company->company_id])->all();

        $searchModel = new \frontend\models\SearchProductMaster([
            'customer'    => Yii::$app->user->identity,
            'company'     => $this->company->company_id,
            'keywords'    => Yii::$app->request->get('keywords'),
            'category_id' => Yii::$app->request->get('category',
                                                     \yii\helpers\ArrayHelper::getColumn($categories, 'category_id')),
        ]);
        if(! $searchModel->category_id)
             $searchModel->category_id = null;
        $provider = $searchModel->search();
        $provider->query->andWhere(['or', ['vial_id' => null],
                                          ['<>','vial_id',\common\models\RemedyVial::DROP],
        ]);

        $html = $this->renderPartial('product',[
            'title'       => sprintf('%s | %s | %s', "商品一覧", $this->company->name, Yii::$app->name),
            'h1'          => $this->company->name,
            'breadcrumbs' => [
                ['label' => $this->company->name, 'url'=>['/'.$this->company->key]],
                ['label' => "商品一覧"],
            ],
            'searchModel' => $searchModel,
            'dataProvider'=> $provider,
            'grid'        => $grid,
            'categories'  => $categories,
        ]);

        $duration   = 3600 * 24 * 365; // 365 days
        $dependency = new \yii\caching\DbDependency([
            'sql' => 'SELECT max(update_date) FROM mvtb_product_master'
        ]);

        $cache->set($page_id, $html, $duration, $dependency);

        return $this->renderContent($html);
    }

    /**
     * display all products belong to the company
     */
    public function actionSubcategory($id=null, $grid=null)
    {
        if($sub =  Subcategory::findOne($id))
            $this->view->params['breadcrumbs'][] = ['label' => $sub->fullname];

        $cache   = new \yii\caching\FileCache();
        $page_id = [
            \yii\helpers\Url::current(),
            Yii::$app->user->isGuest,
            ($user = Yii::$app->user->identity) ? $user->grade_id : null,
            ($user = Yii::$app->user->identity) ? $user->isAgencyOf($this->company->company_id) : null,
        ];
        $page_id = $cache->buildKey($page_id);

        if($html = $cache->get($page_id))
            return $this->renderContent($html);

        // limit categories to company's teritory
        $q1 = Subcategory::find()
            ->select('subcategory_id')
            ->where(['company_id'=>$this->company->company_id])
            ->andWhere(['or',
                        ['subcategory_id'=>$id],
                        ['parent_id'     =>$id]]);

        $categories = \common\models\Category::find()
                    ->where(['seller_id'=>$this->company->company_id])
                    ->select('category_id')
                    ->column();

        $searchModel = new \frontend\models\SearchProductMaster([
            'customer'    => Yii::$app->user->identity,
            'company'     => $this->company->company_id,
            'keywords'    => Yii::$app->request->get('keywords'),
            'category_id' => Yii::$app->request->get('category', $categories),
            'subcategory_id' => $id,
        ]);

        $dataProvider = $searchModel->search([]);

        $model = Subcategory::findOne($id);

        $html = $this->renderPartial('product',[
            'title'       => sprintf('%s | %s | %s', "商品一覧", $this->company->name, Yii::$app->name),
            'h1'          => $this->company->name,
            'breadcrumbs' => [
                ['label' => $this->company->name, 'url'=>['/'.$this->company->key]],
                ['label' => isset($model) ? $model->fullname : "商品一覧"],
            ],
            'searchModel' => $searchModel,
            'dataProvider'=> $dataProvider,
            'grid'        => $grid,
            'categories'  => $categories,
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

    public function actionWholesale()
    {
        if((! $user = Yii::$app->user->identity) ||
           ! $user->isAgencyOf($this->company)
        )
            throw new \yii\web\ForbiddenHttpException();
        $model = new \frontend\models\SearchProductMaster();
        $model->customer = Yii::$app->user->identity;
        $model->company  = $this->company->company_id;
        $provider = $model->searchForAgency(Yii::$app->request->queryParams);


        return $this->render('wholesale',[
            'company'      => $this->company,
            'dataProvider' => $provider,
            'searchModel'  => $model,
        ]);
    }

    private function getExcludes($subcategory_ids)
    {
            $excludes = \common\models\ProductSubcategory::find()
                                                ->select('ean13')
                                                ->where(['in', 'subcategory_id', [Subcategory::PKEY_HJ_AGENCY_EXCLUDE]])
                                                ->all();

            return yii\helpers\ArrayHelper::getColumn($excludes, 'ean13');

    }

    /**
     * Cache の都合上 $this->render() より前でView->paramsを設定しておく
     */
    public function initViewParams($action)
    {
        if(in_array($action->id, ['product','subcategory']))
            $this->view->params['body_id'] = 'Search'; // in case cache is set. the value must not be null

        if ($this->company->company_id !== Company::PKEY_HP)
            $this->view->params['breadcrumbs'][] = ['label' => $this->company->name, 'url'=> [$this->defaultAction]];
    }

}
