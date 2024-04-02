<?php

namespace common\components\cart;
use Yii;
use \yii\helpers\ArrayHelper;
use \common\models\Remedy;
use \common\models\RemedyStock;
use \common\models\RemedyPriceRangeItem;
use \common\models\RemedyPotency;
use \common\models\RemedyVial;
use \common\models\ProductMaster;
use \common\models\PurchaseItem;
use \common\models\RecipeItem;

/**
 * ComplexRemedyForm
 * 適用書レメディーを作るためのフォーム、エンドユーザが自分用のコンビネーションを作るときにも使える。
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/ComplexRemedyForm.php $
 * $Id: ComplexRemedyForm.php 3197 2017-02-26 05:22:57Z naito $
 */

class ComplexRemedyForm extends \yii\base\Model implements CartItemInterface
{
    const SCENARIO_PRESCRIBE = 'prescribe'; // ホメオパスが適用書を作る
    const SCENARIO_TAILOR    = 'tailor';    // エンドユーザが自分用のコンビネーションを作る

    public $vial;
    public $drops;
    public $maxDropLimit;
    public $minDropLimit;
    public $qty;
    public $campaign_id;
    public $is_wholesale;
    public $recipe_id;
    protected $_campaign_id;

    protected $_discount;
    protected $_point;
    public $discount_amount;
    public $discount_rate;
    public $point_amount;
    public $point_rate;
    public $unit_price;
    public $unit_tax;
    public $point_consume;
    public $point_consume_rate;

    public $tax_rate;
    public $sku_id;

    /* @bool $thisを \common\models\RecipeItem に変換した結果 self::convertToRecipeItem()実行後に代入される */
    protected $recipeItem;

    public function init()
    {
        parent::init();

        if(! isset($this->vial))
            $this->vial = new RemedyStock(['prange_id' => 8]); // 滴下母体

        if(! isset($this->drops))
            $this->drops = [new RemedyStock()];

        if(! isset($this->maxDropLimit))
            $this->maxDropLimit = 2; // default value

        if(! isset($this->minDropLimit))
            $this->minDropLimit = 1; // default value

        if(! isset($this->qty))
            $this->qty = 1;    // default value

        $this->_discount = new ItemDiscount();
        $this->_point    = new ItemPoint();

        $this->campaign_id = null;
        $this->_campaign_id = null;
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return(['vial','drops','maxDropLimit','minDropLimit','qty','discount_amount','discount_rate','point_amount','point_rate','unit_price','unit_tax','discountRate','discountAmount','pointRate','pointAmount','recipe_id', 'campaign_id', 'is_wholesale', 'point_consume', 'point_consume_rate']);
    } 

    public function attributeLabels()
    {
        return [
            'vial'     => "容器",
            'drops'    => "滴下",
            'qty'      => "数量",
            'price'    => "定価",
        ];
    }

    public function scenarios()
    {
        return [
            parent::SCENARIO_DEFAULT => self::attributes(),
            self::SCENARIO_TAILOR    => self::attributes(),
            self::SCENARIO_PRESCRIBE => self::attributes(),
        ];
    }

    public function rules()
    {
        return [
            [['campaign_id','is_wholesale','sku_id','tax_rate'], 'safe'],
            ['maxDropLimit', 'default', 'value'=> 2 ],
            ['maxDropLimit', 'integer', 'min'=> 1, 'max'=>4, 'except'=>self::SCENARIO_PRESCRIBE ],
            ['maxDropLimit', 'integer', 'min'=> 1, 'max'=>100,   'on'=>self::SCENARIO_PRESCRIBE ],
            [['price','qty'], 'integer', 'min'=> 1,],
            [['vial','drops','price'],  'required'],
            ['vial',  'validateVial'],
            ['drops', 'validateDrops'],
            ['drops', 'validatePotency', 'when' => function($model){
                return in_array($model->vial->vial_id, [RemedyVial::SMALL_BOTTLE,
                                                        RemedyVial::LARGE_BOTTLE]); }],
        ];
    }

    public function beforeValidate()
    {
        if(! parent::beforeValidate())
            return false;

        if(('app-frontend' == Yii::$app->id) && ! $this->recipeItem)
        {
            // 由井ホメオパスは常に app-backyard を利用するのでこの制限を受けない

            if(RemedyVial::SMALL_BOTTLE == $this->vial->vial_id)
            {
                $this->maxDropLimit = 2;// プラ小瓶では最大２滴まで
                $this->minDropLimit = 2;
            }

            elseif(RemedyVial::LARGE_BOTTLE == $this->vial->vial_id)
            {
                $this->maxDropLimit = 4;// プラ大瓶では最大４滴まで
                $this->minDropLimit = 2;
            }

            else {
                $this->minDropLimit = 1;
            }
        }

        return true;
    }

    public function convertToPurchaseItem($seq = 0, $options = [])
    {
        $items = [];

        if($this->vial->remedy_id === null)
            $this->vial->remedy_id = 0;

        // 滴下が１つだったら１つのオブジェクトを返す
        if(! $this->vial->remedy_id && (RemedyVial::GLASS_5ML != $this->vial->vial_id) && (RemedyVial::ALP_20ML != $this->vial->vial_id) && (1 === count($this->drops)))
        {
            $stock = $this->drops[0];
            $stock->vial_id = $this->vial->vial_id;
            $name = ProductMaster::find()->where(['remedy_id'=>$stock->remedy_id,
                                                  'potency_id'=>$stock->potency_id,
                                                  'vial_id'   =>$stock->vial_id])
                                         ->select('name')
                                         ->scalar();
            if(! $name)
                 $name = strtr($stock->name,"\n",' ');

           return new PurchaseItem([
               'seq'       => $seq,
               'remedy_id' => $stock->remedy_id,
               'company_id'=> \common\models\Company::PKEY_HJ,
               'name'      => $name,
               'code'      => $stock->barcode,
               'price'     => $this->price,
               'parent'    => null,
               'quantity'  => $this->quantity,
               'discount_rate'   => $this->discountRate,
               'discount_amount' => $this->discountAmount,
               'point_rate'   => $this->pointRate,
               'point_amount' => $this->pointAmount,
               'unit_price' => $this->getUnitPrice(),
               'unit_tax' => $this->getUnitTax(),
               'point_consume' => $this->getPointConsume(),
               'point_consume_rate' => $this->getPointConsumeRate(),
            ]);
        }

        $stock = $this->vial;
        $name = ProductMaster::find()->where(['remedy_id' =>$stock->remedy_id,
                                              'potency_id'=>$stock->potency_id,
                                              'vial_id'   =>$stock->vial_id])
                                     ->select('name')
                                     ->scalar();
        if(! $name)
             $name = strtr($stock->name,"\n",' ');

        // convert vial
        $items[] = new PurchaseItem([
            'seq'       => $seq++,
            'remedy_id' => $this->vial->remedy_id,
            'code'      => $this->vial->code,
            'price'     => $this->vial->price,
            'name'      => $name,
            'parent'    => null,
        ]);

        // convert drops
        foreach($this->drops as $drop)
        {
            $name = ProductMaster::find()->where(['remedy_id' =>$drop->remedy_id,
                                                  'potency_id'=>$drop->potency_id,
                                                  'vial_id'   =>$drop->vial_id])
                                         ->select('name')
                                         ->scalar();
            if(! $name)
                $name = strtr($drop->name,"\n",' ');

            $items[] = new PurchaseItem([
                'seq'       => $seq++,
                'remedy_id' => $drop->remedy_id,
                'code'      => $drop->code,
                'price'     => $drop->price,
                'name'      => $name,
                'parent'    => $items[0]->seq,
                'is_wholesale'      => $this->is_wholesale,
                'campaign_id'      => $this->campaign_id,
            ]);
        }

        // init options
        $purchase_id     = isset($options['purchase_id'])    ? $options['purchase_id']  : null;
        $discount_rate   = $this->discountRate            ? $this->discountRate   : 0;
        $discount_amount = $this->discountAmount          ? $this->discountAmount : 0;
        $point_rate      = $this->pointRate               ? $this->pointRate      : 0;
        $point_amount    = $this->pointAmount             ? $this->pointAmount    : 0;
        $is_wholesale    = $this->is_wholesale             ? $this->is_wholesale    : null;
        $campaign_id   = $this->campaign_id             ? $this->campaign_id    : null;
        $unit_price      = $this->unitPrice                  ? $this->unitPrice : 0;
        $unit_tax        = $this->unitTax                    ? $this->unitTax : 0;
        $point_consume        = $this->pointConsume                    ? $this->pointConsume : 0;
        $point_consume_rate        = $this->pointConsumeRate                    ? $this->pointConsumeRate : 0;

        // override options
        if(isset($options['discount_rate']))   $discount_rate   = $options['discount_rate'];
        if(isset($options['discount_amount'])) $discount_amount = $options['discount_amount'];
        if(isset($options['point_rate']))      $point_rate      = $options['point_rate'];
        if(isset($options['point_amount']))    $point_amount    = $options['point_amount'];
        if(isset($options['is_wholesale']))    $is_wholesale    = $options['is_wholesale'];
        if(isset($options['campaign_id']))     $campaign_id     = $options['campaign_id'];
        if(isset($options['unit_tax']))        $unit_tax        = $options['unit_tax'];
        if(isset($options['point_consume']))        $point_consume        = $options['point_consume'];
        if(isset($options['point_consume_rate']))        $point_consume_rate        = $options['point_consume_rate'];

        // apply options
        foreach($items as $i => $item)
        {
            $items[$i]->scenario        = PurchaseItem::SCENARIO_REMEDY;
            $items[$i]->purchase_id     = $purchase_id;
            $items[$i]->product_id      = null;
            $items[$i]->company_id      = \common\models\Company::PKEY_HJ;
            $items[$i]->quantity        = $this->qty;
            $items[$i]->is_wholesale    = $is_wholesale;

            if($i == 0) {
                $items[$i]->price = array_sum(ArrayHelper::getColumn($items, 'price'));
                $items[$i]->discount_rate   = $discount_rate;
                $items[$i]->discount_amount = $discount_amount;
                $items[$i]->point_rate      = $point_rate;
                $items[$i]->point_amount    = $point_amount;
                $items[$i]->unit_price      = $unit_price;
                $items[$i]->unit_tax        = $unit_tax;
                $items[$i]->point_consume        = $point_consume;
                $items[$i]->point_consume_rate      = $point_consume_rate;
            } else {
                $items[$i]->price           = 0;
                $items[$i]->discount_rate   = 0;
                $items[$i]->discount_amount = 0;
                $items[$i]->point_rate      = 0;
                $items[$i]->point_amount    = 0;
                $items[$i]->unit_price      = 0;
                $items[$i]->unit_tax      = 0;
                $items[$i]->point_consume      = 0;
                $items[$i]->point_consume_rate      = 0;
            }
        }

        // $scenarios = \yii\helpers\ArrayHelper::getColumn($items, 'remedy_id');
        // $scenarios = $items[0]->attributes;
        // Yii::error($scenarios);
        return $items;
    }

    public static function convertFromRecipeItem(\common\models\RecipeItem $parentItem)
    {
        $model = new self(['scenario'=>self::SCENARIO_PRESCRIBE]);

        $vial = RemedyStock::find()->where([
            'remedy_id'  => $parentItem->remedy_id,
            'potency_id' => $parentItem->potency_id,
            'vial_id'    => $parentItem->vial_id,
        ])->one();

        if(! $vial)
            $vial = new RemedyStock([
                'remedy_id'  => $parentItem->remedy_id,
                'potency_id' => $parentItem->potency_id,
                'vial_id'    => $parentItem->vial_id,
                'prange_id'  => 8, // 滴下母体
            ]);

        $model->vial = $vial;

        $drops = [];
        foreach($parentItem->children as $child)
        {
            $drop = new RemedyStock([
                'remedy_id'  => $child->remedy_id,
                'potency_id' => $child->potency_id,
                'vial_id'    => $child->vial_id,
            ]);
            $drops[] = $drop;
        }
        $model->drops = $drops;
        $model->quantity = $parentItem->quantity;

        $model->recipeItem = $parentItem;

        return $model;
    }

    public function convertToRecipeItem($seq = 0, $options = [])
    {
        $items = [];

        if($this->vial->remedy_id === null)
            $this->vial->remedy_id = 0;

        // 滴下が１つだったら１つのオブジェクトを返す
        if(! $this->vial->remedy_id && (RemedyVial::GLASS_5ML != $this->vial->vial_id && RemedyVial::ALP_20ML != $this->vial->vial_id) && (1 === count($this->drops)))
        {
            $stock = $this->drops[0];
            $stock->vial_id = $this->vial->vial_id;
            $name = ProductMaster::find()->where(['remedy_id' =>$stock->remedy_id,
                                                  'potency_id'=>$stock->potency_id,
                                                  'vial_id'   =>$stock->vial_id])
                                         ->select('name')
                                         ->scalar();
            if(! $name)
                 $name = strtr($stock->name,"\n",' ');

            return array(
                new RecipeItem([
                    'seq'       => $seq,
                    'remedy_id' => $stock->remedy_id,
                    'vial_id'   => $stock->vial_id,
                    'potency_id'=> $stock->potency_id,
                    'code'      => $stock->code,
                    'name'      => $name,
                    'parent'    => null,
                    'recipe_id' => ArrayHelper::getValue($options, 'recipe_id', null),
                ])
            );
        }

        // convert vial
        $items[] = new RecipeItem([
            'seq'       => $seq++,
            'remedy_id' => $this->vial->remedy_id,
            'vial_id'   => $this->vial->vial_id,
            'potency_id'=> $this->vial->potency_id,
            'code'      => $this->vial->code,
            'name'      => $this->vial->name,
            'parent'    => null,
        ]);

        // convert drops
        foreach($this->drops as $drop)
            $items[] = new RecipeItem([
                'seq'       => $seq++,
                'remedy_id' => $drop->remedy_id,
                'vial_id'   => $drop->vial_id,
                'potency_id'=> $drop->potency_id,
                'code'      => $drop->code,
                'name'      => $drop->name,
                'parent'    => $items[0]->seq,
            ]);

        // init options
        $recipe_id     = isset($options['recipe_id'])    ? $options['recipe_id']     : null;

        // apply options
        foreach($items as $i => $item)
        {
            $items[$i]->recipe_id  = $recipe_id;
            $items[$i]->product_id = null;
            $items[$i]->quantity   = $this->qty;
        }

        return $items;
    }

    public function dump()
    {
        $dump = [
            'vial'         => $this->vial->attributes,
            'drops'        => [],
            'maxDropLimit' => $this->maxDropLimit,
            'qty'          => $this->qty,
            'recipeItem'   => $this->recipeItem ? $this->recipeItem->attributes : null,
            'discount'     => $this->discount->attributes,
            'point'        => $this->point->attributes,
            'recipe_id'        => $this->recipe_id,
        ];

        foreach($this->drops as $drop)
            $dump['drops'][] = $drop->attributes;

        return $dump;
    }

    public function extend()
    {
        if($this->maxDropLimit <= count($this->drops))
            return;

        if(0 == count($this->drops))
        {
            $this->drops[] = new RemedyStock;
            return;
        }

        $this->drops[] = new RemedyStock;
    }

    public function getBasePrice()
    {
        return ($this->price * $this->qty);
    }

    public function getCharge()
    {
        return ($this->basePrice - $this->discountTotal);
    }

    public function getCode()
    {
        if($stock = $this->stock)
            return $stock->code;

        return 'ORIGINAL';
    }

    public function getBarCode()
    {
        if($stock = $this->stock)
            return $stock->code;

        return 'ORIGINAL';
    }

    public function getSkuId()
    {
        if($stock = $this->stock)
            return $stock->getSkuId();

        return 'ORIGINAL';
    }

    public function getTaxRate()
    {
        return $this->isLiquor() ? \common\models\Tax::findOne(1)->getRate() : \common\models\Tax::findOne(2)->getRate();
    }

    public function setTaxRate($vol)
    {
        $this->tax_rate = $vol;
    }


    public function getCompany()
    {
        return \common\models\Company::findOne(\common\models\Company::PKEY_HJ);
    }

    public function getCategory()
    {
        return \common\models\Category::findOne(\common\models\Category::REMEDY);
    }

    public function getIs_wholesale()
    {
         return $this->is_wholesale;
    }

    public function setIs_wholesale($is_wholesale)
    {
         $this->is_wholesale = $is_wholesale;
    }


    public function getDiscountAmount()
    {

        if(isset($this->discount_amount)) {
            return $this->discount_amount;
        }

    	if(isset($this->_discount->amount)) {
            $this->setDiscountAmount($this->_discount->amount);

            return $this->discount_amount;
    	}

        if($this->_discount->rate) {
            $this->setDiscountAmount(floor($this->price * $this->_discount->rate / 100));
        }

        return $this->discount_amount;
    }

    public function getDiscountRate()
    {

        if(isset($this->discount_rate))
            return $this->discount_rate;


        if(isset($this->_discount->rate)) {
            $this->setDiscountRate($this->_discount->rate);
            return $this->discount_rate;
        }

        else if(isset($this->_discount->amount))
            $this->setDiscountRate(round($this->_discount->amount / $this->price * 100));
            return $this->discount_rate;


    	return $this->discount_rate;
    }

    public function getDiscount()
    {
        return $this->_discount;
    }

    public function getDiscountLabel()
    {
    	if($this->discount_amount)
    		return $this->quantity * $this->discount_amount;
        return 0;
    }

    public function getDiscountTotal()
    {
        if($this->discountAmount)
            return $this->qty * $this->discountAmount;
        return $this->qty * floor($this->price * $this->discountRate / 100);
    }

    public function getId()
    {
        return md5(json_encode($this->dump()));
    }

    public function getImage()
    {
        return \common\models\ProductImage::DEFAULT_URL;
    }

    public function getModel()
    {
        return $this;
    }

    public function getName()
    {
        $name = [];
        if($this->vial->remedy_id) {
            $vial_name = ProductMaster::find()->where(['remedy_id'=>$this->vial->remedy_id,
                                                      'potency_id'=>$this->vial->potency_id,
                                                      'vial_id'   =>$this->vial->vial_id])
                                             ->select('name')
                                             ->scalar();
            if($vial_name) {
                $name = [$vial_name, ];
            } else {
                $name = [$this->vial->name, ];
            }
        } else {
            $name = [$this->vial->name, ];
        }
        foreach($this->drops as $drop)
            $name[] = $drop->name;

        return implode("\n", $name);
    }

    public function getPoint()
    {
        return $this->_point;
    }

    public function getPointAmount()
    {
        if($this->point_amount)
            return $this->point_amount;

        if($this->point_rate) {
            $this->setPointAmount(floor(($this->price - $this->discountAmount) * $this->point_rate / 100));
        }

        if($this->_point->rate) {
            $this->setPointAmount(floor(($this->price - $this->discountAmount) * $this->_point->rate / 100));
        }
        return $this->point_amount;
    }

    public function getPointRate()
    {
        if($this->point_rate)
            return $this->point_rate;

        return $this->point_rate = $this->_point->rate;
    }

    public function getPointTotal()
    {
    	if(!$this->point_amount) {
            $this->setPointAmount(floor(($this->price * $this->_point->rate / 100) * (1 -($this->discountRate / 100))));
        }
        return $this->quantity * $this->point_amount;
    }

    public function getPointConsume()
    {
        return $this->point_consume;
    }

    public function setPointConsume($val)
    {
        $this->point_consume = $val;
    }

    public function getPointConsumeRate()
    {
        return $this->point_consume_rate;
    }


    public function setPointConsumeRate($val)
    {
        $this->point_consume_rate = $val;
    }


    public function getPrice()
    {
        if(($stock = $this->stock) && ($price = $stock->price))
            return $price;

        $price = $this->vial->price;
        foreach($this->drops as $drop)
        {
            $price += $drop->getPrice();
        }
        return $price;
    }

    public function getProduct_id()
    {
        return null;
    }

    public function getQuantity()
    {
        return $this->qty;
    }

    public function setQuantity($vol)
    {
        $this->qty = $vol;
    }


    /**
     * 販売単価
     *
     **/
    public function getUnitPrice()
    {
        if($this->unit_price)
            return $this->unit_price;

        if($this->discountAmount)
            return $this->unit_price = $this->price - $this->discountAmount;

        return $this->unit_price = $this->price;
    }

    public function setUnitPrice($val)
    {
        $this->unit_price = $val;
    }


    /**
     * 消費税
     *
     **/
    public function getUnitTax($getOld = false)
    {
        if($getOld) {
            $rate = 0.08;
        } else {
            $rate = $this->isReducedTax() ? \common\models\Tax::findOne(2)->getRate()/100 : \common\models\Tax::findOne(1)->getRate()/100;
        }

        if($this->discountAmount)
            return $this->unit_tax = floor(($this->price - $this->discountAmount) * $rate);


        return $this->unit_tax = floor($this->price * $rate);
    }


    public function setUnitTax($val)
    {
        $this->unit_tax = $val;
    }

    public function isReducedTax()
    {
        return $this->isRemedy() && !$this->isLiquor();
    }



    public function getStock()
    {
        if($this->vial->remedy_id || (1 != count($this->drops)))
            return null;

        $model = \common\models\RemedyStock::find()->where([
            'remedy_id'  => $this->drops[0]->remedy_id,
            'potency_id' => $this->drops[0]->potency_id,
            'vial_id'    => $this->vial->vial_id,
        ])->one();

        if(! $model)
            $model = new \common\models\RemedyStock([
                'remedy_id'  => $this->drops[0]->remedy_id,
                'potency_id' => $this->drops[0]->potency_id,
                'vial_id'    => $this->vial->vial_id,
            ]);

        return $model;
    }

    public function getUrl()
    {
        return '';
    }

    public function setDiscount($model)
    {
        $this->_discount = $model;
        $this->setDiscountAmount($model->amount);
        $this->setDiscountRate($model->rate);
    }

    public function setDiscountRate($vol)
    {
        $this->_discount->rate   = $vol;
        $this->discount_rate = $vol;
    }

    public function setDiscountAmount($vol)
    {
        $this->_discount->amount = $vol;
        $this->discount_amount = $vol;
    }

    public function setPoint($model)
    {
        $this->_point = $model;
        $this->setPointAmount($model->amount);
        $this->setPointRate($model->rate);
    }

    public function setPointAmount($vol)
    {
        $this->_point->amount = $vol;
    	$this->point_amount = $vol;
    }

    public function setPointRate($vol)
    {
        $this->_point->rate = $vol;
        $this->point_rate   = $vol;
    }

    public function getCampaign_Id()
    {
        $this->_campaign_id;
    }

    public function setCampaign_Id($campaign_id)
    {
        $this->_campaign_id = $campaign_id;
    }

    public function getRecipe_Id()
    {
        $this->recipe_id;
    }

    public function setRecipe_Id($recipe_id)
    {
        $this->recipe_id = $recipe_id;
    }

    /**
     * re-construct model from buffer stored per session
     * @return bool
     */
    public function feed($data)
    {
        if(! isset($data['vial']) && ! isset($data['drops']) && ! isset($data['maxDropLimit']))
            return false;

        if(array_key_exists('vial', $data))
        {
            $row = (object) $data['vial'];
            $this->vial->remedy_id  = $row->remedy_id;
            $this->vial->potency_id = $row->potency_id;
            $this->vial->prange_id  = $row->prange_id;
            $this->vial->vial_id    = $row->vial_id;
        }

        if(array_key_exists('maxDropLimit', $data))
            $this->maxDropLimit = $data['maxDropLimit'];

        if(array_key_exists('drops', $data))
            if(is_array($data['drops']))
            {
               $this->drops = [];

               foreach($data['drops'] as $row)
               {
                   $drop = new RemedyStock();
                   $drop->load([$drop->formName() => $row]);
                   $this->drops[] = $drop;
               }
            }

        if(array_key_exists('qty', $data))
            $this->qty = $data['qty'];

        if(array_key_exists('discount', $data))
            $this->_discount->load($data['discount'], '');

        if(array_key_exists('point', $data))
            $this->_point->load($data['point'], '');

        if(array_key_exists('recipeItem', $data))
            $this->recipeItem = \common\models\RecipeItem::find()->where([
                'recipe_id' => $data['recipeItem']['recipe_id'],
                'seq'       => $data['recipeItem']['seq'],
            ])->one();

        if(array_key_exists('recipe_id', $data))
            $this->recipe_id = $data['recipe_id'];

        return true;
    }

    public function isLiquor()
    {
        $uid = ArrayHelper::getValue($this->vial, 'vial.unit.utype_id', null);

        return (\common\models\UnitType::PKEY_LIQUID == $uid);
    }

    public function isProduct()
    {
        return false;
    }

    public function isRemedy()
    {
        return true;
    }

    /**
     * re-construct model from http request params
     * @return bool
     */
    public function load($data, $formName = null)
    {
        parent::load($data, $formName);

        if(isset($data['Vial']))
        {
            $ean13 = \yii\helpers\ArrayHelper::getValue($data,'Vial.barcode','');
            if(13 == strlen($ean13) && ($model = RemedyStock::findByBarcode($ean13))) {
                $this->vial = $model;
            } else {
                $attr = \yii\helpers\ArrayHelper::getValue($data,'Vial');
                $this->vial->load($attr,'');
            }
        }

        if(! isset($data['Drops']))
            return true;

        $this->drops = [];
        foreach($data['Drops'] as $data)
        {
            if(isset($data['delete']))
                continue;

            $drop = new RemedyStock(['vial_id'=>10]);

            if(isset($data['abbr']) && strlen($data['abbr']) > 0)
            {
// この辺で、販売禁止のレメディーのチェックを行って、drop追加をやめて（パラメータパターンの実装時にやった空にするだけにするか）、addErrorをやるか？？
                $abbr = trim($data['abbr']);
                $remedy = Remedy::findOne(['abbr'=> $abbr]);
                if($remedy)
                    $drop->remedy_id = $remedy->remedy_id;
                else
                {
                    $q = Remedy::find()->andWhere(['like','abbr',$abbr])
                                       ->select('remedy_id');
                    if(1 == $q->count())
                        $drop->remedy_id = $q->scalar();
                }

                // この時点でRemedy_idが特定されているはず
                $exists = \common\models\ProductMaster::find()
                            ->where(['remedy_id' => $drop->remedy_id])
                            ->andWhere(['vial_id' => RemedyVial::DROP])
                            ->andWhere(['not', ['name' => '']])
                            ->andWhere(['not', ['name' => null]])->exists();
                if(!$exists)
                {
                    Yii::$app->session->addFlash('error', sprintf('ご指定のレメディー %s は現在取り扱いがありません', $abbr));
                    $drop->remedy_id = null;
                    $drop->potency_id = null;
                    $this->drops[] = $drop;
                    continue;
                }

            }


            if($pid = ArrayHelper::getValue($data, 'potency_id', null))
                $drop->potency_id = $pid;

            elseif($drop->remedy_id)
            {
                $q = RemedyStock::find()->andWhere(['remedy_id'=> $drop->remedy_id,
                                                    'vial_id'  => RemedyVial::DROP])
                                        ->select('potency_id');
                if(1 == $q->count())
                    $drop->potency_id = $q->scalar();
            }

            $this->drops[] = $drop;
        }

        return true;
    }

    public function validateDrops($attribute, $params)
    {
        if((! $this->drops) || (0 == count($this->drops)))
        {
            $this->addError($attribute, '滴下がありません');
            return false;
        }

        foreach($this->drops as $i => $drop)
        {
            $model = new \yii\base\DynamicModel($drop->attributes);
            $model->addRule(['remedy_id'], 'required', ['message'=>sprintf("滴下 %d のレメディーを指定してください",++$i)]);
            $model->addRule(['potency_id'], 'required', ['message'=>sprintf("滴下 %d のポーテンシーを指定してください",$i)]);
            $model->addRule(['remedy_id','potency_id','vial_id'], 'exist', ['targetClass'=>RemedyStock::className()]);
            if(! $model->validate())
                $this->addError($attribute, array_shift($model->getFirstErrors()));
            elseif(! RemedyStock::find()->where([
                'vial_id'   => $drop->vial_id,
                'remedy_id' => $drop->remedy_id,
                'potency_id'=> $drop->potency_id,
                'in_stock'  => 1,
            ])->one())
                $this->addError($attribute, sprintf('滴下 %d の在庫がありません', $i));
        }

        if(!$this->recipeItem && $this->maxDropLimit < count($this->drops))
        {
            $this->addError($attribute, sprintf('その容器(%s)で滴下できるのは %d 点までです', $this->vial->name,$this->maxDropLimit) );
            return false;
        }

        if (!$this->recipeItem && $this->minDropLimit > count($this->drops))
        {
            $this->addError($attribute, sprintf('その容器(%s)で滴下できるのは %d 点からです', $this->vial->name,$this->minDropLimit) );
            return false;
        }

        if(! $this->hasErrors($attribute))
        {
            $rid = \yii\helpers\ArrayHelper::getColumn($this->drops,'remedy_id');
            $rid = array_unique($rid);
            $rows = [];
            foreach($rid as $r){ $rows[$r] = []; }

            foreach($this->drops as $i => $drop)
            {
                if(in_array($drop->potency_id, $rows[$drop->remedy_id]))
                    $this->addError($attribute, sprintf('滴下 %d が重複しています', ++$i));
                else
                    $rows[$drop->remedy_id][] = $drop->potency_id;
            }
        }

        return $this->hasErrors($attribute);
    }

    public function validatePotency($attribute, $params)
    {
        $potencies = RemedyPotency::find()->where(['like','name','LM'])
                                          ->select('potency_id')
                                          ->column();

        foreach($this->drops as $i => $drop)
        {
            if(in_array($drop->potency_id, $potencies))
                $this->addError($attribute, sprintf('その容器(%s)でLMポーテンシーは選べません', $this->vial->name));
        }
    }

    public function validateVial($attribute, $params)
    {
        if(! $this->vial)
            $this->addError($attribute, '容器がありません');

        elseif($prange_id = RemedyStock::find()->where([
                'remedy_id' => $this->vial->remedy_id,
                'potency_id'=> $this->vial->potency_id,
                'vial_id'   => $this->vial->vial_id,
            ])->select('prange_id')->scalar())
        {
            $this->vial->prange_id = $prange_id;
        }

        $model = new \yii\base\DynamicModel($this->vial->attributes);
        $model->addRule(['vial_id','prange_id'], 'required', ['message' => "容器を指定してください"]);
        $model->addRule(['vial_id'], 'in', ['not'=>true, 'range'=>[10]]);
        $model->addRule(['vial_id','prange_id'], 'exist', ['targetClass'=>RemedyPriceRangeItem::className()]);
        $model->addRule(['remedy_id','potency_id','vial_id','prange_id'], 'integer');
        $model->addRule(['remedy_id','potency_id'], 'default', ['value'=> 0]);

        if($this->scenario == self::SCENARIO_TAILOR)
            $model->addRule(['remedy_id','potency_id'], 'integer', ['min'=> 0, 'max'=> 0]);

        if($this->vial->remedy_id)
        {
            $model->addRule(['remedy_id','potency_id'], 'required');
            $model->addRule(['vial_id'], 'in', ['range'=>[7, 8, 13, 14, 15, 16, 17], ]); // ガラス瓶(20ml)、20mlスプレー
            $model->addRule(['remedy_id','potency_id','vial_id','prange_id'], 'exist', ['targetClass'=>RemedyStock::className()]);
            if(! RemedyStock::find()->where([
                'vial_id'   => $this->vial->vial_id,
                'remedy_id' => $this->vial->remedy_id,
                'potency_id'=> $this->vial->potency_id,
                'prange_id' => $this->vial->prange_id,
                'in_stock'  => 1,
            ])->one())
                $this->addError($attribute, '容器の在庫がありません');
        }

        if(! $model->validate())
            $this->addError($attribute, array_shift($model->getFirstErrors()));
    }

}
