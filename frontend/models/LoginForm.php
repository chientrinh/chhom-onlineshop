<?php
namespace frontend\models;

use Yii;
use \common\models\Customer;

/**
 * Login form
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/models/LoginForm.php $
 * $Id: LoginForm.php 3954 2018-07-04 06:08:28Z mori $
 */
class LoginForm extends \yii\base\Model
{
    public $email;
    public $password;
    public $rememberMe = true;
    public $campaign_code;

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
            ['campaign_code', 'validateCode']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email'      => "ID または メールアドレス",
            'password'   => "パスワード",
            'rememberMe' => "パスワードを保存する",
            'campaign_code' => 'キャンペーンコード'
        ];
    }
    
    public function attributeHints() {
        return [
            'campaign_code' => '限定商品のキャンペーンコードをお持ちの場合、入力してください'
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
        if($this->hasErrors())
            return false;

        $this->addError($attribute, 'IDまたはパスワードが異なっています。');

        if(! $user = $this->getUser())
            return false;

        if($user->isExpired())
            return false;

        if($user->parent) // is child member
            return false;

        if(! in_array($this->email, [$user->email, $user->code]))
            return false;

        if(($user->code === $this->email) && ($user->membercode->pw === $this->password))
        {
            $this->addError($attribute,'yet another error1');
            $this->clearErrors($attribute);
            return true;
        }

        if(! $user->password_hash)
            return false;

        if($user->validatePassword($this->password))
        {
            $this->addError($attribute,'yet another error1');
            $this->clearErrors($attribute);
            return true;
        }

        return false;
    }
    
    /**
     * Validates the campaign_code.
     * This method serves as the inline validation for campaign_code.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateCode($attribute)
    {
        if($this->hasErrors())
            return false;
        
        if (!$this->campaign_code) {
            return true;
        }
        
        $campaign = \common\models\EventCampaign::find()->active()->andWhere(['campaign_code' => $this->campaign_code])->one();
        if (!$campaign) {
            $this->addError($attribute, 'キャンペーンコードが異なっています。');
            return false;
        }
        
        return true;
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        $duration = 3600 * 24; // 24 Hours

        if (! $this->validate())
            return false;

        if(! Yii::$app->user->login($this->getUser(), $this->rememberMe ? $duration : 0))
            return false;

        $this->afterLogin();

        return true;
    }

    /**
     * Finds user by [[username]] or [[membercode]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if($this->_user !== false)
            return $this->_user;

        if(is_numeric($this->email))
        {
            if($membercode = \common\models\Membercode::find()
                           ->where(['code'=>$this->email])
                           ->andWhere(['not',['customer_id'=>null]])->one()
            )
                $this->_user = Customer::find()->where(['customer_id'=>$membercode->customer_id])->active()->one();
        }
        else
            $this->_user = Customer::find()->where(['email'=>$this->email])->active()->one();

        return $this->_user;
    }

    private function afterLogin()
    {
        if(! $this->_user)
            throw new \yii\base\Exception('user is undefined but logged in');

        $this->_user->validate();

        $campaign_code = $this->campaign_code ? : false;
        Yii::$app->session->set('campaign_code', $campaign_code);

        if($this->_user->hasErrors())
            Yii::$app->session->addFlash('warning',
                                         '<div class="row text-center">'
                                       . '<h2>お願い</h2>'
                                       . "<p>お客様の会員登録が完了していません。お買い物の前に必要な情報を入力してください。</p>"
                                       . \yii\helpers\Html::a('登録ページへ行く',['/profile/default/update'],['class'=>'btn btn-warning'])
                                       . '</div>'
            );

    }
}
