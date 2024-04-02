<?php

namespace common\components\cart;

use Yii;
use common\models\Branch;
use common\models\Company;
use common\models\Payment;
use common\models\Recipe;

/**
 * Instance of Cart, designed for a Consumer
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/RecipeCart.php $
 * $Id: RecipeCart.php 2836 2016-08-11 06:01:13Z mori $
 */

class RecipeCart extends Cart
{
    public function init()
    {
        parent::init();

        $branch = Branch::findOne(Branch::PKEY_ATAMI);
        $this->setBranch($branch);
        $this->company = Company::findOne(Company::PKEY_HJ);

        $rid   = array_shift(array_values($this->recipes));

        if(! $model = Recipe::findOne($rid))
            throw new \yii\base\UserException("No such recipe_id: $rid");

        foreach($model->items as $m)
        {
            if(0 < $m->product_id)
                $this->add($m->product_id,['qty'=>$m->quantity]);

            elseif($m->children)
                $this->append(\common\components\cart\ComplexRemedyForm::convertFromRecipeItem($m));

            elseif($m->parent)
                continue;

            else
                $this->add($m->remedy_id,['qty'=>$m->quantity,'potency_id'=>$m->potency_id,'vial_id'=>$m->vial_id]);
        }

        if(($client = $model->client) && ($parent = $client->parent)) // クライアントは家族会員である（会員本人ではない）
            foreach(['zip01','zip02','pref_id','addr01','addr02','tel01','tel02','tel03','email'] as $key)
                if(! $client->$key){ $client->$key = $parent->$key; } // 住所,TEL,emailを代入

        parent::setCustomer($parent ? $parent : $client);
        parent::setDestination($client);
    }

    /**
     * @inheritdoc
     */
    protected function initPayment()
    {
        $this->payments = [Payment::PKEY_YAMATO_COD];
        $this->_purchase->payment_id = array_shift(array_values($this->payments));
    }

    /*
     * CustomerはつねにRecipe::clientであること！
     */
    public function setCustomer(\common\models\Customer $customer)
    {
        return false;
    }

}
