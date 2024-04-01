<?php

namespace backend\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/PointingController.php $
 * $Id: PointingController.php 3830 2018-02-02 08:18:55Z kawai $
 */

use Yii;
use \common\models\Pointing;
use \common\models\Customer;
use common\models\Company;
use \common\models\Membercode;

class PointingController extends BaseController
{
    public function beforeAction($action)
    {
        $this->view->title = "ポイント付与";

        $this->view->params['breadcrumbs'][] = ['label' => $this->view->title, 'url' => ['index']];

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $searchModel  = new \common\models\SearchPointing([
            'seller_id'   => Yii::$app->request->get('seller'),
            'customer_id' => Yii::$app->request->get('customer'),
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Create a new Pointing model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($customer_id = null, $branch_id = null)
    {
        if ($branch_id) {
            $branch = \common\models\Branch::findOne($branch_id);
        }

        $display_customer_id = null;
        $parent_flg = false;
        if ($customer_id) {
            $is_parent = Customer::findOne($customer_id);
            $display_customer_id = ($is_parent->parent) ? $is_parent->parent->customer_id : $is_parent->customer_id;
            $parent_flg = ($is_parent->parent) ? true : false;
        }

        $seller = Customer::find()->where(['name01' => 'ポイント', 'name02' => '付与'])->one();
        $model = new Pointing([
            'staff_id'     => Yii::$app->user->identity->staff_id,
            'customer_id'  => $display_customer_id,
            'seller_id'    => $seller->customer_id, // ポイント付与顧客固定
            'total_charge' => 0,
            'status'       => Pointing::STATUS_SOLD,
            'company_id'   => ($branch_id) ? $branch->company_id : ''
        ]);

        // 保存後はレシート発行
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['pointreceipt', 'id' => $model->pointing_id]);
        }

        return $this->render('create', [
            'model' => $model,
            'branch_id' => $branch_id,
            'parent_flg' => $parent_flg
        ]);
    }

    /*
     * バーコードから顧客情報取得
     */
    public function actionApply($target = '', $branch_id = null)
    {
        if('barcode' == $target) {
            $customer_id = $this->applyCustomer(Yii::$app->request->get('barcode'));
        } else {
            throw new \yii\web\NotFoundHttpException("{$target} unknown");
        }
        return $this->redirect(["create?customer_id={$customer_id}&branch_id={$branch_id}"]);
    }

    /**
     * get customer info
     * @param type $id customer_id or membercode
     * @return type string $customer_id
     */
    public function applyCustomer($id)
    {
        $finder = new \common\components\ean13\ModelFinder(['barcode' => $id]);

        $model = $finder->getOne();
        if($model && $model instanceof Customer)
            return $model->customer_id;
        

        return $this->redirect(['create']);
    }

    /**
     * レシート作成
     * @param type $id
     * @return type
     */
    public function actionReceipt($id)
    {
        $html  = \common\widgets\doc\pointing\Receipt::widget([
            'model' => $this->findModel($id),
        ]);

        $this->layout = '/none';
        return $this->renderContent($html);
    }

    /**
     * ポイント付与後のレシート作成
     * @param type $id
     * @return type
     */
    public function actionPointreceipt($id)
    {
        $html  = \common\widgets\doc\pointing\PointingReceipt::widget([
            'model' => $this->findModel($id),
        ]);

        $this->layout = '/none';
        return $this->renderContent($html);
    }

    public function actionExpire($id)
    {
        $model = $this->findModel($id);

        $model->expire();

        return $this->redirect(['view', 'id' => $id]);
    }

    protected static function findModel($id)
    {
        $model = Pointing::findOne($id);

        if($model->isExpired())
            self::addFlash('error', "この伝票は無効です");

        return $model;
    }

    private static function addFlash($key, $value)
    {
        Yii::$app->session->addFlash($key, $value);
    }
}
