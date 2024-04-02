<?php
namespace common\modules\member\models;

use Yii;
use \common\models\Membership;
use \common\models\Product;

/**
 * Customer Create Form
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/member/models/CreateForm.php $
 * $Id: CreateForm.php 3072 2016-11-09 05:43:48Z mori $
 */

class CreateForm extends \common\models\Customer implements \yii\web\IdentityInterface
{
    const SCENARIO_BACKEND    = 'app-backend';

    public $password1;
    public $password2;
    public $membership_id;
    public $addrCandidate = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    public function scenario()
    {
        return [
            \yii\db\ActiveRecord::SCENARIO_DEFAULT,
            self::SCENARIO_BACKEND,
        ];
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
                ['password2', 'compare','compareAttribute'=>'password1','message'=>"パスワードと一致しません"],
                ['membership_id','exist', 'targetClass'=>\common\models\Membership::className() ],
            ]);
    }

    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'membership_id' => '会員種別',
                'password1' => "パスワード",
                'password2' => "パスワードの確認",
            ]
        );
    }

    public function attributeHints()
    {
        return array_merge(
            parent::attributeHints(),
            [
                'birth'     => '',
                'password2' => '英数字6文字以上をご登録ください。',
            ]
        );
    }

    public function beforeSave($insert)
    {
        if(! $this->hasErrors() && strlen($this->password1))
            $this->setPassword($this->password1);

        return parent::beforeSave($insert);
    }

    public function getProduct_id()
    {
        if(Membership::PKEY_TORANOKO_GENERIC == $this->membership_id)
            return Product::PKEY_TORANOKO_G_ADMISSION;

        if(Membership::PKEY_TORANOKO_NETWORK == $this->membership_id)
            return Product::PKEY_TORANOKO_N_ADMISSION;

        return null;
    }
}
