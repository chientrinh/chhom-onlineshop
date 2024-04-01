<?php
namespace backend\rbac;
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/rbac/DbManager.php $
 * $Id: DbManager.php 2235 2016-03-12 06:16:28Z mori $
 */

use Yii;
use \backend\models\StaffRole;
use \backend\models\Role;

class DbManager extends \yii\rbac\DbManager
{
    public function init()
    {
        parent::init();
    }

    /* @return void */
    public function assignRoles($userid)
    {
        if($this->getAssignments($userid))
            return;

        $names = Role::find()->andWhere([
            'role_id' => StaffRole::find()->andWhere(['staff_id' => $userid])
                                          ->select('role_id')
                                          ->column()
        ])->select('name')->column();

        foreach($names as $name)
            if($role = $this->getRole($name))
                $this->assign($role, $userid);
    }
}
