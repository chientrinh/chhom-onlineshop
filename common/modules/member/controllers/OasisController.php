<?php

namespace common\modules\member\controllers;

/**
* $URL: https://localhost:44344/svn/MALL/common/modules/member/controllers/DefaultController.php $
* $Id: DefaultController.php 1729 2015-10-31 02:31:43Z mori $
*/

use Yii;
use \common\models\Product;
use \common\modules\member\models\Oasis;

class OasisController extends BaseController
{
    public function init()
    {
        parent::init();
    }

    public function actionIndex()
    {
        $params   = [];
        $provider = $this->loadProvider($params);
        return $this->render('index',[
            'dataProvider' => $provider,
        ]);
    }

    public function actionView($pid, $shipped = null)
    {
        $model = Oasis::findOne($pid);

        $query = $model->getCustomers($shipped);

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('view',[
            'shipped'      => $shipped,
            'model'        => $model,
            'dataProvider' => $provider,
        ]);
    }

    public function actionCsv($pid, $shipped = null)
    {
        $model    = Oasis::findOne($pid);
        $query    = $model->getCustomers($shipped)->select('*');
        $basename = sprintf('%s-%s.csv', $pid, date('Ymd_His'));
        $charset  = 'SJIS-WIN';
        $eol      = "\r\n";

        ini_set("memory_limit", "1G"); // total 32GB memory @ arnica.toyouke.com
        ini_set("max_execution_time", 60 * 30); // 30 min

        $inline = false;
        $mime   = 'text/csv';
        Yii::$app->response->setDownloadHeaders($basename, $mime, $inline);
        Yii::$app->response->send();

        Yii::setLogger(new \yii\log\Logger());
        // @see http://stackoverflow.com/questions/27420959/yii2-batchinsert-eats-all-server-memory

        // add TITLE in csv
        $label= $shipped ? '発送済み' : ((null===$shipped) ? '対象者 全員' : '未発送');
        $line = implode(',',[$model->name, $label, 'とらのこ正会員リスト', date('Y-m-d H:i:s 現在')]);
        $line = mb_convert_encoding($line, $charset, Yii::$app->charset) . $eol . $eol;
        echo $line;

        // draw CSV contents
        \common\widgets\CsvView::widget([
            'query'      => $query,
            'charset'    => $charset,
            'eol'        => $eol,
            'header'     => ["氏名", "〒", "住所", "配布日"],
            'attributes' => [
                'name',
                'zip',
                'addr',
                function($data)use($model)
                {
                    if($purchase = $model->getPurchase($data)->one())
                        return $purchase['create_date'];
                },
            ],
        ]);

        if(! $shipped && (null !== $shipped))
        {
            $query = $model->getCustomers(false)->select('*');

            foreach($query->batch() as $rows)
                foreach($rows as $customer)
                    $this->createPurchase($pid, $customer->customer_id);
        }

        return 'done';
    }

    public function actionMarkAsShipped($pid, $cid)
    {
        $user = Yii::$app->user->identity;
        if($user instanceof \common\models\Customer)
            $this->createPointing($pid, $cid, $user);
        else
            $this->createPurchase($pid, $cid);

        if(Yii::$app->request->isAjax)
            return 'ok';

        return $this->redirect(['toranoko/view','id'=>$cid]);
    }

    private function createPointing($pid, $cid, $user)
    {
        $model    = Oasis::findOne($pid);
        $customer = \common\models\Customer::findOne($cid);

        if(! $model || ! $customer)
            throw new \yii\web\NotFoundHttpException('パラメータが不正です');

        return $model->setPointing($customer, $user);
    }

    private function createPurchase($pid, $cid)
    {
        $model    = Oasis::findOne($pid);
        $customer = \common\models\Customer::findOne($cid);

        if(! $model || ! $customer)
            throw new \yii\web\NotFoundHttpException('パラメータが不正です');

        return $model->setPurchase($customer);
    }

    private function loadProvider($params)
    {
        $query = Oasis::find();

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder'=>['product_id'=>SORT_DESC]],
        ]);

        return $provider;
    }

}
