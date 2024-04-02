<?php

namespace common\modules\member\controllers;

/**
* $URL: https://tarax.toyouke.com/svn/MALL/common/modules/member/controllers/DefaultController.php $
* $Id: DefaultController.php 3082 2016-11-16 05:31:11Z mori $
*/

use Yii;
use common\models\Customer;
use common\models\Membercode;

/**
 * 豊受モール会員を新規作成する
 */
class DefaultController extends BaseController
{
    public function actionCreate($force=false)
    {
        $model = new Customer();
        $mcode = new Membercode();

        $mcode->load(Yii::$app->request->post());

        if(! $model->load(Yii::$app->request->post()))
            return $this->render('create',['model'=>$model,'mcode'=>$mcode]);

        elseif('zip2addr' == Yii::$app->request->post('scenario'))
        {
            $model->load(Yii::$app->request->post());
            $model->zip2addr();
        }
        elseif($model->validate())
        {
            if(0 < strlen($mcode->code))
            {
                $code = $mcode->code;
                if(13 == strlen($code))
                    $code = substr($code, 2, 10);

                $q = Membercode::find()->andWhere(['code'=> $code,
                                                   'pw'  => $mcode->pw   ])
                                       ->andWhere(['customer_id' => null,
                                                   'migrate_id'  => null ]);
                if($q->exists())
                    $mcode = $q->one();
                else
                    $mcode->addError('code',"会員証NOが存在しないか、仮パスワードが不一致です");
            }

            $query = Customer::find()->active()
                   ->andWhere(['CONCAT(tel01,tel02,tel03)' => $model->tel01
                                                             .$model->tel02
                                                             .$model->tel03]);
            if($query->exists() && (false === $force))
            {
                Yii::$app->session->addFlash('error',
                   "同じ電話番号での登録があります。<br>"
                   .implode(';',$query->select(['CONCAT(name01,name02)'])->column())
                );
            }
            elseif($model->validate() && ! $mcode->hasErrors())
            {
                if(false == $mcode->isNewRecord)
                    $model->detachBehavior('membercode');

                if($model->save())
                    if(false == $mcode->isNewRecord)
                        \common\components\CustomerMigration::attachMembercode($model, $mcode);

                Yii::$app->session->addFlash('success',"入会の申し込みが完了しました");
                return $this->redirect(['view','id'=>$model->customer_id]);
            }
        }

        return $this->render('create',['model'=>$model,'mcode'=>$mcode]);
    }
}
