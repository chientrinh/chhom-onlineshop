<?php
namespace backend\rbac;
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/rbac/UserGroupRule.php $
 * $Id: UserGroupRule.php 2235 2016-03-12 06:16:28Z mori $
 */

use Yii;
use \yii\helpers\ArrayHelper;
use \backend\models\StaffRole;
use \backend\models\Role;

class UserGroupRule extends \yii\rbac\Rule
{
    public $name = 'userGroup';

    public function execute($userid, $item, $params)
    {
        $role_id = Role::find()->where(['name'=>$item->name])
                               ->select('role_id')
                               ->scalar();
        if(! $role_id)
            return false;

        $query = StaffRole::find()->andWhere(['staff_id' => $userid]);

        if(in_array($item->name, ['tenant','editor']))
            $query->andWhere(['role_id' => $role_id]);
        else
            $query->andWhere(['>=','role_id', $role_id]);

        return $query->exists();
    }
}
