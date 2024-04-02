<?php

namespace common\components\cart;
use Yii;

/**
 * Abstract of Item in Shopping Cart
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/CartItem.php $
 * $Id: CartItem.php 2717 2016-07-15 03:12:07Z naito $
 */

abstract class CartItem extends \yii\base\Model
{
    const TYPE_PRODUCT = 'dtb_product';
    const TYPE_REMEDY  = 'mtb_remedy';

    protected $_model    = null;
    protected $_type     = null;
    protected $_qty      = null;
    protected $_price    = null;
    protected $_discount = null;
    protected $_point    = null;
    protected $_recipe_id = null;
    protected $_campaign_id = null;
    protected $_is_wholesale = null;
    public $unit_price;
    public $unit_tax;
    public $point_consume_rate;
    public $point_consume;
    public $tax_rate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','code','name','price','charge'], 'required'],
            ['id',    'integer', 'skipOnEmpty' => true],
            ['qty',   'integer', 'min' => 1],
            ['price', 'integer', 'min' => 0],
            ['charge','integer', 'min' => 0],
            [['discountRate','pointRate','point_consume_rate'], 'integer', 'min' => 0, 'max'=> 100],
            // ['discountAmount', 'compare', 'compareAttribute' => 'price', 'operator' => '<='],
            // ['pointAmount', 'compare', 'compareAttribute' => 'price', 'operator' => '<='],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return(['id','qty','code','name','price','charge','discountRate','discountAmount','pointRate','pointAmount','recipe_id', 'campaign_id', 'is_wholesale','unit_price','unit_tax','point_consume_rate','point_consume']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code'   => "コード",
            'recipe_id' => "適用書ID",
            'campaign_id' => "キャンペーンID",
            'is_wholesale' => "卸売り",
            'name'   => "品名",
            'price'  => "価格",
            'unitPrice' => "販売価格",
            'unitTax'   => "消費税",
            'discountRate'   => "値引(%)",
            'discountAmount' => "値引(￥)",
            'pointRate'    => "Pt_rate",
            'pointAmount'    => "Pt",
            'point_consume_rate' => "ポイント使用割合",
            'point_consume'  => "ポイント使用額",
            'qty'    => "数量",
            'charge' => "小計",
        ];
    }

    public function init()
    {
        parent::init();

        if(! isset($this->_qty))
            $this->_qty      = 1;

        $this->_discount = new ItemDiscount();
        $this->_point    = new ItemPoint();
    }

    /**
     * @param $target another CartItem
     * @return bool
     */
    public function compare($target)
    {
        if($this->id !== $target->id)
            return false;

        if($this->recipe_id !== $target->recipe_id)
            return false;

        if($this->campaign_id !== $target->campaign_id)
            return false;

        if($this->is_wholesale !== $target->is_wholesale)
            return false;
        
        if($this->type !== $target->type)
            return false;

        if($this->isProduct())
        {
            if($this->name === $target->name)
                return true;
            else
               return false;
        }

        if($this->potency_id !== $target->potency_id)
            return false;

        if($this->vial_id !== $target->vial_id)
            return false;

        return true;
    }

    public function getBasePrice()
    {
        return ($this->price * $this->qty);
    }

    public function getCategory()
    {
        return $this->_model->category;
    }

    public function getCharge()
    {
        if(! $this->discountRate && ! $this->discountAmount)
            return $this->basePrice;

        if($this->discountAmount)
            return $this->basePrice - ($this->discountAmount * $this->qty);

        // else if (0 < $discountRate)
        $amount = floor($this->discountRate / 100);
        return $this->basePrice - ($amount * $this->qty);
    }

    public function getCode()
    {
        return $this->_model->code;
    }

    /**
     * 販売価格（１商品あたり）
     **/
    public function getUnitPrice()
    {
        if($this->discountAmount)
            return $this->unit_price = $this->price - $this->discountAmount;

        return $this->unit_price = $this->price;
    }

    public function setUnitPrice($val)
    {
        $this->unit_price = $val;
    }

    /**
     * 消費税（１商品あたり）
     **/
    public function getUnitTax()
    {

        $rate = $this->isReducedTax() ? \common\models\Tax::findOne(2)->getRate()/100 : \common\models\Tax::findOne(1)->getRate()/100;

        if($this->discountAmount)
            return $this->unit_tax = floor(($this->price - $this->discountAmount) * $rate);


        return $this->unit_tax = floor($this->price * $rate);
    }

    public function setUnitTax($val)
    {
        $this->unit_tax = $val;
    }

    public function isReducedTax() {
        if($this->model instanceof \common\models\Product && $this->model->tax_id == 2) {
            return true;
        } else if($this->model instanceof \common\models\RemedyStock) {
            return $this->model->isRemedy() && !$this->model->isLiquor();
        }


        return false;

    }


    /* @return ActiveRecord */
    abstract protected function getCompany();

    public function getDiscount()
    {
        return $this->_discount;
    }

    public function getDiscountAmount()
    {
        if($this->_discount->amount)
            return $this->_discount->amount;

        if($this->_discount->rate)
            return floor($this->price * $this->_discount->rate / 100);

        return 0;
    }

    public function getDiscountRate()
    {
        return $this->_discount->rate;
    }

    public function getDiscountTotal()
    {
        if($this->_discount->rate)
            return ($this->qty * floor($this->price * $this->_discount->rate / 100));

        if($this->_discount->amount)
            return ($this->qty * $this->_discount->amount);

        return 0;
    }

    public function getDiscountLabel()
    {
        return $this->_discount->label;
    }


    public function getPointConsumeRate()
    {
        return $this->point_consume_rate;
    }

    public function setPointConsumeRate($val)
    {
        $this->point_consume_rate = $val;
    }

    public function getPointConsume()
    {
        return $this->point_consume;
    }

    public function setPointConsume($val)
    {
        $this->point_consume = $val;
    }

    public function getId()
    {
        return $this->_model->primaryKey;
    }

    /* @return ActiveRecord */
    public function getImg()
    {
        if((! $imgs = $this->_model->images) ||
           (! $img  = array_shift($imgs))) {

           // 商品画像が無い場合はcomming soon（default.jpg）を渡す
            $default_img = "default.jpg";

            // 商品がレメディーである場合のみ白ラベル写真。vial_id=5(アルポ)の場合はアルポ写真。それ以外は白ラベルと同じ画像を使う。
            if($this->_model->className() == \common\models\RemedyStock::className()) {
                $vial_id = $this->_model->vial_id;
                if($vial_id == 5) {
                    $default_img = "default_alpo.jpg";
                } else if($vial_id <= 4) {
                    $default_img = "default_remedy.jpg";
                }
            }
            return \yii\helpers\Html::img('@web/img/'.$default_img, ['width'=>'60px','height'=>'60px']);
        }
        
        $px   = 60;
        $path = Yii::getAlias("@webroot/assets/images/{$px}/{$img->basename}");
        $dir  = dirname($path);
        if(! is_dir($dir)) { mkdir($dir); }

        $img->exportContent($path, $px, false);

        return \yii\helpers\Html::img("@web/assets/images/{$px}/{$img->basename}", ['width'=>'60px','height'=>'60px']);
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function getName()
    {
        return $this->_model->name;
    }

    public function getPoint()
    {
        return $this->_point;
    }

    public function getPointAmount()
    {
        if($this->_point->amount)
            return $this->_point->amount;

        if($this->_point->rate)
            return floor($this->price * $this->_point->rate / 100);

        return 0;
    }

    public function getPointRate()
    {
        return $this->_point->rate;
    }

    public function getPointTotal()
    {
        if($this->_point->amount)
            return ($this->qty * $this->_point->amount);

        if($this->_point->rate)
            return ($this->qty * floor($this->price * $this->_point->rate / 100));

        return 0;
    }

    public function getPrice()
    {
        return $this->_model->price;
    }

    public function getQty()
    {
        return $this->_qty;
    }

    public function getQuantity()
    {
        return $this->_qty;
    }

    public function getType()
    {
        return $this->_type;
    }

    abstract public function getUrl();

    protected function setModel($model)
    {
        $this->_model = $model;
    }

    public function setDiscount(ItemDiscount $model)
    {
        $this->_discount = $model;
    }

    public function setDiscountAmount($amount)
    {
        $this->_discount->amount = $amount;
    }

    public function setDiscountRate($rate)
    {
        $this->_discount->rate = $rate;
    }

    public function setPoint($model)
    {
        $this->_point = $model;
    }

    public function setPointRate($rate)
    {
        $this->_point->rate = $rate;
    }

    public function setPointAmount($amount)
    {
        $this->_point->amount = $amount;
    }

    public function setQty($num)
    {
        $this->_qty = $num;

        return true;
    }
    
    public function getRecipe_Id()
    {
        return $this->_recipe_id;
    }

    public function setRecipe_id($recipe_id)
    {
        $this->_recipe_id = $recipe_id;
    }

    public function getCampaign_Id()
    {
        return $this->_campaign_id;
    }

    public function setCampaign_id($campaign_id)
    {
        $this->_campaign_id = $campaign_id;
    }

    public function getIs_wholesale()
    {
         return $this->_is_wholesale;
    }
 
    public function setIs_wholesale($is_wholesale)
    {
         $this->_is_wholesale = $is_wholesale;
    }

    public function getTaxRate()
    {
        return $this->isLiquor() ? \common\models\Tax::findOne(1)->getRate() : \common\models\Tax::findOne(2)->getRate();
    }

    public function setTaxRate($vol)
    {
        $this->tax_rate = $vol;
    }


    /**
     * @return void
     */
    public function increase($qty)
    {
        $this->_qty += $qty;
    }

    /**
     * @return bool
     */
    public function isEvent()
    {
        return $this->model->category->isEvent();
    }

    /**
     * @return bool
     */
    abstract function isLiquor();

    /**
     * @return bool
     */
    public function isProduct()
    {
        return (self::TYPE_PRODUCT === $this->type);
    }

    /**
     * @return bool
     */
    public function isRemedy()
    {
        return (self::TYPE_REMEDY === $this->type);
    }

    /* @return PurchaseItem */
    abstract public function convertToPurchaseItem($purchase_id, $seq);

}
