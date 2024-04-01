<?php
namespace backend\models;

/**
 * Password reset form
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/ResetPasswordForm.php $
 * $Id: ResetPasswordForm.php 890 2015-04-15 10:50:02Z mori $
 */

class ResetPasswordForm extends \yii\base\Model
{
    public $password;

    /**
     * @var \common\models\User
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
            throw new \yii\base\InvalidParamException('Password reset token cannot be blank.');
        }
        $this->_user = Staff::findByPasswordResetToken($token);
        if (!$this->_user) {
            throw new \yii\base\InvalidParamException('Wrong password reset token.');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
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
