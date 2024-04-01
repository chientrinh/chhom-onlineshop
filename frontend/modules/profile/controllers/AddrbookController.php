<?php

namespace frontend\modules\profile\controllers;
use Yii;

use \common\models\CustomerAddrbook;

/**
 * CRUD for dtb_customer_addrbook
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/controllers/AddrbookController.php $
 * $Id: AddrbookController.php 3970 2018-07-13 08:46:33Z mori $
 */

class AddrbookController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => "住所録",'url'=>['index']];

        return true;
    }

    /**
     * display customer's profile 
     */
    public function actionIndex()
    {
        $searchModel  = new \common\models\SearchCustomerAddrbook();
        $searchModel->customer_id = $this->customer->customer_id;

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'customer'     => $this->customer,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * display customer's addrbook instance
     */
    public function actionView($id)
    {
        throw new \yii\web\NotFoundHttpException();
    }

    /**
     * create customer's new addrbook
     */
    public function actionCreate()
    {
        $model = new CustomerAddrbook();
        $model->customer_id = $this->customer->customer_id;
        if($model::SCENARIO_ZIP2ADDR == Yii::$app->request->post('scenario'))
        {
            $model->load(Yii::$app->request->post());
            $model->scenario = $model::SCENARIO_ZIP2ADDR;
            $candidates = $model->zip2addr();
        }
        elseif($model::SCENARIO_CODE == Yii::$app->request->post('scenario'))
        {
            $model->load(Yii::$app->request->post());
            $model->scenario = $model::SCENARIO_CODE;
            $direct_customer = $model->code2addr();            
            if($direct_customer)
                Yii::$app->session->addFlash('success','会員証：'.$direct_customer->code.' から会員情報を自動入力しました');
        }
        elseif($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['index']);

        // fail, or no input
        return $this->render('create', [
            'model'      => $model,
            'candidates' => isset($candidates) ? $candidates : null,
            'direct_flg' => (isset($model->code) && strlen($model->code) > 0) ? 1 : 0,
            'direct_customer' => isset($direct_customer) ? $direct_customer : null,
        ]);
    }

    /**
     * update customer's addrbook instance
     */
    public function actionUpdate($id)
    {
        $scenario = null;

        $post = Yii::$app->request->post();
        $model = CustomerAddrbook::findOne([
            'customer_id'=> $this->customer->customer_id,
            'id'         => $id,
        ]);
        if(! $model)
            throw new \yii\web\BadRequestHttpException("当該の住所録は見当たりません");

        if($post) {

            if(isset($post['scenario']))
                $scenario = $post['scenario'];

            if(!isset($post['direct_flg'])) {
                $post['CustomerAddrbook']['code'] = "";
            }

            if(isset($post['direct_flg'])) {
                $model->scenario = $model::SCENARIO_CODE;
            }

            if($model::SCENARIO_ZIP2ADDR == $scenario)
            {
                $model->load($post);
                $model->scenario = $model::SCENARIO_ZIP2ADDR;
                $candidates = $model->zip2addr();
            }
            elseif($model::SCENARIO_CODE == $scenario)
            {
                $model->load($post);
                $direct_customer = $model->code2addr();
                if($direct_customer && $direct_customer->customer_id != $this->customer->customer_id)
                    Yii::$app->session->addFlash('success','会員証：'.$direct_customer->code.' から会員情報を自動入力しました');
            }
            elseif($model->load($post)) {
                if(strlen($model->code) == 0 || $model->code2addr())
                    if($model->save())
                        return $this->redirect(['index']);
            }
        }

        if($model->code)
            $direct_customer = $model->code2addr();
        // fail, or no update
        return $this->render('update', [
            'model'      => $model,
            'candidates' => isset($candidates) ? $candidates : null,
            'direct_flg' => (isset($model->code) && strlen($model->code) > 0) ? 1 : 0,
            'direct_customer' => isset($direct_customer) ? $direct_customer : null,
        ]);
    }

    /**
     * delete a addrbook
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->post('id');
        $model = CustomerAddrbook::findOne([
            'customer_id'=> $this->customer->customer_id,
            'id'         => $id,
        ]);

        if(! $model) // might be a crack!!
        {
            Yii::error("wrong request for delete Addrbook, possibly access violation attempted.");
            // TODO
            // trigger special error_log (dump http request detail, such as ip, user->id, timestamp, etc)

            throw new \yii\web\NotFoundHttpException();
        }
        
        if($model->delete()) // success
        {
            Yii::$app->getSession()->addFlash('success', "住所録から 1 件削除されました");
            return $this->redirect('index');
        }
        else // fail
        {
            Yii::$app->getSession()->addFlash('error', "削除できませんでした");
            Yii::error(sprintf("internal error, dtb_customer_addrbook->delete(%d) failed upon customer's request", $id) );
        }

        return $this->render('view', [ 'id' => $id ]);
    }

}
