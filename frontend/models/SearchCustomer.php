<?php

namespace frontend\models;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\Membercode;

/**
 * SearchCustomer represents the model behind the search form about `common\models\Customer`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/models/SearchCustomer.php $
 * $Id: SearchCustomer.php 1226 2015-08-02 04:57:39Z mori $
 */
class SearchCustomer extends Membercode
{
    public $migrated;
    public $barcode;
    public $tel;

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
            [['tel'], 'filter', 'filter'=>function($value){ return preg_replace('/-/', '', $value); },],
            [['code','migrate_id','barcode','tel'], 'integer'],
            [['tel','barcode'],'integer','min'=>1],
            [['tel'],'string', 'min'=>9],
            [['barcode'],'string', 'length'=>13],
            [['code','migrate_id'], 'integer'],
            [['migrated'], 'boolean'],
            [['directive','code','migrate_id','migrated'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // retun null when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        if('0' === $this->migrated)
            $query->andWhere(['customer_id' => null ]);

        if('1' === $this->migrated)
            $query->andWhere(['not', ['mtb_membercode.customer_id' => null ]]);

        if($this->code)
            $query->andWhere(['like','code',$this->code]);

        if($this->barcode)
            $query->andFilterWhere(['code'=> substr($this->barcode, 3,10) ]);

        if($this->tel)
            $query->innerJoinWith(['customer'])->andFilterWhere(['like', 'CONCAT(dtb_customer.tel01,dtb_customer.tel02,dtb_customer.tel03)', $this->tel]);

        $query->andFilterWhere([
            'directive'  => $this->directive,
            'migrate_id' => $this->migrate_id,
        ]);

        return $dataProvider;
    }
}
