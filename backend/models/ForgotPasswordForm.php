<?php
namespace backend\models;

/**
 * Password reset request form
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/ForgotPasswordForm.php $
 * $Id: ForgotPasswordForm.php 1827 2015-11-27 08:44:44Z mori $
 */

class ForgotPasswordForm extends \yii\base\Model
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
             'targetClass' => 'backend\models\Staff',
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
        return \backend\models\LoginForm::attributeLabels();
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        $staff = \backend\models\Staff::find()->where(['email' => $this->email])->one();

        if ($staff)
        {
            $token         = $staff->generatePasswordResetToken();
            $subject       = sprintf("%s パスワード初期化のご案内", \Yii::$app->name);
            $mailer        = \Yii::$app->mailer;
            $mailer->table = $staff->tableName();
            $mailer->pkey  = $staff->staff_id;

            return $mailer->compose(
                ['text' => 'passwordResetToken-text'],
                ['user' => $staff, 'token'=>$token])
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name ])
                    ->setTo($this->email)
                    ->setSubject($subject)
                    ->send();
        }

        return false;
    }
}
