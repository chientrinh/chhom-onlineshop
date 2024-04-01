<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/webdb/controllers/CustomerController.php $
 * $Id: CustomerController.php 3258 2017-04-19 07:07:41Z kawai $
 */

namespace app\modules\webdb\controllers;

use Yii;
use yii\data\ArrayDataProvider;

// ログインチェックをbeforeActionで行っているBaseControllerを継承させる
class CustomerController extends \backend\controllers\BaseController
{
    public function actionView($id, $db)
    {
        $model = $this->loadModel($id,$db);

        return $this->render('view', ['db'=>$db, 'model'=>$model]);
    }

    public function actionIndex($db)
    {
        $searchModel  = new \backend\models\SearchCustomer();
        $searchModel->load(Yii::$app->request->get());
        $dataProvider = $this->loadProvider($db, $searchModel->keywords);

        return $this->render('index', [
            'db'          => $db,
            'dataProvider'=> $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    private function loadModel($id, $db)
    {
        $model = false;
        if('webdb18' == $db)
            $model = \common\models\webdb18\SearchCustomer::findOne($id);
        if('webdb20' == $db)
            $model = \common\models\webdb20\SearchCustomer::findOne($id);

        if(! $model)
            throw new \yii\web\NotFoundHttpException("当該のIDは見つかりません");

        return $model;
    }

    private function loadProvider($db, $keywords='')
    {
        if('webdb18' == $db)
            $query = \common\models\webdb18\Customer::find();
        if('webdb20' == $db)
            $query = \common\models\webdb20\Customer::find();

        if(! isset($query))
            throw new \yii\web\NotFoundHttpException("当該のDBは見つかりません");

        $query->leftJoin(['a'=>'tbladdress'],'a.customerid = tblcustomer.customerid')
              ->leftJoin(['o'=>'tbloffice' ],'o.customerid = tblcustomer.customerid')
              ->leftJoin(['s'=>'tblsomeadd'],'s.customerid = tblcustomer.customerid');

        if($keywords)
            $keywords = \common\components\Romaji2Kana::translate($keywords,'katakana');
        if(in_array($db, ['webdb18','webdb20']))
            $keywords = mb_convert_encoding($keywords, 'CP51932', 'UTF-8');

        if($items = explode(' ', $keywords))
        {
            foreach($items as $item)
            {
                if(preg_match('/^([0-9]{10})$/',$item, $match) ||
                   preg_match('/^28([0-9]{10})[0-9]$/',$item, $match)
                )
                    $nums[] = $match[1]; // 会員証NO 
            }
            if(isset($nums))
                $customerid = \common\models\Membercode::find()
                            ->select('migrate_id')
                            ->andWhere(['or',['code' => $nums]])
                            ->andWhere(['directive' => $db])
                            ->column();

            if(isset($customerid) && ! empty($customerid))
                $query->andWhere(['IN','tblcustomer.customerid',$customerid]);
            else
            foreach($items as $item)
                $query->andFilterWhere([
                    'or',
                    ['like','tblcustomer.name',  $item],
                    ['like','tblcustomer.kana',  $item],
                    ['like','tblcustomer.email', $item],
                    ['like','a.tel',             $item],
                    ['like','o.tel',             $item],
                    ['like','s.tel',             $item],
                    ['like','a.mobile',          $item],
                    ['like','a.postnum',         $item],
                    ['like','CAST(tblcustomer.customerid AS TEXT)',$item],
                ]);
        }

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]);

        return $provider;
    }
}
