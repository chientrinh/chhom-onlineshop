<?php

namespace backend\controllers;

use Yii;
use common\models\Category;
use common\models\CustomerGrade;
use common\models\Subcategory;
use common\models\ProductMaster;
use \common\models\ProductSubcategory;
use common\models\Campaign;
use common\models\CampaignDetail;
use backend\models\SearchCampaign;
use backend\models\SearchCampaignDetail;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use GuzzleHttp\Exception\RequestException;
use \yii\helpers\ArrayHelper;

/**
 * CampaignController implements the CRUD actions for Campaign model.
 */
class CampaignController extends BaseController
{
    /**
     * @var PDF_MERGER absolute path of `pdfunite`
     * Caution: this class is completely dependent on this executable, no warranty without it
     */
    const PDF_MERGER  = '/usr/bin/pdfunite';

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => 'キャンペーン', 'url' => ['index']];

        return true;
    }

    /**
     * Lists all Vegetable models.
     * @return mixed
     */
    public function actionIndex()
    {
        $streamings = ArrayHelper::map(\common\models\Streaming::find()->orderBy(['streaming_id' => SORT_DESC])->asArray()->All(), 'streaming_id', 'name');
        $shippings = [0 => "通常", 1 => "送料無料"];
        $preorders = [0 => "通常", 1 => "事前注文受付"];
        $branches =  ArrayHelper::map(\common\models\Branch::find()->All(), 'branch_id', 'name');

        $searchModel = new SearchCampaign();

        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'branches'    => $branches,
            'streamings'  => $streamings,
            'shippings' => $shippings,
            'preorders' => $preorders,
        ]);
    }

    /**
     * Displays a single Vegetable model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $target = 'viewCategory')
    {
        if (method_exists($this, $target))
            return $this->$target($id);

        throw new NotFoundHttpException("ページが存在しません。");        
    }


    public function viewCategory($id)
    {

        return $this->render('view', [
            'viewFile'     => 'view/_category',
            'campaign'     => $this->findModel($id),
            'dataProvider' => SearchCampaignDetail::searchCategories($id),
        ]);

    }

    public function viewSubCategory($id)
    {
        return $this->render('view', [
            'viewFile'     => 'view/_subcategory',
            'campaign'     => $this->findModel($id),
            'dataProvider' => SearchCampaignDetail::searchSubCategories($id),
        ]);

    }

    public function viewProduct($id)
    {
        return $this->render('view', [
            'viewFile'     => 'view/_product',
            'campaign'     => $this->findModel($id),
            'dataProvider' => SearchCampaignDetail::searchProducts($id),
        ]);

    }

    public function actionChangestatus($id)
    {
        $campaign = $this->findModel($id);
        // 現在のステータス値と異なる値を設定する（現在が0の場合は1を、1の場合は0を設定する。ステータスが増えた場合は要改修）
        $campaign->status = (int) ! $campaign->isActiveOnlyStatus(); 

        if (!$campaign->save())
            throw new NotFoundHttpException("有効/無効の更新に失敗しました。初めからやり直して下さい。");

        return $this->redirect(['view', 'id' => $campaign->campaign_id, 'target' => Yii::$app->request->get('target', 'viewCategory')]);
    }

    /**
     * Creates a new Vegetable model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $campaign = new Campaign();
        if($campaign->load(Yii::$app->request->post()) && $campaign->save())
            return $this->redirect(['view', 'id' => $campaign->campaign_id]);

        $unselected = ['' => ''];
        $streamings = $unselected + ArrayHelper::map(\common\models\Streaming::find()->orderBy(['streaming_id' => SORT_DESC])->asArray()->All(), 'streaming_id', 'name');
    
        return $this->render('create', [
            'campaign' => $campaign,
            'streamings' => $streamings
        ]);
    }

    /**
     * Updates an existing Campaign model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $target)
    {
        $campaign = $this->findModel($id);
        if($campaign->load(Yii::$app->request->post()) && $campaign->save())
            return $this->redirect(['view', 'id' => $campaign->campaign_id]);

        $unselected = ['' => ''];
        $streamings = $unselected + ArrayHelper::map(\common\models\Streaming::find()->orderBy(['streaming_id' => SORT_DESC])->asArray()->All(), 'streaming_id', 'name');
    
        return $this->render('update', [
            'campaign' => $campaign,
            'streamings' => $streamings
        ]);   
    }

    /**
     * Deletes an existing Vegetable model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $campaign = $this->findModel($id);
        CampaignDetail::deleteAll(['campaign_id' => $campaign->campaign_id]);
        if($campaign->delete())
            return $this->redirect(['index']);
        
        Yii::$app->session->addFlash('error', "{$campaign->campaign_name}を削除できません、システム担当者へ連絡してください");
        return $this->redirect(['view', 'id' => $campaign->campaign_id]);
    }

    public function actionAdd($id, $target)
    {
        $campaign = $this->findModel($id);

        if ($target == 'category')
            return $this->addCategory($campaign);

        if ($target == 'subcategory')
            return $this->addSubCategory($campaign);

        if ($target == 'product')
            return $this->addProduct($campaign);
    }

    private function addCategory($campaign) 
    {
        $campaignDetails = new CampaignDetail(['campaign_id' => $campaign->campaign_id]);
        if($campaignDetails->load(Yii::$app->request->post()) && $campaignDetails->save()) 
            $campaignDetails = new CampaignDetail(['campaign_id' => $campaign->campaign_id]);

        return $this->render('_form-category', [
                'campaign'        => $campaign,
                'campaignDetails' => $campaignDetails,
                'categories'      => Category::find()->all(),
                'grades'          => CustomerGrade::find()->all(),
            ]);

    }

    private function addSubCategory($campaign) 
    {
        $campaignDetails = new CampaignDetail(['campaign_id' => $campaign->campaign_id]);

        if($campaignDetails->load(Yii::$app->request->post()) && $campaignDetails->save())
            $campaignDetails = new CampaignDetail(['campaign_id' => $campaign->campaign_id]);


        return $this->render('_form-subcategory', [
                'campaign'        => $campaign,
                'campaignDetails' => $campaignDetails,
                'subcategories'   => Subcategory::find()->all(),
                'grades' => CustomerGrade::find()->all(),
            ]);

    }

    private function addProduct($campaign) 
    {
        $campaignDetails = new CampaignDetail(['campaign_id' => $campaign->campaign_id]);

        if($campaignDetails->load(Yii::$app->request->post()) && $campaignDetails->save())
            $campaignDetails = new CampaignDetail(['campaign_id' => $campaign->campaign_id]);

        return $this->render('_form-product', [
                'campaign'        => $campaign,
                'campaignDetails' => $campaignDetails,
                'categories'      => Category::find()->all(),
                'subcategories'   => Subcategory::find()->all(),
                'grades' => CustomerGrade::find()->all(),
            ]);

    }
 
    public function actionSearch()
    {
        $params         = Yii::$app->request->get();
        $options        = null;
        $products       = [];
        $productArray   = [];
        $target         = ArrayHelper::getValue($params, 'target', null);
        $category_id    = ArrayHelper::getValue($params, 'category_id', null);
        $subcategory_id = ArrayHelper::getValue($params, 'subcategory_id', null);
        $grade_id = ArrayHelper::getValue($params, 'grade_id', null);
        if((null === $target) || (null === $category_id && null === $subcategory_id))
            return false;

        if ($category_id)
            $products = ProductMaster::find()
                                    ->andWhere(['category_id' => $category_id])
                                    ->all();

        if ($subcategory_id)
            $products = ProductMaster::find()
                            ->leftJoin(['sp' => ProductSubcategory::tableName()], ProductMaster::tableName(). '.ean13=sp.ean13')
                            ->andWhere(['sp.subcategory_id' => $subcategory_id])
                            ->all();     

        if ($products) {
            foreach ($products as $key => $product){
//                $productArray[$product->ean13] = $product->name. '&nbsp;&nbsp;('. $product->ean13. ')'
                  $productArray[$product->ean13] = $product->ean13.'&nbsp;&nbsp;'.$product->name;
            }


            $options = ArrayHelper::merge(
                                [0 => '未選択'], 
                                $productArray
                            );            

        }
        if(Yii::$app->request->isAjax)
            return \yii\helpers\Json::encode($options);

        return $options;
    }

    public function actionEdit($id, $target)
    {
        $campaign = $this->findModel($id);

        if ($target == 'category')
            return $this->updateCategory($campaign);

        if ($target == 'subcategory')
            return $this->updateSubCategory($campaign);

        if ($target == 'product')
            return $this->updateProduct($campaign);

        throw new \yii\web\NotFoundHttpException("$target unknown");
    }

    private function updateCategory($campaign) 
    {
        $get = Yii::$app->request->get();


        if(isset($get['grade_id'])) {
           $campaignDetails = CampaignDetail::find()
                            ->andWhere(['campaign_id' => $campaign->campaign_id])
                            ->andWhere(['category_id' => $get['category_id']])
                            ->andWhere(['grade_id' => $get['grade_id']])->one();
        } else {
           $campaignDetails = CampaignDetail::find()
                            ->andWhere(['campaign_id' => $campaign->campaign_id])
                            ->andWhere(['category_id' => $get['category_id']])->one();

        }
        if(! $campaignDetails)
            throw new NotFoundHttpException("対象のデータが見つかりません。初めからやり直してください。");

        if($campaignDetails->load(Yii::$app->request->post()) && $campaignDetails->save())
            return $this->redirect(['view', 'id' => $campaignDetails->campaign_id, 'target'=>'viewCategory']);

        return $this->render('_form-category', [
                'campaign'   => $campaign,
                'campaignDetails' => $campaignDetails,
                'categories' => Category::find()->all(),
                'grades' => CustomerGrade::find()->all(),
            ]);

    }

    private function updateSubCategory($campaign) 
    {
        
        $get = Yii::$app->request->get();
       
 
        if(isset($get['grade_id'])) {
            $campaignDetails = CampaignDetail::find()
                            ->andWhere(['campaign_id' => $campaign->campaign_id])
                            ->andWhere(['subcategory_id' => $get['subcategory_id']])
                            ->andWhere(['grade_id' => $get['grade_id']])->one();

        } else {
            $campaignDetails = CampaignDetail::find()
                            ->andWhere(['campaign_id' => $campaign->campaign_id])
                            ->andWhere(['subcategory_id' => $get['subcategory_id']])->one();
        }
 

        if(! $campaignDetails)
            throw new NotFoundHttpException("対象のデータが見つかりません。初めからやり直してください。");

        if($campaignDetails->load(Yii::$app->request->post()) && $campaignDetails->save())
            return $this->redirect(['view', 'id' => $campaignDetails->campaign_id, 'target'=>'viewSubCategory']);

        return $this->render('_form-subcategory', [
                'campaign'   => $campaign,
                'campaignDetails' => $campaignDetails,
                'subcategories' => Subcategory::find()->all(),
                'grades' => CustomerGrade::find()->all(),
            ]);

    }

    private function updateProduct($campaign) 
    {
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post('CampaignDetail');

        if(isset($get['grade_id'])) {
            $campaignDetails = CampaignDetail::find()
                            ->andWhere(['campaign_id' => $campaign->campaign_id])
                            ->andWhere(['grade_id' => $get['grade_id']])->one();
        } else {
            $campaignDetails = CampaignDetail::find()
                            ->andWhere(['campaign_id' => $campaign->campaign_id])
                            ->andWhere(['ean13' => $get['ean13']])->one();
        }

        if(! $campaignDetails)
            throw new NotFoundHttpException("対象のデータが見つかりません。初めからやり直してください。");

        if($campaignDetails->load(Yii::$app->request->post()) && $campaignDetails->save())
            return $this->redirect(['view', 'id' => $campaignDetails->campaign_id, 'target'=>'viewProduct']);

        return $this->render('_form-product', [
                'campaign'        => $campaign,
                'campaignDetails' => $campaignDetails,
                'categories'      => Category::find()->all(),
                'subcategories'   => Subcategory::find()->all(),
                'grades' => CustomerGrade::find()->all(),
            ]);

    }

    public function actionDel($id, $target)
    {
        $campaign = $this->findModel($id);
        $get = Yii::$app->request->get();

        if ($target == 'category') {
            $keyName  = 'category_id';
            $viewName = 'viewCategory';
        }

        elseif ($target == 'subcategory') {
            $keyName  = 'subcategory_id';
            $viewName = 'viewSubCategory';
        }

        elseif ($target == 'product') {
            $keyName  = 'ean13';
            $viewName = 'viewProduct';
        }

        else 
            throw new \yii\web\NotFoundHttpException("$target unknown");


        if(isset($get['grade_id'])) {
            $campaignDetails = CampaignDetail::find()
                            ->andWhere(['campaign_id' => $campaign->campaign_id])
                            ->andWhere([$keyName => Yii::$app->request->get($keyName)])
                            ->andWhere(['grade_id' => $get['grade_id']])
                            ->one();

        } else {
            $campaignDetails = CampaignDetail::find()
                            ->andWhere(['campaign_id' => $campaign->campaign_id])
                            ->andWhere([$keyName => Yii::$app->request->get($keyName)])
                            ->one();

        }

        if(! $campaignDetails)
            throw new NotFoundHttpException("対象のデータが見つかりません。初めからやり直してください。");

        $campaignDetails->delete();
        
        return $this->redirect(['view', 'id' => $campaign->campaign_id, 'target' => $viewName]);
        //return $this->$viewName($id);
        //return $this->render('view', [$id, $viewName]);
    }

    /**
     * Finds the Vegetable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Vegetable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $campaign = Campaign::findOne($id);

        if(! $campaign)
            throw new NotFoundHttpException("当該IDは見つかりません({$id})");

        return $campaign;
    }
}
