<?php
namespace backend\models;

use Yii;

/**
 * Login form
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/LoginForm.php $
 * $Id: LoginForm.php 2235 2016-03-12 06:16:28Z mori $
 */
class LoginForm extends \yii\base\Model
{
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
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
            'email'      => "メールアドレス",
            'password'   => "パスワード",
            'rememberMe' => "パスワードを保存する",
        ];
    }

    /**
     * @return string as $this->username
     * required by vendor/yiisoft/yii2/base/Component.php
     */
    public function getUsername()
    {
        return $this->email;
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
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
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
        $duration = 3600 * 24 * 30; // 30 days
        if(! Yii::$app->user->login($user, $this->rememberMe ? $duration : 0))
            return false;

        Yii::$app->authManager->assignRoles($user->id);

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
           $this->_user = Staff::findByEmail($this->email);

        return $this->_user;
    }
}
