<?php
namespace common\components;

use Yii;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/CustomerMigration.php $
 * $Id: CustomerMigration.php 3032 2016-10-28 01:49:30Z mori $
 */

class CustomerMigration
{
    /**
     * @brief  attach membercode to the customer
     *         Note: beware for Exception (should catch and display error message on \yii\web\Application)
     * @var    $dst
     * @var    $model
     * @return bool
     */
    public static function attachMembercode(\common\models\Customer $dst, \common\models\Membercode $model)
    {
        if($model->customer_id && ($dst->customer_id != $model->customer_id))
            throw new \yii\base\UserException(sprintf("指定された会員証NOには別の顧客が紐付けされています:(%s!=%s)",
                                          $model->customer_id,
                                          $dst->customer_id));

        // attach new membercode to the customer
        if(!$model->customer_id)
            $model->customer_id = $dst->customer_id;

        $model->status = 1; // status が -1 されるので +1 しておく
        $model->save(false);

        $ret = Yii::$app->db
                 ->createCommand("UPDATE mtb_membercode SET status = status - 1 WHERE customer_id = :cid")
                 ->bindValues([':cid' => $model->customer_id,])
                 ->execute();
        if(! $ret)
            return false;

        self::addInfo($dst->customer_id, sprintf("会員証NO(%s)を紐付け／最上位に位置づけました",$model->code));

        return true;
    }

    /**
     * @return array of Customer - common\models\{webdb18,20,eccube,ecorange}\{Customer,CustomerForm}
     */
    public static function findModels(\common\models\Customer $dst)
    {
        return SrcModelFinder::findModels($dst);
    }

    /**
     * @brief  INSERT or UPDATE a record in dtb_customer
     * @var    $src \common\models\webdb{18,20}\CustomerForm, or \common\models\ecorange\Customer
     * @return Customer model (newly inserted or already inserted)
     */
    public static function migrateModel($src)
    {
        if($src instanceof \common\models\Membercode)
            if(! $src = self::findSrcModel($src))
                return false;

        $dst = self::findModel($src);
        if(!$dst)
            $dst = self::loadModel($src);

        if($dst->isNewRecord)
        {
            $dst->detachBehavior('membercode'); // skip creating membercode
            if(! $dst->save(false))
            {
                Yii::error(["会員の移行に失敗しました",$dst->attributes,$dst->errors], __CLASS__.'::'.__FUNCTION__);
                return $dst;
            }
            else
                self::addInfo($dst->customer_id, sprintf("%sから顧客情報を移行しました", $src->schema));
        }

        self::syncModel($dst, $src);

        return $dst;
    }

    /**
     * @brief  attach (webdb18|webdb20|eccube|ecorange) to the customer
     * @var    $dst \common\models\Customer
     * @var    $src \common\models\webdb{18,20}\CustomerForm, or
     *              \common\models\ecorange\Customer, or
     *              \common\models\eccube\Customer
     * @return bool
     * @link   @tests/codeception/common/unit/functional/signup/*Test.php
     */
    public static function syncModel(\common\models\Customer $dst, \yii\base\Model $src)
    {
        if(! self::linkByMembercode($dst, $src))
            Yii::error(["linkByMembercode()に失敗しましたがsyncModel()を続行します",$dst->attributes,$src->attributes]);

        self::syncMembership($dst, $src);

        // sync childlen
        $params = $src->migrateAttributes();
        if(isset($params['children']))
           foreach($params['children'] as $model)
        {
            $child = self::migrateModel($model);
            if(! $child || ! $child->customer_id)
            {
                Yii::error(["子会員の登録に失敗しました",$child->errors], __CLASS__.'::'.__FUNCTION__);
                continue;
            }
            if(! $child->parent)
                $dst->link('children', $child);
        }

        self::syncPoint($dst, $src);

        // fetch the model with newly created relations
        $model = \common\models\Customer::findOne($dst->customer_id);
        if(! $model)
            return null;

        $model->save(false,['grade_id','point']); // attributes possibly updated by Customer::afterFind()

        return true;
    }

    /**
     * 会員証NOを介して旧IDを紐づける。会員証はすでにあるかもしれないし、ないかもしれない
     */
    private static function linkByMembercode($dst, $src)
    {
        $mcode = \common\models\Membercode::find()
               ->andWhere(['migrate_id' => $src->customerid])
               ->andWhere(['directive'  => $src->schema])
               ->andWhere(['customer_id'=> $dst->customer_id])
               ->one();
        if($mcode && (0 == $mcode->status))
            return true; // no need to update

        $mcode = \common\models\Membercode::find()
               ->andWhere(['migrate_id' => $src->customerid])
               ->andwhere(['directive'  => $src->schema])
               ->one();

        if($mcode && ! $mcode->customer_id)
        {
            $mcode->customer_id = $dst->customer_id;
            $mcode->status      = 0 - abs($mcode->find()->where(['customer_id'=>$mcode->customer_id])->count());
            $mcode->save(false,['customer_id']);
        }
        elseif($mcode && ($dst->customer_id != $mcode->customer_id))
        {
            // 指定された会員証NOには別の顧客IDで紐付けが完了している
            // 顧客本人による別Email登録などで移行済みとみなす
            return false;
        }
        elseif(! $mcode)
        {
            $mcode = new \common\models\Membercode([
                'directive'   => $src->schema,
                'migrate_id'  => $src->customerid,
                'customer_id' => $dst->customer_id,
            ]);
            $mcode->save();
        }

        return true;
    }

    /**
     * @return true
     */
    private static function syncMembership($dst, $src)
    {
        $active  = $dst->getMemberships()->select('membership_id')->column();
        $expired = $dst->getMemberships(false)->select('membership_id')->column();

        $params = $src->migrateAttributes();
        if(isset($params['memberships']))
           foreach($params['memberships'] as $param)
        {
            $mship = new \common\models\CustomerMembership(['customer_id'=>$dst->customer_id]);
            $mship->load($param,'');

            if(in_array($mship->membership_id, $active))
                continue;
            if(in_array($mship->membership_id, $expired) && $mship->isExpired())
                continue;

            if(! $mship->validate('membership_id'))
            {
                Yii::error(['Membershipの追加に失敗しました', $mship->errors], __CLASS__.'::'.__FUNCTION__);
                continue;
            }

            if($mship->save())
                self::addInfo($dst->customer_id, sprintf("会員区分(%s)を追加しました",$mship->membership->name));
            else
                Yii::error(['Membershipの追加に失敗しました', $mship->errors], __CLASS__.'::'.__FUNCTION__);
        }

        return true;
    }

    /* @return integer (sync point amount) */
    private static function syncPoint($dst, $src)
    {
        if($src instanceof \common\models\eccube\Customer ||
           $src instanceof \common\models\ecorange\Customer)
               $pt = (int)$src->point;
        else
            return 0; // no point to sync

        $record = \common\models\Pointing::find()->where([
            'customer_id'  => $dst->customer_id,
            'seller_id'    => $dst->customer_id,
            'company_id'   => $src->company->company_id,
            'point_consume'=> 0,
            'subtotal'     => 0,
            'tax'          => 0,
            'total_charge' => 0,
            'receive'      => 0,
            'change'       => 0,
            'note'         => sprintf('旧DBより自動移行 (migrate_id: %s)', $src->customerid),
            'status'       => \common\models\Pointing::STATUS_SOLD,
        ])->one();
        if($record)
        {
            $record->point_given = $pt;
        }
        else
            $record = new \common\models\Pointing([
                'customer_id'  => $dst->customer_id,
                'seller_id'    => $dst->customer_id,
                'company_id'   => $src->company->company_id,
                'point_given'  => $pt,
                'point_consume'=> 0,
                'subtotal'     => 0,
                'tax'          => 0,
                'total_charge' => 0,
                'receive'      => 0,
                'change'       => 0,
                'note'         => sprintf('旧DBより自動移行 (migrate_id: %s)', $src->customerid),
                'status'       => \common\models\Pointing::STATUS_SOLD,
            ]);

        if($record->save(false))
            return $pt;

        return 0;
    }

    /**
     * @brief INSERT INTO 
     */
    private function addInfo($customer_id, $content)
    {
        $info = new \backend\models\CustomerInfo([
            'customer_id'=> $customer_id,
            'content'    => $content,
        ]);

        return $info->save();
    }

    /**
     * @brief search the existing customer
     * @return Customer | null
     */
    private static function findModel($src)
    {
        $membercode = \common\models\Membercode::find()->where([
            'directive'  => $src->schema,
            'migrate_id' => $src->customerid,
        ])->one();
        if($membercode && $membercode->customer_id)
            return $membercode->customer;

        return null;
    }

    /**
     * @brief search src model from membercode
     * @return common/models/webdb/Customer | null | false
     */
    private function findSrcModel(\common\models\Membercode $model)
    {
        $src = null;

        if(! $model->migrate_id)
            return false;

        if('webdb20' == $model->directive)
            $src = \common\models\webdb20\SearchCustomer::findOne($model->migrate_id);
        if('webdb18' == $model->directive)
            $src = \common\models\webdb18\SearchCustomer::findOne($model->migrate_id);

        return $src;
    }

    /**
     * @brief init new customer model
     * @return Customer
     */
    private static function loadModel($src)
    {
        $dst = new \common\models\Customer([
            'name01' => $src->name01,
            'name02' => $src->name02,
            'kana01' => $src->kana01,
            'kana02' => $src->kana02,
            'email'  => $src->email,
            'sex_id' => $src->sex_id,
            'zip01'  => $src->zip01,
            'zip02'  => $src->zip02,
            'pref_id'=> $src->pref_id,
            'addr01' => $src->addr01,
            'addr02' => $src->addr02,
            'tel01'  => $src->tel01,
            'tel02'  => $src->tel02,
            'tel03'  => $src->tel03,
            'birth'  => $src->birth,
        ]);

        if(! $dst->validate())
        {
            if(! $dst->name01)
            {
                $dst->name01 = $src->name;
                $dst->clearErrors('name01');
            }
            if(! $dst->kana01)
            {
                $dst->kana01 = $src->name;
                $dst->clearErrors('kana01');
            }
            
            foreach(['name02','kana02'] as $attr)
            if($dst->hasErrors($attr))
            {
                $dst->clearErrors($attr);
                $dst->$attr = ' '; // not null
            }
        }

        return $dst;
    }
}

class SrcModelFinder
{
    /*
     * @brief find a models from various DB
     * @return array
     */
    public static function findModels(\common\models\Customer $dst)
    {
        $models = [];
        $membercodes = \common\models\Membercode::find()
            ->andWhere(['customer_id' => $dst->customer_id])
            ->andWhere(['not',['directive' => null]])
            ->all();
        if($membercodes)
            foreach($membercodes as $model)
                $models[$model->directive] = $model->migratedModel;

        // わざわざ webdb18 を探しにいく理由がないので findModel() しない
        // 2015.10.02 mori
        /* if(! isset($models['webdb18']))
           $models['webdb18'] = self::findModel($dst,'webdb18'); */

        if(! isset($models['webdb20']))
            $models['webdb20'] = self::findModel($dst,'webdb20');

        if(! isset($models['ecorange']))
            $models['ecorange'] = self::findModel($dst,'ecorange');

        if(! isset($models['eccube']))
            $models['eccube'] = self::findModel($dst,'eccube');

        foreach($models as $k => $model)
        {
            if(null === $model)
            {
                unset($models[$k]);
                continue;
            }

            $mcode = \common\models\Membercode::find()
                ->andWhere(['directive'  => $k                ])
                ->andWhere(['migrate_id' => $model->customerid])
                ->andWhere(['not',['customer_id' => $dst->customer_id]])
                ->one();
            if($mcode)
            {
                unset($models[$k]);
                Yii::info(sprintf('customerid(%s:%d) is already defined for (%d), not for (%d)'."\n",
                       $mcode->directive,
                       $model->customerid,
                       $mcode->customer_id,
                       $dst->customer_id),__CLASS__.'::'.__FUNCTION__);
            }
        }

        return array_values($models);
    }
    /*
     * @brief find a model from tblcustomer
     * @return \common\models\webdb20\CustomerForm or null
     */
    private static function findModel(\common\models\Customer $dst, $schema)
    {
        if($dst->email)
            if($src = self::findByEmail($schema, $dst->email))
                return $src;

        $tel = implode('',[$dst->tel01,$dst->tel02,$dst->tel03]);
        if(! $tel)
            return null;

        if($src = self::findByTel($schema, $tel))
            return $src;

        if($src = self::findByTel($schema, $tel,$dst->kana))
            return $src;

        return null;
    }

    private static function findByEmail($schema,$email)
    {
        $validator = new \yii\validators\EmailValidator(['skipOnEmpty'=>false]);
        if(! $validator->validate($email))
            return null;

        if('webdb18' == $schema)
            return self::findByEmailFromW18($email);
        if('webdb20' == $schema)
            return self::findByEmailFromW20($email);
        elseif('ecorange' == $schema)
            return self::findByEmailFromEO($email);
        else
            return self::findByEmailFromEC($email);
    }

    private static function findByEmailFromW18($email)
    {
        $db = Yii::$app->webdb18;

        // check duplication
        $query = (new \yii\db\Query())
            ->from('tblcustomer')
            ->andWhere(['email' => $email]);

        $rows = $query->column($db);

        if(1 == count($rows))
            return \common\models\webdb18\SearchCustomer::findOne(array_shift($rows));

        return null;
    }

    private static function findByEmailFromW20($email)
    {
        $db = Yii::$app->webdb20;

        // check duplication
        $query = (new \yii\db\Query())
            ->from('tblcustomer')
            ->andWhere(['email' => $email])
            ->select('customerid');

        $rows = $query->column($db);

        if(1 == count($rows))
            return \common\models\webdb20\SearchCustomer::findOne(array_shift($rows));

        return null;
    }

    private static function findByEmailFromEC($email)
    {
        $db = Yii::$app->ecCube;

        $query = (new \yii\db\Query())
            ->from('dtb_customer')
            ->andWhere(['email'   => $email])
            ->andWhere(['status'  => 3 /* とようけ */])
            ->andWhere(['del_flg' => 0 /* 有効 */]);

        $rows = $query->column($db);

        if(1 == count($rows))
            return \common\models\eccube\Customer::findOne(array_shift($rows));

        return null;
    }

    private static function findByEmailFromEO($email)
    {
        $db = Yii::$app->ecOrange;

        $query = (new \yii\db\Query())
            ->from('dtb_customer')
            ->andWhere(['email' => $email])
            ->andWhere(['del_flg' => 0 /* 有効 */])
            ->andWhere(['shop_id' => 1 /* 一般 */]);

        $rows = $query->column($db);

        if(1 == count($rows))
            return \common\models\ecorange\Customer::findOne(array_shift($rows));

        return null;
    }

    /*
     * @brief find a model from tblcustomer
     * @return \common\models\webdb20\CustomerForm or null
     */
    private function findByTel($schema, $tel, $kana=null)
    {
        $validator = new \yii\validators\NumberValidator(['min'=>1,'skipOnEmpty'=>false]);
        if(! $validator->validate($tel))
            return null;

        $validator = new \yii\validators\StringValidator(['min'=>2,'skipOnEmpty'=>true]);
        if(! $validator->validate($kana))
            $kana = '';

        if('webdb18' == $schema)
            return self::findByTelFromW18($tel,$kana);
        if('webdb20' == $schema)
            return self::findByTelFromW20($tel,$kana);
        elseif('ecorange' == $schema)
            return self::findByTelFromEO($tel,$kana);
        else
            return self::findByTelFromEC($tel,$kana);
    }

    private static function findByTelFromEC($tel, $kana)
    {
        $db    = Yii::$app->ecCube;
        $query = (new \yii\db\Query)
               ->select('customer_id')
               ->from('dtb_customer')
               ->andWhere(['concat(tel01,tel02,tel03)' => $tel])
               ->andWhere(['status'  => 3 /* とようけ */])
               ->andWhere(['del_flg' => 0]);

        $rows = $query->column($db);

        if(1 == count($rows)) // exact match
            return \common\models\eccube\Customer::findOne(array_shift($rows));

        if(! $kana) // no further clue
            return null;

        /* several customerid detected, don't know which is the best matched */

        // prepare a query param
        $kana = mb_convert_kana($kana, 'C');

        // narrower the search
        $rows = $query->andWhere(['customer_id' => $rows])
                      ->andWhere(["concat(kana01,' ',kana02)" => $kana])
                      ->column($db);

        if(1 == count($rows))
            return \common\models\eccube\Customer::findOne(array_shift($rows));

        return null;
    }

    private static function findByTelFromEO($tel, $kana)
    {
        $db    = Yii::$app->ecOrange;
        $query = (new \yii\db\Query)
               ->select('customer_id')
               ->from('dtb_customer')
               ->andWhere(['concat(tel01,tel02,tel03)' => $tel])
               ->andWhere(['del_flg' => 0])
               ->andWhere(['shop_id' => 1]);
        $rows = $query->column($db);

        if(1 == count($rows)) // exact match
            return \common\models\ecorange\Customer::findOne(array_shift($rows));

        if(! $kana) // no further clue
            return null;

        /* several customerid detected, don't know which is the best matched */

        // prepare a query param
        $kana = mb_convert_kana($kana, 'C');

        // narrower the search
        $rows = $query->andWhere(['customer_id' => $rows])
                      ->andWhere(["concat(kana01,' ',kana02)" => $kana])
                      ->column($db);

        if(1 == count($rows))
            return \common\models\ecorange\Customer::findOne(array_shift($rows));

        return null;
    }

    private static function findByTelFromW18($tel,$kana)
    {
        $db = Yii::$app->webdb18;
        if($customerid = self::findByTelFromWebdb($db, $tel, $kana))
            return \common\models\webdb18\SearchCustomer::findOne($customerid);

        return null;
    }

    private static function findByTelFromW20($tel,$kana)
    {
        $db = Yii::$app->webdb20;
        if($customerid = self::findByTelFromWebdb($db, $tel, $kana))
            return \common\models\webdb20\SearchCustomer::findOne($customerid);

        return null;
    }

    private static function findByTelFromWebdb($db, $tel, $kana)
    {
        $q = (new \yii\db\Query)
              ->select('c.customerid')
              ->from('tblcustomer c')
              ->leftJoin('tbladdress a','a.customerid = c.customerid')
              ->leftJoin('tbloffice  o','o.customerid = c.customerid')
              ->leftJoin('tblsomeadd s','s.customerid = c.customerid')
              ->leftJoin('tbloldaddr l','l.customerid = c.customerid')
              ->andFilterwhere(['OR',
                  ['a.tel'    => $tel],
                  ['o.tel'    => $tel],
                  ['s.tel'    => $tel],
                  ['l.tel'    => $tel],
                  ['a.mobile' => $tel],
              ]);
        $rows = $q->column($db);

        if(1 == count($rows)) // exact match
            return array_shift($rows);

        if(! $kana) // no further clue
            return null;

        /* several customerid detected, don't know which is the best matched */

        // prepare a query param
        $kana = mb_convert_kana($kana, 'SC');

        if('euc-jp' == $db->charset)
            $kana = mb_convert_encoding($kana, 'CP51932', 'UTF-8'); // conv utf8 to EUC-WIN-JP

        // narrower the search
        $q = (new \yii\db\Query())
              ->select('customerid')
              ->from('tblcustomer')
              ->where(['customerid' => $rows])
              ->andWhere(['kana' => $kana]);

        $rows = $q->column($db);

        if(1 == count($rows))
            return array_shift($rows);

        return null;
    }
}
