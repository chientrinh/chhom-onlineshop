<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "mtb_staff".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/Staff.php $
 * $Id: Staff.php 3243 2017-04-13 10:59:36Z kawai $
 *
 * @property integer $staff_id
 * @property integer $company_id
 * @property string $name01
 * @property string $name02
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property string $update_date
 * @property string $expire_date
 *
 * @property Inventory[] $dtbInventories
 * @property Manufacture[] $dtbManufactures
 * @property MaterialInventory[] $dtbMaterialInventories
 * @property MaterialStorage[] $dtbMaterialStorages
 * @property Company $company
 * @property StaffRole[] $mtbStaffRoles
 * @property Branch[] $branches
 */
class Staff extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const DATETIME_MAX = '3000-00-00 00:00:00';
    const PKEY_NOBODY  = 0;
    const STAFF_SYSTEM = '0'; // システム　自動処理

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mtb_staff';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'log' => [
                'class'  => \common\models\ChangeLogger::className(),
                'owner'  => $this,
                'user'   => Yii::$app->has('user') ? Yii::$app->user : null,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'name01', 'name02'], 'required'],
            [['company_id'],                'integer'],
            [['email'],                     'email'],
            [['name01', 'name02'],          'string', 'max' => 128],
            [['email', 'password'],         'string', 'max' => 255],
            [['company_id','name01','name02','email','update_date','expire_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'staff_id'    => "従業員ID",
            'company_id'  => 'Company ID',
            'name01'      => 'Name01',
            'name02'      => 'Name02',
            'email'       => "メールアドレス",
            'password'    => "パスワード",
            'update_date' => '更新日',
            'expired'     => '期限切れ',
        ];
    }

    public function activate()
    {
        $this->expire_date = self::DATETIME_MAX;
        return $this->save();
    }

    public function expire()
    {
        $this->expire_date = date('Y-m-d H:i:s');
        return $this->save();
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new StaffQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['staff_id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by email
     *
     * @param string $username
     * @return static|null
     */
    public static function findByEmail($email)
    {
        $model = self::findOne(['email' => $email]);
        if($model && $model->expired)
            $model = null;

        return $model;
    }

    /**
     * Finds user by password reset token (used when forgot password)
     *
     * @param string $username
     * @return static|null
     */
    public function findByPasswordResetToken($token)
    {
        $model = \common\models\PasswordResetToken::find()
               ->where(['token'=>$token])
               ->active()
               ->one();
        
        if($model)
            $staff = self::findByEmail($model->email);

        return isset($staff) ? $staff : null;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranches()
    {
        return $this->hasMany(\common\models\Branch::className(), ['branch_id' => 'branch_id'])->viaTable('mtb_staff_role', ['staff_id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(\common\models\Company::className(), ['company_id' => 'company_id']);
    }

    public function getExpired()
    {
        return (strtotime($this->expire_date) <= time());
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventories()
    {
        return $this->hasMany(Inventory::className(), ['staff_id' => 'staff_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManufactures()
    {
        return $this->hasMany(Manufacture::className(), ['staff_id' => 'staff_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialInventories()
    {
        return $this->hasMany(MaterialInventory::className(), ['staff_id' => 'staff_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialStorages()
    {
        return $this->hasMany(MaterialStorage::className(), ['staff_id' => 'staff_id']);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return sprintf("%s %s", $this->name01, $this->name02);
    }

    public function getPasswordResetToken()
    {
        return $this->hasOne(\common\models\PasswordResetToken::className(), ['email' => 'email']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(StaffRole::className(), ['staff_id' => 'staff_id']);
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return null;
    }

    public function generatePasswordResetToken()
    {
        $transaction = Yii::$app->db->beginTransaction();

        if($this->passwordResetToken)
        {
            $model = $this->passwordResetToken;
        }
        else
        {
            $model = new \common\models\PasswordResetToken();
            $model->email = $this->email;
        }

        if($model->save())
        {
            $transaction->commit();
        }
        else
        {
            Yii::error("failed in PasswordResetToken::save()");

            $transaction->rollBack();
            return false;
        }

        return $model->token;
    }

    /**
     * @brief check if any of roles exist
     * @return bool
     */
    public function hasRole($names)
    {
        $roles = \yii\helpers\ArrayHelper::getColumn($this->roles, 'role.name');

        if(! is_array($names)){ $names = [$names]; }

        foreach($names as $name)
            if(in_array($name, $roles)){ return true; }

        return false;
    }

    public function addRole($name)
    {
        if($this->hasRole([$name]))
            return true;

        if(! $role = Role::findOne(['name' => $name]))
            throw new \yii\base\Exception('Role::findOne() failed');

        $model = new StaffRole([
            'staff_id' => $this->staff_id,
            'role_id'  => $role->role_id,
        ]);
        
        return $model->save();
    }

    public function removePasswordResetToken()
    {
        $row = \common\models\PasswordResetToken::findOne(['email'=> $this->email]);
        if($row)
            $row->delete();
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function beforeValidate()
    {
        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {

        if (parent::beforeSave($insert))
        {
            if (true == $insert)
            {
                $this->auth_key    = Yii::$app->getSecurity()->generateRandomString();
                $this->expire_date = self::DATETIME_MAX;
            }
            elseif(strtotime($this->expire_date) < time())
            {
                $this->addError('expire_date', "有効期限を過ぎたアカウントは編集できません");
                return false;
            }
            $this->update_date = date('Y-m-d H:i:s'); // now

            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($insert)
            Yii::info(sprintf("%s is created: (user=%s)",
                              $this->name,
                              Yii::$app->user->id),
                      $this->className());
        else
            Yii::info(sprintf("%s is up to date: %s (user=%s)",
                              $this->name,
                              implode(',',array_keys($changedAttributes)),
                              Yii::$app->user->id),
                      $this->className());
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        // do not allow delete any Staff
        return false;
    }
}

class StaffQuery extends \yii\db\ActiveQuery
{
    public function active($state = true)
    {
        $now = new \yii\db\Expression('NOW()');

        if($state)
            return $this->andWhere(['>',  'expire_date', $now]);
        else
            return $this->andWhere(['<=', 'expire_date', $now]);
    }
}
