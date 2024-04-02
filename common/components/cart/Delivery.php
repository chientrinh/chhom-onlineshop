<?php

namespace common\components\cart;
use Yii;

/**
 * Delivery model for Shopping Cart
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/Delivery.php $
 * $Id: Delivery.php 4248 2020-04-24 16:29:45Z mori $
 */

class Delivery extends \yii\base\Model
{
    public  $gift  = 0;
    public  $code  = "";
    private $_date = null;
    private $_time = null;
    private $_destination = null;

    public function rules()
    {
        return [
            [['name01','name02','zip01','zip02','pref_id','addr01','addr02','tel01','tel02','tel03'], 'required', 'when' => function(){ return 'echom-frontend' != Yii::$app->id;} ],
            [['name01','name02','tel01','tel02','tel03'], 'required', 'skipOnError' => true, 'when'=>function(){ return 'echom-frontend' == Yii::$app->id;} ],
            ['date', 'date', 'format'=>'yyyy-M-d', 'skipOnEmpty'=>true],
            ['time_id', 'exist', 'targetClass'=>'\common\models\DeliveryTime', 'targetAttribute'=>'time_id', 'skipOnEmpty' => true],
            [['name01','name02','zip01','zip02','pref_id','addr01','addr02','tel01','tel02','tel03','gift', 'code','email'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return ['name01','name02','zip01','zip02','pref_id','addr01','addr02','tel01','tel02','tel03',
                'date','time_id','gift','code','email'];
    }

    public function init()
    {
        $this->_time        = new \common\models\DeliveryTime();
        $this->_destination = new \common\models\PurchaseDelivery();
    }

    // wrapper functions for PurchaseDelivery
    public function getName()   { return $this->_destination->name;   }
    public function getName01() { return $this->_destination->name01; }
    public function getName02() { return $this->_destination->name02; }
    public function getZip()    { return $this->_destination->zip;    }
    public function getZip01()  { return $this->_destination->zip01;  }
    public function getZip02()  { return $this->_destination->zip02;  }
    public function getPref_id(){ return $this->_destination->pref_id;}
    public function getAddr()   { return $this->_destination->addr;   }
    public function getAddr01() { return $this->_destination->addr01; }
    public function getAddr02() { return $this->_destination->addr02; }
    public function getTel()    { return $this->_destination->tel;    }
    public function getTel01()  { return $this->_destination->tel01;  }
    public function getTel02()  { return $this->_destination->tel02;  }
    public function getTel03()  { return $this->_destination->tel03;  }
    public function getCode()  { return $this->_destination->code;  }
    public function getEmail()  { return $this->_destination->email;  }

    public function getDate()
    {
        return $this->_date;
    }

    public function getDateString()
    {
        return Yii::$app->formatter->asDate($this->_date, 'php:Y年m月d日(D)');
    }

    public function getDateTimeString()
    {
        if(! $this->_date && ! $this->time_id)
            $str = "(指定なし)";
        elseif(! $this->_date)
            $str = sprintf("(日付指定なし) %s", $this->time->name);
        elseif(! $this->time_id)
            $str = sprintf("%s (時間指定なし)", $this->dateString);
        else
            $str = sprintf("%s %s", $this->dateString, $this->time->name);

        return $str;
    }

    public function getModel()
    {
        $this->_destination->expect_date = ($this->date ? $this->date : null);
        $this->_destination->expect_time = ($this->_time->time_id ? $this->_time->time_id : null);
        $this->_destination->gift        = $this->gift;
        $this->_destination->code        = $this->code;

        return $this->_destination;
    }

    public function setPurchase_id($value)
    {
        $this->_destination->purchase_id = $value;
    }

    public function getTime()
    {
        return $this->_time;
    }

    public function getTime_id()
    {
        return $this->_time->time_id;
    }

    public function setName01($value) { $this->_destination->name01 = $value; }
    public function setName02($value) { $this->_destination->name02 = $value; }
    public function setZip01($value)  { $this->_destination->zip01  = $value; }
    public function setZip02($value)  { $this->_destination->zip02  = $value; }
    public function setPref_id($value){ $this->_destination->pref_id= $value; }
    public function setAddr01($value) { $this->_destination->addr01 = $value; }
    public function setAddr02($value) { $this->_destination->addr02 = $value; }
    public function setTel01($value)  { $this->_destination->tel01  = $value; }
    public function setTel02($value)  { $this->_destination->tel02  = $value; }
    public function setTel03($value)  { $this->_destination->tel03  = $value; }
    public function setEmail($value)  { $this->_destination->email  = $value; }

    /* @return bool */
    public function setDate($str)
    {
        if(0 == strlen($str))
            $this->_date = null;
        else
            $this->_date = $str;

        return $this->validate(['date']);
    }

    public function setTime($time_id)
    {
        if(0 == $time_id)
        {
            $this->_time = new \common\models\DeliveryTime();
            return true;
        }

        $model = \common\models\DeliveryTime::find()->where(['time_id'=>$time_id])->one();
        if(! $model)
            return false;

        $this->_time = $model;

        return true;
    }

    public function setTime_id($time_id)
    {
        return $this->setTime($time_id);
    }

    public function setDestination($model)
    {
        return $this->_destination->load($model->attributes, '');
    }

}

