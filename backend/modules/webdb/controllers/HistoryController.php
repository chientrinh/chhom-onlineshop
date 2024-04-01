<?php
namespace app\modules\webdb\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/webdb/controllers/HistoryController.php $
 * $Id: HistoryController.php 3258 2017-04-19 07:07:41Z kawai $
 */

use Yii;
use \common\models\webdb18\DenpyoItem;

// ログインチェックをbeforeActionで行っているBaseControllerを継承させる
class HistoryController extends \backend\controllers\BaseController
{
    public function actionIndex()
    {
        $model = new DenpyoItem();
        $model->load(Yii::$app->request->get());

        return $this->render('index',[
            'dataProvider' => $this->loadProvider($model),
            'searchModel'  => $model,
        ]);
    }

    public function actionView($code)
    {
        $model    = new DenpyoItem(['d_item_syohin_num' => $code]);
        $provider = $this->loadProvider($model);

        return $this->render('view', ['code'=>$code, 'dataProvider'=>$provider]);
    }

    private function loadProvider($model)
    {
        $query = DenpyoItem::find()
               ->where("d_item_date <> ''")
               ->andFilterWhere(['AND',
                   ['like','d_item_syohin_num',$model->d_item_syohin_num],
                   ['like','d_item_date',      $model->d_item_date],
                   ['like','d_item_syohin_num',$model->d_item_syohin_num],
                   ['like','d_item_1_syohin_name_hidden',$model->d_item_1_syohin_name_hidden],
                   ['d_item_std_tanka' => $model->d_item_std_tanka],
               ])
               ->with('denpyo.center');

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => ['d_item_date' => SORT_DESC],
                'enableMultiSort' => true,
            ],
        ]);

        return $provider;
    }

}
