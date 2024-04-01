<?php
namespace backend\rbac;
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/rbac/OwnerRule.php $
 * $Id: OwnerRule.php 2481 2016-05-03 00:30:31Z mori $
 */

use Yii;
use \yii\helpers\ArrayHelper;

class OwnerRule extends \yii\rbac\Rule
{
    public $name = 'isOwner';

    public function execute($userid, $item, $params)
    {
        if(! $user = \backend\models\Staff::findOne($userid))
            return false;

        if($user->company_id == ArrayHelper::getValue($params, 'company_id'))
            return true;

        if($customer_id = ArrayHelper::getValue($params, 'customer_id'))
            return Purchase::find()->active()
                            ->andWhere(['company_id' => $user->company_id,
                                        'customer_id'=> $customer_id])->exists();

        return false;
    }
}
