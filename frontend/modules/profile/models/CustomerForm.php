<?php
namespace frontend\modules\profile\models;

use Yii;

/**
 * Customer Update Form
 * handled by the customer themselves
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/models/CustomerForm.php $
 * $Id: CustomerForm.php 1381 2015-08-27 00:22:32Z mori $
 */

class CustomerForm extends \common\models\Customer
{
    public $password1;
    public $password2;
    public $addrCandidate = null;
    private $_y;
    private $_m;
    private $_d;

    /**
     * @inheritdoc
     */
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
                ['password1', 'string', 'min' => 6],
                ['password2', 'compare','compareAttribute'=>'password1','message'=>"確認用のパスワードが一致しません"],
                ['birth_y',  'integer', 'skipOnEmpty'=>true, 'min'=>1900,'max'=> date('Y')],
                ['birth_m',  'integer', 'skipOnEmpty'=>true, 'min'=>1,   'max'=> 12],
                ['birth_d',  'integer', 'skipOnEmpty'=>true, 'min'=>1,   'max'=> 31],
                [['birth_y','birth_m','birth_d'], 'safe'],
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
            'password1' => 'パスワードを変更する場合のみご入力 (英数字6文字以上) ください。',
        ]);
    }

    public function attributes()
    {
        return parent::attributes();
    }

    public function getBirth_y()
    {
        if(preg_match('#([0-9]+)/([0-9]+)/([0-9]+)#', $this->birth, $match) ||
           preg_match('#([0-9]+)-([0-9]+)-([0-9]+)#', $this->birth, $match))
        {
            $this->_y = (int) $match[1];
            $this->_m = (int) $match[2];
            $this->_d = (int) $match[3];
        }
        if(0 == $this->_y) $this->_y = '';
        if(0 == $this->_m) $this->_m = '';
        if(0 == $this->_d) $this->_d = '';

        return $this->_y;
    }

    public function getBirth_m()
    {
        if(isset($this->_m))
            return $this->_m;

        $this->getBirth_y();

        return $this->_m;
    }

    public function getBirth_d()
    {
        if(isset($this->_d))
            return $this->_d;

        $this->getBirth_y();

        return $this->_d;
    }

    public function setBirth_y($val)
    {
        $this->_y = $val;
        $this->birth = sprintf('%04d-%02d-%02d', $this->_y, $this->_m, $this->_d);
    }

    public function setBirth_m($val)
    {
        $this->_m = $val;
        $this->birth = sprintf('%04d-%02d-%02d', $this->_y, $this->_m, $this->_d);
    }

    public function setBirth_d($val)
    {
        $this->_d = $val;
        $this->birth = sprintf('%04d-%02d-%02d', $this->_y, $this->_m, $this->_d);
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

            return true;
        }

        return false;
    }

    public function beforeSave($insert)
    {
        if(! $this->hasErrors() && strlen($this->password1))
            $this->setPassword($this->password1);

        return parent::beforeSave($insert);
    }

}
