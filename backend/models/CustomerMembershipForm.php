<?php

namespace backend\models;

use Yii;

/**
 * This is the model class to submit CustomerMembership
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/CustomerMembershipForm.php $
 * $Id: CustomerMembershipForm.php 4171 2019-07-15 19:11:42Z mori $
 *
 */
class CustomerMembershipForm extends \common\models\CustomerMembership
{
    public $note;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return \yii\helpers\ArrayHelper::merge(parent::rules(), [
            [['note'], 'trim'],
            [['note'], 'required'],
            [['note'], 'string', 'length'=>[1, 1024]],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['note'] = "備考";

        return $labels;
    }

    public function save( $runValidation = true, $attributeNames = null )
    {
        $transaction = Yii::$app->db->beginTransaction();
        try
        {
            // スペシャルランクアップ新規作成時に限定。UPDATEで消費させないように
            $use_point_rankup = $this->isNewRecord && $this->membership_id == \common\models\Membership::PKEY_SPECIAL_RANKUP;
            
            if(! parent::save( $runValidation, $attributeNames ))
                throw new \yii\db\Exception('failed to save dtb_customer_membership');

            // スペシャルランクアップする時は1000ポイントを消費させる（新規作成時）
            if ($use_point_rankup) {
                $pointing = new \common\models\Pointing([
                    'customer_id' => $this->customer_id,
                    'seller_id'   => $this->customer_id,
                    'point_given' => -1000,
                    'note'        => 'ポイント使用によるスペシャルランクアップ',
                    'total_charge' => 0,
                    'status'       => \common\models\Pointing::STATUS_SOLD,
                    'company_id'   => \common\models\Company::PKEY_TY,
                ]);
                if (!$pointing->save()) {
                    throw new \yii\db\Exception('failed to save dtb_pointing');
                }
            }

            $note = new CustomerInfo([
                'customer_id' => $this->customer_id,
                'content'     => $this->note,
            ]);
            if(! $note->save( $runValidation ))
            {
                throw new \yii\db\Exception('failed to save dtb_customer_info');
            }

        }
        catch (\yii\db\Exception $e)
        {
            Yii::warning($e->__toString(), $this->className().'::'.__FUNCTION__);

            $transaction->rollBack();
            return false;
        }
        $transaction->commit();

        return true;
    }

}
