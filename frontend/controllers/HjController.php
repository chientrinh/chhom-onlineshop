<?php
namespace frontend\controllers;
use Yii;
use \common\models\ProductMaster;
use \common\models\Remedy;
use \common\models\RemedyPotency;
use \common\models\RemedyStock;
use yii\helpers\ArrayHelper;

/**
 * Site controller
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/controllers/HjController.php $
 * $Id: HjController.php 4093 2018-12-27 05:00:55Z kawai $
 */
class HjController extends CompanyController
{
    public function actionTincture($name)
    {
        $condition = [
            'potency_id' => [RemedyPotency::MT,
                             RemedyPotency::COMBINATION,
                             RemedyPotency::JM
                ]
        ];
        $remedy = Remedy::findOne(['abbr' => $name]);

        if(!$remedy ||
       	   ($remedy->isRestrictedTo(Yii::$app->user->identity)) ||
           (! $stocks = RemedyStock::find()->where(['remedy_id' =>$remedy->remedy_id])
                                           ->andWhere($condition)
                                           ->all()) ||
           (! $names = ProductMaster::find()->where(['remedy_id'=>$remedy->remedy_id])
                                            ->andWhere($condition)
                                            ->select(['name', 'vial_id'])
                                            ->distinct()
                                            ->all())
        )
            throw new \yii\web\NotFoundHttpException("ページが見つかりません");

        $title = "";
        $vials = ArrayHelper::getColumn($names, 'vial_id');
        $names = ArrayHelper::map($names, 'vial_id','name');
        $vid = Yii::$app->request->get("vid");
        if($vid && isset($names[$vid])) {
            $name1 = $names[$vid];
        } else {
            $name1 = array_shift($names);
        }
        $vials = \common\models\RemedyVial::find()->where(['in', 'vial_id', $vials])->select('name')->column();

        foreach($vials as $vial) {
             if(strpos($name1, $vial) !== false) {
                 $name1 = str_replace($vial, "", $name1);
                 break;
             }
        }

        $buf  = preg_split('//u', $name1,-1, PREG_SPLIT_NO_EMPTY);
        foreach($buf as $k => $char)
        {
           if($char == "大" || $char == "小")
           {
               break;
           }
           else
           {
               $title .= $char;
           }
        }

        // レメディーの広告用説明
        $advertisement = end($stocks)->remedyAdDescription;
        $category_advertisement = end($stocks)->categoryAd;

        // レメディーの補足説明
        $descriptions['remedyDescriptions'] = end($stocks)->remedyDescriptions;    // レメディー商品単位の補足説明
        // レメディーカテゴリー単位の補足説明
        $descriptions['categroyDescriptions'] = end($stocks)->categoryDescriptions; // レメディーカテゴリー単位の補足説明

        return $this->render('tincture',
                            [
                            'title'         => $title,
                            'model'         => $remedy,
                            'stocks'        => $stocks,
                            'advertisement' => $advertisement,
                            'descriptions'  => $descriptions,
                            'category_advertisement' => $category_advertisement
        ]);
    }
}
