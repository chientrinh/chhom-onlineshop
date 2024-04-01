<?php
namespace frontend\modules\signup\models;

use Yii;

/**
 * Signup form
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/signup/models/SignupForm.php $
 * $Id: SignupForm.php 3857 2018-05-01 02:06:42Z mori $
 */

class SignupForm extends \common\models\Customer
{
    public $password1;
    public $password2;
    public $birth_y;
    public $birth_m;
    public $birth_d;
    public $addrCandidate = null;

    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['password1','password2'], 'required'],
                ['password1', 'string', 'min' => 6],
                ['password2', 'compare','compareAttribute'=>'password1','message'=>"確認用のパスワードが一致しません"],
                ['birth_y',  'integer', 'skipOnEmpty'=>true, 'min'=>1900,'max'=> date('Y')],
                ['birth_m',  'integer', 'skipOnEmpty'=>true, 'min'=>1,   'max'=> 12],
                ['birth_d',  'integer', 'skipOnEmpty'=>true, 'min'=>1,   'max'=> 31],
            ]);
    }
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'birth_y' => "年",
                'birth_m' => "月",
                'birth_d' => "日",
                'password1' => "パスワード",
                'password2' => "パスワードの確認",
            ]
        );
    }

    public function attributeHints()
    {
        return array_merge(parent::attributeHints(), [
            'password1' => '英数字6文字以上をご登録ください。',
        ]);
    }

    /**
     * @retrutn bool
     */
    public function beforeValidate()
    {
        $this->birth = $this->getBirth(); // rebuild from birth_{y,m,d}

        foreach(['birth_y','birth_m','birth_d'] as $attr)
            if(0 == $this->$attr)
                $this->$attr = null;

        return parent::beforeValidate();
    }

    public function getBirth()
    {
        return sprintf('%04d-%02d-%02d',
                       $this->birth_y ? $this->birth_y : 0,
                       $this->birth_m ? $this->birth_m : 0,
                       $this->birth_d ? $this->birth_d : 0);
    }

    public function setBirth($str)
    {
        if(preg_match('#([0-9]+)/([0-9]+)/([0-9]+)#', $str, $match) ||
           preg_match('#([0-9]+)-([0-9]+)-([0-9]+)#', $str, $match))
        {
            $this->birth_y = (int) $match[1];
            $this->birth_m = (int) $match[2];
            $this->birth_d = (int) $match[3];
        }
    }

    /**
     * @retrutn bool
     */
    public function load($data, $formName = null)
    {
        if(parent::load($data, $formName))
        {
            if(is_array($data) && array_key_exists('scenario', $data))
                $this->scenario = $data['scenario'];

            $this->setBirth($this->birth); // update birth_{y,m,d}

            return true;
        }

        return false;
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup($runValidation = true)
    {
        if($runValidation && ! $this->validate())
            return null;

        if('zip2addr' == $this->scenario)
        {
            $this->addrCandidate = $this->zip2addr();
            return null;
        }
        if('review' == $this->scenario)
        {
            return null;
        }

        $customer = new \common\models\Customer();
        $customer->setAttributes($this->attributes);
        $customer->setPassword($this->password1);
        $customer->sex_id = $this->sex_id;
        $customer->birth  = $this->getBirth();

        if(! $this->getBehavior('membercode'))
            // the behavior is detached, so do for new Model
            $customer->detachBehavior('membercode');

        if($customer->save($runValidation))
        {
            return $customer;
        }

        return null;
    }

    /**
     * @retrutn bool
     */
    public function zip2addr()
    {
        if($this->scenario != parent::SCENARIO_ZIP2ADDR)
            return false;

        $addr01 = parent::zip2addr();
        if(! $addr01)
            return false;

        if(is_array($addr01) && (1 < count($addr01)))
            $this->addrCandidate = $addr01;

        return true;
    }

}
