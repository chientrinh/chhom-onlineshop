<?php
namespace frontend\modules\profile\controllers;

use Yii;

/**
 * CRUD for dtb_customer_addrbook
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/controllers/FavoriteController.php $
 * $Id: FavoriteController.php 3970 2018-07-13 08:46:33Z mori $
 */

class FavoriteController extends BaseController 
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $rule = [
            'actions'=> ['add','delete'],
            'allow'  => true,
            'roles'  => ['@'], // allow authenticated users
            'verbs'  => ['GET','POST'],
        ];

        // append a rule on top
        array_unshift($behaviors['access']['rules'], $rule);

        return $behaviors;
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => "お気に入り",'url'=>['index']];

        return true;
    }

    /**
     * display customer's favorites
     */
    public function actionIndex()
    {
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \common\models\CustomerFavorite::find()->where(['customer_id' => Yii::$app->user->id]),
        ]);

        return $this->render('index', [
            'customer'     => $this->customer,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * not implemented
     */
    public function actionView($id)
    {
        throw new \yii\web\NotFoundHttpException();
    }

    /**
     * not implemented
     */
    public function actionCreate()
    {
        throw new \yii\web\NotFoundHttpException();
    }

    /**
     * not implemented
     */
    public function actionUpdate($id)
    {
        throw new \yii\web\NotFoundHttpException();
    }

    /**
     * delete a addrbook
     */
    public function actionDelete($id=null,$rid=null)
    {
        $condition = ['customer_id' => $this->customer->customer_id];

        if($id)
            $condition['product_id'] = $id;
        elseif($rid)
            $condition['remedy_id'] = $rid;
        else
            throw new \yii\web\NotFoundHttpException();

        $model = \common\models\CustomerFavorite::find()->where($condition)->one();
        if(! $model) // might be a crack!!
        {
            Yii::error("wrong request for delete CustomerFavorite, possibly access violation attempted.");
            // TODO
            // trigger special error_log (dump http request detail, such as ip, user->id, timestamp, etc)

            throw new \yii\web\NotFoundHttpException();
        }
        
        if($model->delete()) // success
        {
            Yii::$app->getSession()->addFlash('success', "お気に入りから 1 件削除されました");
            return $this->redirect('index');
        }
        else // fail
        {
            Yii::$app->getSession()->addFlash('error', "削除できませんでした");
            Yii::error(sprintf("internal error, dtb_customer_favorite->delete(%d) failed upon customer's request", $id) );
        }

        return $this->render('index');
    }

    /**
     * add a favorite
     */
    public function actionAdd($id=null,$rid=null)
    {
        $model = new \common\models\CustomerFavorite();
        $model->customer_id = $this->customer->customer_id;

        if($id)
        {
            $model->product_id = $id;
            $model->scenario = $model::SCENARIO_PRODUCT;
        }
        elseif($rid)
        {
            $model->remedy_id  = $rid;
            $model->scenario = $model::SCENARIO_REMEDY;
        }
        else
            throw new \yii\web\NotFoundHttpException();

        if($model->save()) // success
        {
            Yii::$app->getSession()->addFlash('success',
                                              "お気に入りに追加しました<br>"
                                              . \yii\helpers\Html::a("お気に入りを見る", ['/profile/favorite']));
        }
        else // fail
        {
            Yii::$app->getSession()->addFlash('error', "追加できませんでした: ".array_shift(array_values($model->getfirstErrors())));
            Yii::error(sprintf("internal error, dtb_customer_favorite->insert(%d) failed upon customer's request", $id) );
        }

        $backUrl = Yii::$app->request->referrer;
        return $this->redirect($backUrl ? $backUrl : 'index');
    }

}
