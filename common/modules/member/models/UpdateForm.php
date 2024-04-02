<?php
namespace common\modules\member\models;

use Yii;
use \common\models\Customer;
use \common\models\CustomerMembership;
use \common\models\Membership;
use \common\models\Payment;
use \common\models\Product;

/**
 * Customer Create Form
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/member/models/UpdateForm.php $
 * $Id: UpdateForm.php 2987 2016-10-19 05:10:43Z mori $
 */

class UpdateForm extends \yii\base\Model
{
    public $customer_id;
    public $membership_id;
    public $issues;
    public $payment_id;
    public $paid;

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
        return [
            [['customer_id','membership_id','payment_id'],'required'],
            ['customer_id','exist',
             'targetClass' => Customer::className(),
             'filter'      => ['>=', 'expire_date', new \yii\db\Expression('NOW()')],
             'message'     => "無効な顧客が指定されました",
            ],
            [['customer_id'],'allowCustomerToExtend', 'skipOnError' => true],
            ['membership_id','exist','targetClass'=>Membership::className()],
            ['payment_id',   'exist','targetClass'=>Payment::className()],
            ['issues', 'each','rule'=>['exist','targetClass'=>Product::className(),'targetAttribute'=>'product_id']],
            ['membership_id', 'in', 'range'=>[Membership::PKEY_TORANOKO_GENERIC,
                                              Membership::PKEY_TORANOKO_NETWORK]],
            ['payment_id',    'in', 'range'=>[Payment::PKEY_CASH,
                                              Payment::PKEY_BANK_TRANSFER]],
            ['paid', 'default', 'value'=>0],
            ['paid', 'integer', 'min'=>0,'max'=>1],
            ['paid', 'in','range'=>[1], 'when'=>function($model){ return ($model->payment_id === Payment::PKEY_CASH); }],
            ['issues','validateIssues'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'customer_id'   => 'お客様',
            'membership_id' => '会員種別',
            'issues'        => 'とらのこ会報誌『Oasis』',
            'payment_id'    => 'お支払い方法',
            'paid'          => '入金',
        ];
    }

    public function attributeHints()
    {
        return
            [
                'issues'=> "いま、更新と同時にお渡しできる冊子があればチェックしてください",
            ];
    }

    public function allowCustomerToExtend($attr, $params)
    {
        $query = CustomerMembership::find()
            ->toranoko()
            ->andWhere(['customer_id' => $this->customer_id])
            ->andWhere(['>','expire_date', new \yii\db\Expression('DATE_ADD(NOW(), INTERVAL 1 YEAR)')]) // 365 日以上未来の会員権がある
            ->orderBy(['expire_date' => SORT_DESC]);

        if(! $mship = $query->one())
            return true;

        $msg = sprintf('会員資格（%s）は %s まで有効です。一年以上有効なお客様につき延長は必要ありません。',
                       $mship->name,
                       date('Y-m-d', strtotime($mship->expire_date)));

        $this->addError($attr, $msg);

        return false;
    }

    public function getProduct_id()
    {
        if(Membership::PKEY_TORANOKO_GENERIC == $this->membership_id)
            return Product::PKEY_TORANOKO_G_ADMISSION;

        if(Membership::PKEY_TORANOKO_NETWORK == $this->membership_id)
            return Product::PKEY_TORANOKO_N_ADMISSION;

        return null;
    }

    public function validateIssues($attr, $param)
    {
        if(! $this->issues)
            return true;

        if(Membership::PKEY_TORANOKO_NETWORK == $this->membership_id)
            $this->addError($attr, "ネットワーク会員には冊子をお渡ししない決まりです");

        return $this->hasErrors($attr);
    }
}
