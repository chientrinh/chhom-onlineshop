<?php

namespace frontend\controllers;

use Yii;
use common\models\Category;
use common\models\Company;
use common\models\CustomerGrade;
use common\models\Membership;
use common\models\ProductMaster;
use common\models\Remedy;
use common\models\RemedyPotency;
use common\models\RemedyStock;
use common\models\RemedyVial;
use common\models\SearchRemedy;
use yii\web\NotFoundHttpException;

/**
 * RemedyController implements the CRUD actions for Remedy model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/controllers/RemedyController.php $
 * $Id: RemedyController.php 4178 2019-08-26 08:02:50Z mori $
 */
class RemedyController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $company  = Company::findOne(Company::PKEY_HJ);
        $category = Category::findOne(Category::REMEDY);
        $this->view->params['breadcrumbs'][] = ['label'=> $company->name, 'url'=> ['/hj']  ];
        $this->view->params['breadcrumbs'][] = ['label'=> $category->name,'url'=> ['/remedy']];

        return true;
    }

    /**
     * Lists all Remedy models.
     * @return mixed
     */
    public function actionIndex($format='html',$pagination='true')
    {
        $provider = $this->loadProvider();

        if('true' !== $pagination)
            $provider->pagination = false;

        $customer    = Yii::$app->user->identity;
        $restrict_id = (int)($customer ? $customer->getAttribute('grade_id') : 0);
        $query = Remedy::find()->where(['<=', Remedy::tableName().'.restrict_id', $restrict_id]);
        $query->andWhere('0 < remedy_id');  // exclude 空レメディー
        // FE/MTのみしかないレメディーは一覧表示対象外とする
        $target = RemedyStock::find()->tinctureAndFlower(false)->all();
        $target_ids = Yii\helpers\ArrayHelper::map($target, 'remedy_id', 'remedy_id');
        $query->andWhere(['remedy_id' => $target_ids]);

        $model = new SearchRemedy([
            'on_sale'=>true,
            'remedy_id'=>$target_ids
        ]);

        return $this->render('index', [
            'format'       => 'html',
            'searchModel'  => $model,
            'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $query]),
        ]);
    }

    public function actionIndexof($firstLetter)
    {
        $key   = $firstLetter . '%';
        $query = Remedy::find()->where(['like','abbr',$key, false]);
        $query->andWhere('0 < remedy_id');  // exclude 空レメディー

        $customer    = Yii::$app->user->identity;
        $restrict_id = (int)($customer ? $customer->getAttribute('grade_id') : 0);
        $query->andWhere(['<=', Remedy::tableName().'.restrict_id', $restrict_id]);

        // FE/MTのみしかないレメディーは一覧表示対象外とする
        $target = RemedyStock::find()->tinctureAndFlower(false)->all();
        $target_ids = Yii\helpers\ArrayHelper::map($target, 'remedy_id', 'remedy_id');
        $query->andWhere(['remedy_id' => $target_ids]);

        $model = new SearchRemedy([
            'abbr'=>$firstLetter,
            'on_sale'=>true,
            'remedy_id'=>$target_ids
        ]);

        return $this->render('index', [
            'format'       => 'html',
            'searchModel'  => $model,
            'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $query]),
        ]);
    }

    public function actionPopular()
    {
        return $this->render('popular');
    }

    public function actionSearch()
    {
        $remedy = new \common\models\Remedy();
        $stock  = new \common\models\RemedyStock();

        $remedy->load(Yii::$app->request->get());
        $remedy->load(Yii::$app->request->post());

        $stock->load(Yii::$app->request->get());
        $stock->load(Yii::$app->request->post());

        return $this->render('search',[
            'remedy'=> $remedy,
            'stock' => $stock,
            'user' => Yii::$app->user->identity,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->redirect(['viewbyname', 'name'=>$model->abbr]);
    }

    public function actionViewbyname($name)
    {
        $model = $this->findModelByName($name);
        if($name != $model->abbr)
            return $this->redirect(['viewbyname','name'=>$model->abbr]);

        return $this->renderView($model);
    }

    /**
     * Displays images of a single Remedy model.
     * @param string $name
     * @param string $top
     * @return mixed
     */
    public function actionViewImage($id, $top=null)
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

        return $this->render('view-image', [
            'model'   => $model,
            'images'  => $images,
        ]);
    }

    /**
     * Displays a single Remedy model.
     * @param  $model Remedy
     * @return mixed
     */
    private function renderView($model)
    {
        if(Yii::$app->user->isGuest)
            $favorite = false;
        else
            $favorite = \common\models\CustomerFavorite::find()->where([
                'customer_id' => Yii::$app->user->id,
                'remedy_id'   => $model->remedy_id,
            ])->exists();

        $user  = Yii::$app->user->identity;
        $super = ! $user ? false :
                 $user->isMemberOf([Membership::PKEY_STUDENT_INTEGRATE     ,
                                    Membership::PKEY_STUDENT_TECH_COMMUTE  ,
                                    Membership::PKEY_STUDENT_TECH_ELECTRIC ,
                                    Membership::PKEY_AGENCY_HJ_A           ,
                                    Membership::PKEY_AGENCY_HJ_B           ,
                                    Membership::PKEY_HOMOEOPATH            ,
                                    Membership::PKEY_CENTER_HOMOEOPATH     ,]);
        if($super)
            $user->grade_id = CustomerGrade::PKEY_NA;

        $query = RemedyStock::find()->forcustomer($user)->andWhere(['remedy_id'=>$model->remedy_id]);

        if(! $query->exists())
            $products = [];
        else
            $products = $model->products;

        $images = [];
        $restrict_id = $user ? $user->grade_id : 0;
        foreach($products as $k => $product)
        {
            $master = ProductMaster::findOne(['ean13' => $product->barcode]);
            if($restrict_id < $master->restrict_id)
                unset($products[$k]);

            elseif(RemedyPotency::MT == $product->potency_id)
                unset($products[$k]);

            else
                foreach($product->images as $img)
                    $images[$img->img_id] = $img;

        }

        // レメディーの広告用説明
        $advertisement = [];
        $category_advertisement = [];
        $descriptions = [];
        if (! empty($products)) {
            $advertisement = end($products)->remedyAdDescription;
            $category_advertisement = end($products)->categoryAd;

            // レメディーの補足説明
            $descriptions['remedyDescriptions'] = end($products)->remedyDescriptions;
            // レメディーカテゴリー単位の補足説明
            $descriptions['categroyDescriptions'] = end($products)->categoryDescriptions;

        }

        \yii\helpers\ArrayHelper::multisort($images,'weight',SORT_DESC);

        return $this->render('view', [
            'isFavorite'    => $favorite,
            'title'         => $this->getTitle($model),
            'model'         => $model,
            'products'      => $products,
            'images'        => $images,
            'descriptions'  => $descriptions,
            'advertisement' => $advertisement,
            'category_advertisement' => $category_advertisement
        ]);
    }

    /**
     * Finds the Remedy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Remedy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Remedy::findOne($id);

        if(! $model || $model->isRestrictedTo(Yii::$app->user->identity) || $model->remedy_id == 0)
            throw new NotFoundHttpException('ご指定のページは見つかりません');

        return $model;
    }

    protected function findModelByName($abbr)
    {
        $model = Remedy::findOne(['abbr'=>$abbr]);
        if(! $model)
            $model = Remedy::find()->where(['like','abbr',$abbr.'%',false])->one();

        if(! $model || $model->isRestrictedTo(Yii::$app->user->identity) || $model->remedy_id == 0)
            throw new NotFoundHttpException('ご指定のページは見つかりません');

        return $model;
    }

    private static function loadProvider()
    {
        $query = Remedy::find()->andWhere('0 < remedy_id'); // exclude 空レメディー

        if(Yii::$app->user->isGuest)
            $query->andWhere(['restrict_id' => 0,'on_sale' => 1]);

        elseif($customer = Yii::$app->user->identity)
            if(! $customer->isAgencyOf(\common\models\Company::PKEY_HJ))
                $query->andWhere(['<=','restrict_id', $customer->grade_id]);

        return new \yii\data\ActiveDataProvider([
                'query' => $query,
        ]);
    }

    private function getTitle($model)
    {
        $title_query = ProductMaster::find()
                                  ->where(['remedy_id' => $model->remedy_id])
                                  // ->select(['potency_id', 'name'])
                                  ->vialRemedy()
                                  ->distinct()
                                  ->orderby(['vial_id' => SORT_ASC]);

        $potency = $title_query->select('potency_id')->column();
        $names   = $title_query->select('name')->column();

        // 対象件数が1件以上、且つ、potency_idが1の時、日本語名を返す
        if ($title_query->count() > 0 && array_shift($potency) == 1) {
            return $model->ja;
        }

        // それ以外の場合は、「略称＋日本語名」で表示する
        return $model->abbr .' '. $model->ja;
    }

}
