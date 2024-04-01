<?php
namespace frontend\models;

use yii\base\Model;

/**
 * Password reset request form
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/models/ForgotPasswordForm.php $
 * $Id: ForgotPasswordForm.php 2984 2016-10-19 00:58:42Z mori $
 */

class ForgotPasswordForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
             'targetClass' => 'common\models\Customer',
             'filter'      => ['>=', 'expire_date', new \yii\db\Expression('NOW()')],
             'message'     => "そのようなメールアドレスは該当しません",
            ],
        ];
    }

    

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return LoginForm::attributeLabels();
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user Customer */
        $user = \common\models\Customer::findByEmail($this->email);

        if ($user) {
            $token   = $user->generatePasswordResetToken();
            $subject = sprintf("%s パスワード初期化のご案内", \Yii::$app->name);

            $mailer = \Yii::$app->mailer;
            $mailer->table = \common\models\Customer::tableName();
            $mailer->pkey  = $user->id;

            return $mailer->compose(
                ['text' => 'passwordResetToken-text'],
                ['user' => $user, 'token'=>$token])
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name ])
                ->setTo($this->email)
                ->setSubject($subject)
                ->send();
        }

        Yii::error('Unexpected error');
        return false;
    }
}
