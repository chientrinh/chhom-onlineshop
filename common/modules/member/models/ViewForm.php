<?php
namespace common\modules\member\models;

use Yii;
use \common\models\CustomerMembership;
use \common\models\Membership;
use \common\models\Product;

/**
 * Customer Create Form
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/member/models/ViewForm.php $
 * $Id: ViewForm.php 2351 2016-04-01 03:26:31Z mori $
 */

class ViewForm extends \common\models\Customer
{
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'membership' => '会員種別',
            ]
        );
    }

    public function getMembership()
    {
        return $this->hasOne(CustomerMembership::className(),['customer_id' => 'customer_id'])
                    ->orderBy('expire_date DESC')
                    ->andWhere([
            'membership_id' => [ Membership::PKEY_TORANOKO_GENERIC,
                                 Membership::PKEY_TORANOKO_NETWORK,
                                 Membership::PKEY_TORANOKO_GENERIC_UK,
                                 Membership::PKEY_TORANOKO_NETWORK_UK,
                                 Membership::PKEY_TORANOKO_FAMILY]]);
    }

    public function wasMember()
    {
        return $this->getMembership()->exists();
    }

    public function isMember()
    {
        return ($mship = $this->membership) && ! $mship->isExpired();
    }
}
