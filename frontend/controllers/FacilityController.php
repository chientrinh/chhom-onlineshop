<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\Facility;
use common\models\Pref;
use common\models\Zip;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/controllers/FacilityController.php $
 * $Id: FacilityController.php 4212 2020-01-09 15:20:42Z mori $
 *
 */
class FacilityController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'提携施設','url'=>['index']];

        return true;
    }

    public function actionIndex($id=null)
    {
        $query = Facility::find()->where('private = 0')
                                 ->orderBy(['addr01' => SORT_ASC]); // 県内住所昇順
//                                 ->orderBy(['update_date' => SORT_DESC]); // 新着順

        $query->innerJoinWith([
                    'memberships' => function($query){
                        $query->active();
                    }
        ]);

        $model = new Facility();
        if(Yii::$app->request->isGet) {
            if(null === $id && count(Yii::$app->request->get()) == 0)
                 $query->andWhere('1 = 0'); // forbid to search
            else
                $query->andFilterWhere(['pref_id' => $id]);

            if($model->load(Yii::$app->request->get()))
                foreach($model->getDirtyAttributes() as $attr => $value)
                    $query->andFilterWhere(['like', $attr, $value]);


            if($mid = Yii::$app->request->get('membership_id'))
           //     $query->innerJoinWith([
           //         'memberships' => function($query)use($mid){
                        $query->andFilterWhere(['membership_id' => $mid]);
           //         }
           //     ]);

        }

        if(Yii::$app->request->isPost)
        {
            $post = Yii::$app->request->post();
            if($model->load(Yii::$app->request->post()))
                foreach($model->getDirtyAttributes() as $attr => $value)
                    $query->andFilterWhere(['like', $attr, $value]);

            if($mid = Yii::$app->request->post('membership_id'))
        //        $query->innerJoinWith([
        //            'memberships' => function($query)use($mid){
                        $query->andFilterWhere(['membership_id' => $mid]);
        //            }
        //        ]);

            //地域（都道府県）選択により地域絞り込みを行う
            if(isset($post['pref']))
                $id = $post['pref'];
                if($id > 0)
                    $query->andFilterWhere(['pref_id' => $id]);
        }

        return $this->render('index',[
            'model'         => $model,
            'query'         => $query,
            'pref_id'       => $id,
            'membership_id' => isset($mid) ? $mid : [],
        ]);
    }

    public function actionView($id)
    {
        return $this->actionIndex($id);
    }

}
