<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Purchase;
use common\models\Membership;

/**
 * SearchPurchase represents the model behind the search form about `common\models\Purchase`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchPurchase.php $
 * $Id: SearchPurchase.php 3720 2017-11-02 03:48:22Z kawai $
 */
class SearchPurchase extends Purchase
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['purchase_id', 'branch_id','company_id','customer_id', 'subtotal', 'tax', 'postage', 'total_charge', 'receive', 'change', 'payment_id', 'paid', 'shipped', 'staff_id'], 'integer'],
            [['create_date', 'update_date', 'customer_msg', 'note','status', 'is_agency', 'agencies'], 'safe'],
        ];
    }

    public static function primaryKey()
    {
        return ['purchase_id'];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params=[])
    {
        $query = Purchase::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['purchase_id'=>SORT_DESC]]
        ]);

        $this->load($params);

        /* if (!$this->validate()) {
           // uncomment the following line if you do not want to any records when validation fails
           // $query->where('0=1');
           return $dataProvider;
           } */

        $query->andFilterWhere([
            'purchase_id'  => $this->purchase_id,
            'branch_id'    => $this->branch_id,
            'company_id'   => $this->company_id,
            'customer_id'  => $this->customer_id,
            'subtotal'     => $this->subtotal,
            'tax'          => $this->tax,
            'postage'      => $this->postage,
            'total_charge' => $this->total_charge,
            'receive'      => $this->receive,
            'change'       => $this->change,
            'payment_id'   => $this->payment_id,
            'paid'         => $this->paid,
            'shipped'      => $this->shipped,
            'status'       => $this->status,
        ]);

        $query->andFilterWhere(['like', 'create_date', $this->create_date]);
        $query->andFilterWhere(['like', 'update_date', $this->update_date]);
        $query->andFilterWhere(['like', 'note', $this->note]);
        $query->andFilterWhere(['like', 'customer_msg', $this->customer_msg]);


        if($this->is_agency) {

            $query->leftJoin(\common\models\CustomerMembership::tableName(), 'dtb_customer_membership.customer_id = dtb_purchase.customer_id');

            if(2 == $this->is_agency) {
               $query->andFilterWhere(['in', 'dtb_customer_membership.membership_id', [\common\models\Membership::PKEY_AGENCY_HJ_A,\common\models\Membership::PKEY_AGENCY_HJ_B,\common\models\Membership::PKEY_AGENCY_HE,\common\models\Membership::PKEY_AGENCY_HP]])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'));
            } else if (1 == $this->is_agency) {

               $query2 = clone $query;

               $query2->andWhere(['in', 'dtb_customer_membership.membership_id', [\common\models\Membership::PKEY_AGENCY_HJ_A,\common\models\Membership::PKEY_AGENCY_HJ_B,\common\models\Membership::PKEY_AGENCY_HE,\common\models\Membership::PKEY_AGENCY_HP]])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))->distinct();

                $query->andFilterWhere(['not in', 'purchase_id',  $query2->select(Purchase::tableName().'.purchase_id')->asArray()->all()]);
                
            }
        }

        if(isset($this->agencies)) {
                $three_agency_query = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'membership_id', \common\models\Membership::PKEY_AGENCY_HE, \common\models\Membership::PKEY_AGENCY_HP])
                      ->andWhere('start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('customer_id')
                      ->having('COUNT(*) >= 3')
                      ->select('customer_id')
                      ->asArray()->all();
               $three_agency = \yii\helpers\ArrayHelper::getColumn($three_agency_query, 'customer_id');
            switch($this->agencies) {
            // なし
            case 99:
                break;
            // HJ
            case 0:

                $query2 = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'dtb_customer_membership.membership_id', \common\models\Membership::PKEY_AGENCY_HJ_A, \common\models\Membership::PKEY_AGENCY_HJ_B])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 1')
                      ->select('customer_id');

// HJ HEを除外
                $hj_he_query = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'dtb_customer_membership.membership_id', \common\models\Membership::PKEY_AGENCY_HE, \common\models\Membership::PKEY_AGENCY_HJ_B])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');

// HJ HPを除外
                $hj_hp_query = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'dtb_customer_membership.membership_id', \common\models\Membership::PKEY_AGENCY_HJ_A, \common\models\Membership::PKEY_AGENCY_HP])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');


                $query->andFilterWhere(['in', Purchase::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($query2->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Purchase::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($hj_he_query->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Purchase::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($hj_hp_query->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Purchase::tableName().'.customer_id', $three_agency, 'customer_id']);

                break;


            // HE
            case 1:

                $query2 = \common\models\CustomerMembership::find()
                      ->where(['dtb_customer_membership.membership_id' => \common\models\Membership::PKEY_AGENCY_HE])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 1')
                      ->select('customer_id');

// HJ HEを除外
                $hj_he_query = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'dtb_customer_membership.membership_id', \common\models\Membership::PKEY_AGENCY_HE, \common\models\Membership::PKEY_AGENCY_HJ_B])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');

// HE HPを除外
                $he_hp_query = \common\models\CustomerMembership::find()
                      ->where(['IN',  'dtb_customer_membership.membership_id', [\common\models\Membership::PKEY_AGENCY_HE, \common\models\Membership::PKEY_AGENCY_HP]])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');


                $query->andFilterWhere(['in', Purchase::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($query2->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Purchase::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($hj_he_query->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Purchase::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($he_hp_query->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Purchase::tableName().'.customer_id', $three_agency, 'customer_id']);



                break;
            // HP
            case 2:

                $query2 = \common\models\CustomerMembership::find()
                      ->where(['dtb_customer_membership.membership_id' => \common\models\Membership::PKEY_AGENCY_HP])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 1')
                      ->select('customer_id');

// HJ HPを除外
                $hj_hp_query = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'dtb_customer_membership.membership_id', \common\models\Membership::PKEY_AGENCY_HJ_A, \common\models\Membership::PKEY_AGENCY_HP])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');

// HE HPを除外
                $he_hp_query = \common\models\CustomerMembership::find()
                      ->where(['IN',  'dtb_customer_membership.membership_id', [\common\models\Membership::PKEY_AGENCY_HE, \common\models\Membership::PKEY_AGENCY_HP]])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');


                $query->andFilterWhere(['in', Purchase::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($query2->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Purchase::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($hj_hp_query->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Purchase::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($he_hp_query->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Purchase::tableName().'.customer_id', $three_agency, 'customer_id']);




                break;
            // HJ HE
            case 3:

                $query2 = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'dtb_customer_membership.membership_id', \common\models\Membership::PKEY_AGENCY_HE, \common\models\Membership::PKEY_AGENCY_HJ_B])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');
                $query->andFilterWhere(['in', Purchase::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($query2->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Purchase::tableName().'.customer_id', $three_agency, 'customer_id']);

                break;
            // HJ HP
            case 4:

                $query2 = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'dtb_customer_membership.membership_id', \common\models\Membership::PKEY_AGENCY_HJ_A, \common\models\Membership::PKEY_AGENCY_HP])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');
                $query->andFilterWhere(['in', Purchase::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($query2->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Purchase::tableName().'.customer_id', $three_agency, 'customer_id']);

                break;

            // HE HP
            case 5:

                $query2 = \common\models\CustomerMembership::find()
                      ->where(['IN',  'dtb_customer_membership.membership_id', [\common\models\Membership::PKEY_AGENCY_HE, \common\models\Membership::PKEY_AGENCY_HP]])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');
                $query->andFilterWhere(['in', Purchase::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($query2->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Purchase::tableName().'.customer_id', $three_agency, 'customer_id']);

                break;
            // HJ HE HP
            case 6:
                $query->andFilterWhere(['in', Purchase::tableName().'.customer_id', $three_agency, 'customer_id']);
                break;

            }
        }

       $query->distinct();

#print($query->createCommand()->rawSql);exit;
        return $dataProvider;
    } 


}
