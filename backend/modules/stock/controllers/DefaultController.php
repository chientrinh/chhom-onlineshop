<?php

namespace backend\modules\stock\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/stock/controllers/DefaultController.php $
 * $Id: DefaultController.php 821 2015-03-25 05:22:56Z mori $
 */

class DefaultController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $potency_LM = \common\models\RemedyPotency::find()
            ->andFilterWhere(['like', 'name', 'LM'])
                    ->all();

        $potency_id = \yii\helpers\ArrayHelper::map(
            \common\models\RemedyPotency::find()
            ->andFilterWhere(['like', 'name', 'LM'])
            ->all(),
            'potency_id', 'potency_id');

        // $query = \common\models\RemedyStock::find()->where(['in','potency_id', $potency_LM]);

        // $provider = new \yii\data\ActiveDataProvider([
        //     'query' => $query,
        //     'pagination'=>[
        //         'pageSize' => 20,
        //     ],
        // ]);

        $query = new \yii\db\Query;
        $remedy_id = $query->select('remedy_id')
                   ->distinct(true)
                   ->from('mtb_remedy_stock')
                   ->where(['in','potency_id', array_values($potency_id)])
                   ->createCommand()
                   ->queryColumn();

        $provider = new \yii\data\ActiveDataProvider([
            'query' => \common\models\Remedy::find()->where(['in','remedy_id',$remedy_id]),
            'pagination'=> [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index',['provider'=>$provider, 'potencies'=>$potency_LM]);
    }
}
