<?php
namespace backend\models;

use Yii;

/**
 * Auth form
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/AuthForm.php $
 * $Id: LoginForm.php 2235 2016-03-12 06:16:28Z mori $
 */
class AuthForm extends \yii\base\Model
{
    public $password;
    public $rememberMe = true;

    private $_user = false;
    const PASSWORD = '2017';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password'   => "パスワード",
            'rememberMe' => "パスワードを保存する",
        ];
    }


    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (self::PASSWORD != $this->password) {
                $this->addError($attribute, 'Incorrect password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if(! $this->validate())
            return false;

        $user     = $this->getUser();

        return true;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if($this->_user === false)
           $this->_user = Yii::$app->user->identity;

        return $this->_user;
    }
}


