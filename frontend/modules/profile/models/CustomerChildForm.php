<?php
namespace frontend\modules\profile\models;

use Yii;

/**
 * form to insert customer's child
 *
 * $URL: http://test-webhj.homoeopathy.co.jp:8000/svn/MALL/frontend/modules/profile/models/CustomerForm.php $
 * $Id: CustomerForm.php 1085 2015-06-13 10:18:26Z mori $
 */

class CustomerChildForm extends CustomerForm
{
    public function init()
    {
        parent::init();

        $this->scenario = \common\models\Customer::SCENARIO_CHILDMEMBER;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id','name01','name02','kana01','kana02','sex_id'], 'required'],
            [['parent_id'], 'exist', 'targetClass'=> \common\models\Customer::className(), 'targetAttribute'=>'customer_id'],
            [['name01','name02','kana01','kana02'], 'trim'],
            [['name01','name02','kana01','kana02'], 'string', 'min'=> 1],
            ['name01', 'validateNameKana'],
            ['sex_id',   'in',      'range'=> [0,1,2,9]],
            ['birth_y',  'integer', 'skipOnEmpty'=>true, 'min'=>1900,'max'=> date('Y')],
            ['birth_m',  'integer', 'skipOnEmpty'=>true, 'min'=>1,   'max'=> 12],
            ['birth_d',  'integer', 'skipOnEmpty'=>true, 'min'=>1,   'max'=> 31],
            [['birth_y','birth_m','birth_d'], 'safe'],
        ];
    }

    /* @brief check if the child name already exists */
    public function validateNameKana($attr, $params)
    {
        $parent = Yii::$app->user->identity;

        if(! $parent->children) // no child
            return true;

        foreach($parent->children as $child)
        {
            if($this->customer_id == $child->customer_id)
                continue;

            if($this->name == $child->name)
            {
                $this->addError('name02', "この名前はすでに登録されています");
                return false;
            }
            if($this->kana == $child->kana)
            {
                $this->addError('kana02', "この名前はすでに登録されています");
                return false;
            }
        }
        return true;
    }

    public function beforeValidate()
    {
        $this->scenario = \common\models\Customer::SCENARIO_CHILDMEMBER;

        return parent::beforeValidate();
    }
}
