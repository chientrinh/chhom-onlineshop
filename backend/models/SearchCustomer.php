<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Customer;

/**
 * SearchCustomer represents the model behind the search form about `common\models\Customer`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchCustomer.php $
 * $Id: SearchCustomer.php 4151 2019-04-12 02:09:01Z mori $
 */
class SearchCustomer extends Customer
{
    public $company;
    public $membership;
    public $code;
    public $keywords;

    public $is_agency;
    public $agencies;
    public $is_active;
 
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['keywords','filter', 'filter'=> function($value) { return \common\components\Romaji2Kana::translate($value); }, 'skipOnEmpty'=>true ],
            [['grade_id','customer_id', 'sex_id', 'pref_id','membership'], 'integer'],
            [['name01', 'name02', 'kana01', 'kana02', 'birth', 'email', 'zip01', 'zip02', 'addr01', 'addr02', 'tel01', 'tel02', 'tel03', 'expire_date', 'subscribe', 'is_agency', 'agencies','is_active'], 'safe'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return parent::scenarios();
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
        $query = Customer::find()->active();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort = self::appendSortAttributes($dataProvider->sort);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'customer_id' => $this->customer_id,
            'grade_id'    => $this->grade_id,
            'sex_id'      => $this->sex_id,
            'birth'       => $this->birth,
            'pref_id'     => $this->pref_id,
            'expire_date' => $this->expire_date,
        ]);


        $query->andFilterWhere(['like', 'name01', $this->name01])
            ->andFilterWhere(['like', 'name02', $this->name02])
            ->andFilterWhere(['like', 'kana01', $this->kana01])
            ->andFilterWhere(['like', 'kana02', $this->kana02])
            ->andFilterWhere(['like', 'email',  $this->email])
            ->andFilterWhere(['like', 'zip01',  $this->zip01])
            ->andFilterWhere(['like', 'zip02',  $this->zip02])
            ->andFilterWhere(['like', 'addr01', $this->addr01])
            ->andFilterWhere(['like', 'addr02', $this->addr02])
            ->andFilterWhere(['like', 'tel01',  $this->tel01])
            ->andFilterWhere(['like', 'tel02',  $this->tel02])
            ->andFilterWhere(['like', 'tel03',  $this->tel03]);

        $query->joinWith(['membercode']);

        if($this->company)
            $query->joinWith(['companies'])->andFilterWhere(['company_id'=>$this->company]);

        if($this->membership)
            $query->joinWith(['memberships'])->andFilterWhere(['membership_id'=>$this->membership]);

        if($this->is_active)
        {
            if(1 == $this->is_active) {
                $query->andFilterWhere(['>=', 'dtb_customer.expire_date' , new Expression('NOW()')]);
            } else if(2 == $model->is_active) {
                $query->andFilterWhere(['<', 'dtb_customer.expire_date' , new Expression('NOW()')]);
            }

        }

        if($this->keywords)
        {
            $strings = self::filterNumber(explode(' ',$this->keywords), true);
            $numbers = self::filterNumber(explode(' ',$this->keywords), false);

            foreach(array_merge($strings, $numbers) as $item)
            {
                $query->andFilterWhere([
                    'or',
                    ['like','name01',$item],
                    ['like','name02',$item],
                    ['like','kana01',$item],
                    ['like','kana02',$item],
                    ['like','email', $item],
                    ['like','CONCAT(tel01,tel02,tel03)',$item],
                    ['like','CONCAT(zip01,zip02)',      $item],
                    ['like','code', $item],
                ]);
            }
        }
        if($this->code)
            $query->andFilterWhere(['like','code',$this->code]);
        
        if($this->is_agency)
            if(1 == $this->is_agency) {
                $query->andWhere(['in', 'membership_id', [Membership::PKEY_AGENCY_HJ_A,Membership::PKEY_AGENCY_HJ_B,Membership::PKEY_AGENCY_HE,Membership::PKEY_AGENCY_HP]])
                      ->andWhere(['not',['>', 'start_date',  new \yii\db\Expression('NOW()')]])
                      ->andWhere(['not',['<', 'expire_date', new \yii\db\Expression('NOW()')]]);

            } else if (0 == $this->is_agency) {

                $query->andWhere(['not in', 'membership_id', [Membership::PKEY_AGENCY_HJ_A,Membership::PKEY_AGENCY_HJ_B,Membership::PKEY_AGENCY_HE,Membership::PKEY_AGENCY_HP]])
                      ->orWhere(['and',
                          ['in', 'membership_id', [Membership::PKEY_AGENCY_HJ_A,Membership::PKEY_AGENCY_HJ_B,Membership::PKEY_AGENCY_HE,Membership::PKEY_AGENCY_HP]],
                          ['start_date' =>  '\> '. new \yii\db\Expression('NOW()')],
                          ['expire_date' => '\< '.new \yii\db\Expression('NOW()')]
                       ]);
            }
            
        if($this->agencies)
            switch($this->agencies) {
            case 99:
                break;
            case 0:
                $query->andWhere(['in', 'membership_id', [Membership::PKEY_AGENCY_HJ_A,Membership::PKEY_AGENCY_HJ_B]]);
            case 1:
                $query->andWhere(['membership_id' => Membership::PKEY_AGENCY_HE]);
            case 2:
                $query->andWhere(['membership_id' => Membership::PKEY_AGENCY_HP]);
            case 3:
                $query->andWhere(['membership_id' => Membership::PKEY_AGENCY_HJ]);
                $query->andWhere(['membership_id' => Membership::PKEY_AGENCY_HE]);
            case 4:
                $query->andWhere(['membership_id' => Membership::PKEY_AGENCY_HJ]);
                $query->andWhere(['membership_id' => Membership::PKEY_AGENCY_HP]);
            case 5:
                $query->andWhere(['membership_id' => Membership::PKEY_AGENCY_HE]);
                $query->andWhere(['membership_id' => Membership::PKEY_AGENCY_HP]);
            case 6:
                $query->andWhere(['membership_id' => Membership::PKEY_AGENCY_HJ]);
                $query->andWhere(['membership_id' => Membership::PKEY_AGENCY_HE]);
                $query->andWhere(['membership_id' => Membership::PKEY_AGENCY_HP]);

            }
            
        return $dataProvider;
    }
    
    private static function filterNumber($items, $exclude=false)
    {
        $num = [];
        $str = [];

        foreach($items as $item)
        {
            if(is_numeric($item))
                $num[] = $item;
            else
                $str[] = $item;
        }

        if($exclude)
            return $str;

        return $num;
    }

    private static function appendSortAttributes(\yii\data\Sort $sort)
    {
        $sort->attributes['kana'] = [
            'asc'  => ['kana01' => SORT_ASC, 'kana02' => SORT_ASC],
            'desc' => ['kana01' => SORT_DESC, 'kana02' => SORT_DESC],
        ];

        $sort->attributes['name'] = [
            'asc'  => ['kana01' => SORT_ASC, 'kana02' => SORT_ASC],
            'desc' => ['kana01' => SORT_DESC, 'kana02' => SORT_DESC],
        ];

        $sort->attributes['tel'] = [
            'asc'   => ['tel01' => SORT_ASC, 'tel02' => SORT_ASC, 'tel03'=> SORT_ASC],
            'desc'  => ['tel01' => SORT_DESC,'tel02' => SORT_DESC,'tel03'=> SORT_DESC],
        ];

        $sort->attributes['code'] = [
            'asc'  => ['mtb_membercode.code' => SORT_ASC],
            'desc' => ['mtb_membercode.code' => SORT_ASC],
        ];

        return $sort;
    }
}
