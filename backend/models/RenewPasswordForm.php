<?php
namespace backend\models;

/**
 * Password reset form
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/RenewPasswordForm.php $
 * $Id: RenewPasswordForm.php 897 2015-04-17 01:24:18Z mori $
 */

class RenewPasswordForm extends \yii\base\Model
{
    public $email;
    public $password;
    public $retype;

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

        $staff = new \backend\models\Staff;
        $this->_user = $staff->findByPasswordResetToken($token);
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
            'retype'   => "確認のためパスワードを再入力",
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
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        if($user->save() && $this->sendEmail())
        {
            return true;
        }

        return false;
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        $user    = $this->_user;
        $subject = sprintf("%s パスワード初期化完了", \Yii::$app->name);

        $ret = \Yii::$app->mailer->compose(
            ['text' => 'passwordRenewed-text'],
            ['user' => $user])
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name ])
            ->setTo($user->email)
            ->setSubject($subject)
            ->send();

        if(! $ret)
        {
            Yii::error("mailer failed");
            return false;
        }

        return true;
    }

}
