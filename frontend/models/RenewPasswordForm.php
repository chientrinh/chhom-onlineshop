<?php
namespace frontend\models;

use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * Password reset form
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/models/RenewPasswordForm.php $
 * $Id: RenewPasswordForm.php 3379 2017-06-02 14:43:41Z naito $
 */

class RenewPasswordForm extends Model
{
    public $email;
    public $password;
    public $retype;

    /**
     * @var \common\models\Customer
     */
    private $_user;


    /**
     * Creates a form model given a token.
     *
     * @param  string                          $token
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Password reset token cannot be blank.');
        }
//       $user = new \common\models\Customer;
        $user = new \console\models\Customer;
        $this->_user = $user->findByPasswordResetToken($token);
        
        if (!$this->_user) {
            throw new InvalidParamException('Wrong password reset token.');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email','password','retype'], 'required'],
            ['email',                       'email'],
            [['password','retype'],         'string', 'min' => 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email'    => "メールアドレス",
            'password' => "新しいパスワード",
            'retype'   => "確認のため、もう一度入力してください",
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        if($this->password != $this->retype)
            $this->addError('retype', "確認用のパスワードが一致しません");

        if($this->email != $this->_user->email)
            $this->addError('email', "登録されているメールアドレスと一致しません");

        return parent::afterValidate();
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->password = $this->password;
        $user->removePasswordResetToken();

        return $user->save();
    }
}
