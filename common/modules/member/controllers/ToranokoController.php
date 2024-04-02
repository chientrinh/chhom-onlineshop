<?php

namespace common\modules\member\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/member/controllers/ToranokoController.php $
 * $Id: ToranokoController.php 3088 2016-11-18 02:16:28Z mori $
 */

use Yii;
use \yii\helpers\Html;
use \backend\models\Staff;
use \common\models\Branch;
use \common\models\Customer;
use \common\models\Membercode;
use \common\models\Membership;
use \common\models\Payment;
use \common\modules\member\models\ToranokoApplicationForm;
use \common\modules\member\models\CreateForm;
use \common\modules\member\models\UpdateForm;

class ToranokoController extends BaseController
{
    /**
     * とらのこ会員を申し込む
     * Customerを追加し、さらにPurchaseを（あるいはさらにPoingingとCommissionを）追加する
     */
    public function actionCreate($force=false)
    {
        $model = new CreateForm([
            'scenario'      => \yii\db\ActiveRecord::SCENARIO_DEFAULT,
            'membership_id' => Membership::PKEY_TORANOKO_GENERIC,
        ]);

        $mcode = new Membercode();
        if($mcode->load(Yii::$app->request->post()))
        {
            $code = $mcode->code = trim($mcode->code);
            if(13 == strlen($code))
                $code = substr($code, 2, 10);

            $q = Membercode::find()->andWhere(['code'=> $code,
                                               'pw'  => $mcode->pw   ])
                                   ->andWhere(['customer_id' => null,
                                               'migrate_id'  => null ]);
            if(0 == strlen($code))
                ; // skip
            elseif($q->exists())
                $mcode = $q->one();
            else
                $mcode->addError('code',"会員証NOが存在しないか、仮パスワードが不一致です");
        }

        if(! $model->load(Yii::$app->request->post()))
            return $this->render('create',['model'=>$model,'mcode'=>$mcode]);

        elseif('zip2addr' == Yii::$app->request->post('scenario'))
        {
            $model->load(Yii::$app->request->post());
            $model->zip2addr();
        }
        elseif($model->validate() && ! $mcode->hasErrors())
        {
            $payment_id = Yii::$app->request->post('payment_id', Payment::PKEY_CASH);
            $shipped    = (Payment::PKEY_CASH == $payment_id) ? true : false;
            $paid       = (Payment::PKEY_CASH == $payment_id) ? true : false;
            $user       = Yii::$app->user->identity;
            $seller     = $user instanceof Customer ? $user : null;

            $form = new ToranokoApplicationForm([
                'customer'   => $model,
                'membercode' => $mcode->isNewRecord ? null : $mcode->code,
                'seller'     => $seller,
                'branch'     => Branch::findOne(Branch::PKEY_HE_TORANOKO),
                'product_id' => $model->product_id,
                'shipped'    => $shipped,
                'paid'       => $paid,
                'payment_id' => $payment_id,
                'pointBack'  => ($user instanceof Staff) ? 0 : $this->module->pointBack[$model->product_id],
            ]);

            if($form->validate())
            {
                if(0 < strlen($form->customer->tel))
                {
                    $query = Customer::find()->active()
                           ->andWhere(['CONCAT(tel01,tel02,tel03)' => $model->tel01
                                                                     .$model->tel02
                                                                     .$model->tel03]);
                    if($query->exists() &&
                      (false === $force)
                    )
                        Yii::$app->session->addFlash('error',
                            "同じ電話番号での登録があります。<br>"
                           .implode(';',$query->select(['CONCAT(name01,name02)'])->column())
                        );
                }
            }

            if((! Yii::$app->session->hasFlash('error')) || ($force == '1') )
            {
                if($form->apply())
                {
                    if($form->shipped)
                        Yii::$app->session->addFlash('success',"ありがとうございます。入会の申し込みが完了しました");
                    elseif($user instanceof Staff)
                        Yii::$app->session->addFlash('success',"申し込みが完了しました。ただし会員資格はまだ有効ではありません。「発送済み」にすると会員資格が適用されます");
                    else
                        Yii::$app->session->addFlash('error',"申し込みが完了しました。ただし会員資格はまだ有効ではありません。システムエラーが発生した可能性があります。お手数ですが担当者までお問い合わせください");

                    if($user instanceof Staff)
                    {
                        $purchase_id = $form->customer
                                            ->getPurchases()
                                            ->orderBy(['purchase_id'=>SORT_DESC])
                                            ->select('purchase_id')
                                            ->scalar();

                        return $this->redirect([
                            ($form->paid ? '/casher/default/receipt' : '/purchase/view'),
                            'id' => $purchase_id
                        ]);
                    }

                    return $this->redirect(['view','id'=>$model->customer_id]);
                }
            }

            if($form->hasErrors())
            {
                Yii::error(['errorSummary' => Html::errorSummary($form),
                            'attributes'   => $form->attributes,
                ]);
                throw new \yii\web\ServerErrorHttpException("登録中にエラーが発生しました。申し訳ありませんが復旧するまでしばらくおまちください");
            }
        }

        return $this->render('create',['model'=>$model,'mcode'=>$mcode]);
    }

    public function actionUpdate($id, $mid=null, $pid=Payment::PKEY_CASH)
    {
        $customer = $this->findModel($id);
        $model    = new UpdateForm(['customer_id'  => $id,
                                    'membership_id'=> $mid,
                                    'payment_id'   => $pid,
                                    'paid'         => (int)false,
        ]);
        $user     = Yii::$app->user->identity;

        if(Payment::PKEY_CASH == $pid)
            $model->paid = (int)true;

        $customer->scenario = $customer::SCENARIO_EMERGENCY;
        if(! $customer->validate() &&
             $customer->load(Yii::$app->request->post()) &&
             $customer->validate()
        )
            $customer->save();

        $viewParams = ['model'   => $model,
                       'customer'=> $customer,
                       'payments'=> $this->module->getPayments($user instanceof Staff)
        ];

        if(! $model->load(Yii::$app->request->post()))
        {
            $model->validate(['customer_id','payment_id']); // 最低限の validate()
            return $this->render('update', $viewParams);
        }

        if($model->validate() && $customer->validate())
        {
            $form = new ToranokoApplicationForm([
                'customer'   => $customer,
                'membercode' => $customer->code,
                'seller'     => ($user instanceof Staff) ? null : $user,
                'branch'     => Branch::findOne(Branch::PKEY_HE_TORANOKO),
                'product_id' => $model->product_id,
                'issues'     => $model->issues,
                'shipped'    => $model->paid ? true : false,
                'paid'       => $model->paid,
                'payment_id' => $pid,
                'pointBack'  => ($user instanceof Staff) ? 0 : $this->module->pointBack[$model->product_id],
            ]);

            if($form->validate() && $form->apply())
            {
                if($form->shipped)
                    Yii::$app->session->addFlash('success',"ありがとうございます。会員資格が更新されました");
                else
                    Yii::$app->session->addFlash('success',"伝票が起票されました。ただし会員資格はまだ有効ではありません。「発送済み」にすると会員資格が適用されます");

                if($user instanceof Staff)
                {
                    $purchase_id = $customer->getPurchases()
                                            ->orderBy(['purchase_id'=>SORT_DESC])
                                            ->select('purchase_id')
                                            ->scalar();

                    return $this->redirect([
                        ($model->paid ? '/casher/default/update' : '/purchase/view'),
                        'id' => $purchase_id
                    ]);
                }
                return $this->redirect(['view','id'=>$customer->customer_id]);
            }

            Yii::error(['errorSummary' => \yii\helpers\Json::encode($form->firstErrors),
                        'attributes'   => $form->attributes,]);

            throw new \yii\web\ServerErrorHttpException("登録中にエラーが発生しました。申し訳ありませんが復旧するまでしばらくおまちください");
        }

        return $this->render('update', $viewParams);
    }

    protected function findModel($id)
    {
        $model = parent::findModel($id);

        if($p = $model->parent)
            throw new \yii\base\UserException("{$model->name} さんは {$p->name} さんの家族会員です。手続きはできません");

        return $model;
    }
}
