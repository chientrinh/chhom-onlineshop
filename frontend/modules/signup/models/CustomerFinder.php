<?php
namespace frontend\modules\signup\models;

use Yii;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/signup/models/CustomerFinder.php $
 * $Id: CustomerFinder.php 1231 2015-08-05 07:47:41Z mori $
 */

class CustomerFinder extends \yii\base\Model
{
    public $company;
    public $target;
    public $agreed;
    public $userid;
    public $password;

    //$model = new \common\models\webdb\CustomerSearchForm();

    public function rules()
    {
        return [
            [['agreed','userid','password'],'required'],
            ['agreed','boolean'],
            ['target', 'compare', 'operator' => '==', 'compareValue' => 'ecorange', 'skipOnEmpty'=>true],
            [['userid','password'],'string','min'=>1,'max'=>255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'agreed'   => 'プライバシーポリシーに同意する',
            'userid'   => 'ID',
            'password' => 'パスワード',
        ];
    }

    public function loadModel($attributes)
    {
        switch($this->company->key)
        {
        case 'ty':
            $model = \common\models\eccube\SearchCustomer::findOne($attributes['customer_id']);
            break;
        case 'hj':
            $model = \common\models\webdb18\SearchCustomer::findOne($attributes['customerid']);
            break;
        case 'hp':
            $model = \common\models\webdb20\SearchCustomer::findOne($attributes['customerid']);
            break;

        case 'he':
            if(isset($attributes['customerid']))
                $model = \common\models\webdb20\SearchCustomer::findOne($attributes['customerid']);
            elseif(isset($attributes['customer_id']))
                $model = \common\models\ecorange\SearchCustomer::findOne($attributes['customer_id']);
            break;

        default:
            return false;
            break;
        }

        return $model;
    }

    public function search()
    {
        if(! $this->validate())
            return false;

        if('ecorange' === $this->target)
            $model = $this->searchFromEcOrange();

        else
        switch($this->company->key)
        {
        case 'ty':
            $model = $this->searchFromEcCube();
            break;
        case 'hj':
            $model = $this->searchFromWebdb18();
            break;
        case 'hp':
            $model = $this->searchFromWebdb20();
            break;
        case 'he':
            $model = $this->searchFromMembercode();
            break;
        default:
            return false;
            break;
        }

        return $model;
    }

    private function searchFromEcCube()
    {
        $model = \common\models\eccube\SearchCustomer::findFromEmailAndPassword(
            $this->userid,
            $this->password
        );
        if(! $model)
            return false;

        return $model;
    }

    private function searchFromEcOrange()
    {
        $model = \common\models\ecorange\SearchCustomer::findFromEmailAndPassword(
            $this->userid,
            $this->password
        );
        if(! $model)
            return false;

        return $model;
    }

    private function searchFromWebdb18()
    {
        // select from webdb first
        $model = \common\models\webdb18\SearchCustomer::findFromEmailAndPassword(
            $this->userid,
            $this->password
        );
        if(! $model)
            return false;

        return $model;
    }

    private function searchFromWebdb20()
    {
        // select from webdb first
        $model = \common\models\webdb20\SearchCustomer::findFromEmailAndPassword(
            $this->userid,
            $this->password
        );
        if(! $model)
            return false;

        return $model;
    }

    private function searchFromMembercode()
    {
        $membercode = \common\models\Membercode::find()->where([
            'code'      => $this->userid,
            'pw'        => $this->password,
            'directive' => 'webdb20',
        ])
        ->andWhere('migrate_id >= 1')
        ->one();

        if(! $membercode)
            return false;

        return \common\models\webdb20\SearchCustomer::findOne($membercode->migrate_id);
    }
    

}
