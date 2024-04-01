<?php

namespace frontend\modules\pointing\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/pointing/controllers/BaseController.php $
 * $Id: BaseController.php 3620 2017-09-29 08:04:43Z kawai $
 */

use Yii;
use \yii\helpers\ArrayHelper;
use \common\models\Company;
use \common\models\Category;

abstract class BaseController extends \yii\web\Controller
{
    public $company;
    public $nav;
    public $nav2;
    public $crumbs = [
        'index' =>['label'=>"履歴",],
        'view'  =>['label'=>"閲覧",],
        'create'=>['label'=>"起票",],
        'update'=>['label'=>"修正",],
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    // deny guest users
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    // allow only when user is agency of this controller's company
                    [
                        'allow' => true,
                        'matchCallback' => function()
                        {
                            return (Yii::$app->user->identity->isAgencyOf($this->company) ||
                                    Yii::$app->user->identity->isHomoeopath()             );
                        }
                    ],
                ],
            ],
        ];
    }

    public function init()
    {
        parent::init();

        $this->company = $this->findCompany($this->id);

    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->initBreadcrumbs($action);
        $this->initNav($action);

        return true;
    }

    public function afterAction($action, $result)
    {
        if(! parent::afterAction($action, $result))
            return false;

        if($this->module->pointForm)
        {
            $this->module->pointForm->mergeItems();
            $this->module->pointForm->compute();
        }

        return $result;
    }

    public function actionIndex()
    {
        $searchModel  = new \common\models\Pointing([
            'seller_id' => Yii::$app->user->id,
        ]);
        $searchModel->load(Yii::$app->request->queryParams);

        $dataProvider = $this->loadProvider($searchModel);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', ['model' => $model]);
    }

    public function actionReceipt($id)
    {
        $html  = \common\widgets\doc\pointing\Receipt::widget([
            'model' => $this->findModel($id),
        ]);

        $this->layout = '/none';
        return $this->renderContent($html);
    }

    public function actionCreate()
    {
        if(0 < $this->module->pointForm->pointing_id) // now editing exsisting record
            return $this->redirect(['update', 'id'=>$this->module->pointForm->pointing_id]);

        $this->module->pointForm->validate();

        return $this->render('create', ['model' => $this->module->pointForm]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id); // check if this model is accesible for the user

        if($id != $this->module->pointForm->pointing_id)
        {
            $form = \common\models\PointingForm::findOne($id);

            $form->items = $model->items;

            $this->module->pointForm = $form;
            $this->module->reloadBuffer();
        }

        $this->module->pointForm->validate();

        return $this->render('update', ['model'=> $this->module->pointForm]);
    }

    public function actionApply($target)
    {
        if('barcode'  == $target)
            $this->applyBarcode(Yii::$app->request->get('barcode'));

        elseif('customer' == $target)
            $this->applyCustomer(Yii::$app->request->get('id'));

        elseif('product'  == $target)
            return $this->applyBarcode(Yii::$app->request->get('barcode'));

        elseif('quantity' == $target)
            $this->applyQuantity(Yii::$app->request->get());

        elseif('summary'  == $target)
            $this->applySummary(Yii::$app->request->get());

        elseif('reset'  == $target)
            $this->module->pointForm = new \common\models\PointingForm([
                'seller_id'  => Yii::$app->user->id,
                'company_id' => $this->company->company_id,
            ]);

        else
            throw new \yii\web\NotFoundHttpException();

        return $this->redirect('create');
    }

    public function actionFinish()
    {
        // success
        if($this->module->pointForm->validate() && $this->module->pointForm->save())
        {
            $this->module->clearBuffer();
            return $this->redirect(['receipt','id'=>$this->module->pointForm->pointing_id]);
        }

        // failure
        return $this->redirect('create');
    }

    public function actionSearch($target)
    {
        if('customer' == $target)
            return $this->actionSearchCustomer();

        elseif('product'  == $target)
            return $this->actionSearchProduct();

        else
            throw new \yii\web\NotFoundHttpException();
    }

    private function actionSearchCustomer()
    {

        $searchModel  = new \frontend\models\SearchCustomer();
        $searchModel->load(Yii::$app->request->queryParams);
    	if($tel = Yii::$app->request->post('tel'))
    	{
    		$searchModel->tel = $tel;
    		if(true == $searchModel->validate())
    			$dataProvider = $this->loadProvider4Customer($searchModel->tel);
    	}
        else
        {
            $dataProvider = new \yii\data\ArrayDataProvider(['allModels'=>[]]);
        }
        return $this->render('search',[
            'title'        => 'お客様を検索',
            'viewFile'     => '_customer',
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    private function actionSearchProduct()
    {
        $searchModel  = new \frontend\models\SearchProductMaster();
        $searchModel->customer = Yii::$app->user->identity;
        $searchModel->company  = $this->company->company_id;
        $dataProvider = $searchModel->searchForAgency(Yii::$app->request->queryParams);



        return $this->render('search',[
            'title'        => '商品を検索',
            'viewFile'     => '_product',
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    private static function addFlash($key, $value)
    {
        Yii::$app->session->addFlash($key, $value);
    }

    private function applyBarcode($barcode)
    {
        $barcode = mb_convert_kana($barcode, 'as'); // 全角 -> 半角
        $barcode = trim($barcode);

        $finder = new \common\components\ean13\ModelFinder(['barcode' => $barcode]);
        $model  = $finder->getOne();
        if($model instanceof \common\models\Product)
            return $this->applyProduct($model);

        elseif($model instanceof \common\models\RemedyStock)
            return $this->applyProduct($model);

        elseif($model instanceof \common\models\Customer)
            $this->applyCustomer($model->customer_id);

        elseif(! $finder->validate())
            self::addFlash('error', "バーコードにエラーがあるようです。もう一度スキャンしてください。<br>");

        elseif(! $model)
            self::addFlash('error', "検索できませんでした。バーコードをもう一度スキャンしてください。<br>");

        return $this->redirect('create');
    }

    /* @return void */
    private function applyCustomer($id)
    {
        $this->module->pointForm->customer_id = $id;

        if($customer = $this->module->pointForm->customer)
            self::addFlash('success', sprintf("<strong>%s</strong>さんを設定しました", $customer->name));
        else
            self::addFlash('error', sprintf("顧客が設定できませんでした: ID = %s", $id));

        return;
    }

    /* @return void */
    private function applyProduct($product)
    {
        $item = new \common\models\PointingItem();
        foreach($item->attributes as $name => $value)
        {
            if(isset($product->$name))
            //if($product->hasAttribute($name))
            {
                if($name == 'code') 
                    $item->$name = $product->barcode;
                else               
                    $item->$name = $product->$name;
            }
        }
        $item->quantity = 1;
        $this->module->pointForm->items[] = $item;
        self::addFlash('success', sprintf("<strong>%s</strong>を追加しました", $item->name));

        return $this->redirect(['search', 'target'=>'product']);
    }

    /* @return void */
    private function applyQuantity($params)
    {
        $seq = ArrayHelper::getValue($params, 'seq', null);
        $vol = ArrayHelper::getValue($params, 'vol', null);
        if((null === $seq) || (null === $vol))
            return;

        $this->module->pointForm->items[$seq]->quantity += $vol;

        $item = $this->module->pointForm->items[$seq];
        self::addFlash('success', sprintf("<strong>%s</strong> が <strong>%s 点</strong> になりました", $item->name, $item->quantity));
    }

    /* @return void */
    private function applySummary($params)
    {
        $point_consume = ArrayHelper::getValue($params, 'point_consume', null);
        $receive       = ArrayHelper::getValue($params, 'receive',       null);
        $note          = ArrayHelper::getValue($params, 'note',       null);

        if(null !== $point_consume)
            $this->module->pointForm->point_consume = $point_consume;

        if(null !== $receive)
            $this->module->pointForm->receive = $receive;

        if(null !== $note)
            $this->module->pointForm->note = $note;
    }

    public function actionExpire($id)
    {
        $model = $this->findModel($id);

        if($model->isExpired())
            self::addFlash('error',"その伝票はすでに無効となっています");

        $model->expire();

        return $this->render('view', ['model'=>$model]);
    }

    protected static function findCompany($key)
    {
        $company = \common\models\Company::findOne(['key' => $key]);

        if(! $company)
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');

        return $company;
    }

    protected static function findModel($id)
    {
        $model = \common\models\Pointing::findOne($id);

        if(! $model || ($model->seller_id != Yii::$app->user->id))
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');

        if($model->isExpired())
            self::addFlash('error', "この伝票は無効です");

        return $model;
    }

    private function initBreadcrumbs($action)
    {
        $this->view->params['breadcrumbs'] = [
            ['label' => 'マイページ', 'url' => ['/profile']],
            ['label' => $this->module->name, 'url' => [sprintf('/%s/%s',$this->module->id, $this->id)],],
        ];

        if(isset($this->crumbs[$action->id]))
            $this->view->params['breadcrumbs'][] = $this->crumbs[$action->id];

        return true;
    }

    private function initNav($action)
    {
        $this->nav = \yii\bootstrap\Nav::begin([
            'items'   => [
                ['label' => "履歴",    'url' => [ 'index'], 'active' => ('index'  == $action->id) ],
                ['label' => "起票",    'url' => [ 'create'],'active' => in_array($action->id, ['create','search']) ],
            ],
            'options' => ['class' =>'nav-tabs alert-info'],
        ]);

        $target = Yii::$app->request->get('target');
        $this->nav2 = \yii\bootstrap\Nav::begin([
            'items'   => [
                ['label' => "かご",    'url' => [ 'create'], 'active' => ('create'  == $action->id) ],
                ['label' => "商品を検索",  'url' => ['search','target'=>'product'], 'active' => ('product' == $target)],
                ['label' => "お客様を検索", 'url' => ['search','target'=>'customer'], 'active' => ('customer' == $target)],
            ],
              'options' => ['class' =>'nav-tabs alert-success'],
        ]);
    }

    private function loadProvider(\common\models\Pointing $model)
    {
        $query = \common\models\Pointing::find()
               ->andWhere(['seller_id' => $model->seller_id])
               ->andFilterWhere(['AND',
                ['company_id'          => $this->company->company_id],
                ['status'              => $model->status            ],
                ['like', 'point_given',   $model->point_given       ],
                ['like', 'point_consume', $model->point_consume     ],
               ])
               ->with('customer');

        return new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => ['pointing_id' => SORT_DESC],
            ],
        ]);
    }

    private static function loadProvider4Customer($key)
    {
        $query = \common\models\Customer::find()->where(['CONCAT(tel01,tel02,tel03)' => $key]);

        if(! $query->exists())
        {
            if(13 == strlen($key)) // if barcode is given
                $key = substr($key, 2, 10);

            $q2 = \common\models\Membercode::find()->where(['code' => $key]);

            if($q2->exists())
                $query = \common\models\Customer::find()->where(['customer_id' => $q2->select('customer_id') ]);


            return new \yii\data\ActiveDataProvider(['query'=> $query]);
        }
        $query = \common\models\Customer::find()->leftJoin(['f' => \common\models\CustomerFamily::tableName()], 'f.child_id=customer_id')
              ->andWhere(['CONCAT(tel01,tel02,tel03)' => $key])
              ->andWhere(['f.parent_id' => NULL]);
        return new \yii\data\ActiveDataProvider(['query'=> $query]);
    }

    //private static function loadProvider4Product(\frontend\models\SearchProduct $model)
    private static function loadProvider4Product(\frontend\models\SearchProductMaster $model)
    {
        $model->validate();

        $query = \common\models\ProductMaster::find()
               ->andWhere([
                   'category_id' => \common\models\Category::find()
                                 ->where(['seller_id' => $model->company])
                                 ->select('category_id')
               ])
               ->andFilterWhere([
                   'AND',
                   ['like','ean13',  $model->ean13 ],
                   ['like','name',  $model->name ],
                   ['like','price', $model->price],
                   ['like','kana',  $model->kana ],
               ]);

        return new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]);
    }

}
