<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LiveInfo;

/**
 * StreamingSearch represents the model behind the search form about `common\models\LiveInfo`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/models/LiveInfoSearch.php $
 * $Id: LiveInfoSearch.php 792 2015-03-14 00:23:21Z mori $
 */

class LiveInfoSearch extends LiveInfo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['info_id','product_id','online_coupon_enable','online_option_enable','adult_price1','adult_price2','adult_price3','child_price1','child_price2','child_price3','infant_price1','infant_price2','infant_price3','capacity','subscription','campaign_type','support_entry'], 'integer'],
            [['name','coupon_name','coupon_name','option_name','place','campaign_code','campaign_period','pre_order_code','pre_order_period'], 'string', 'max' => 255],
            [['companion'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = LiveInfo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);



        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'info_id' => $this->info_id,
        ]);
        $query->andFilterWhere([
            'product_id' => $this->product_id,
        ]);
        $query->andFilterWhere([
            'online_coupon_enable' => $this->online_coupon_enable,
        ]);
        $query->andFilterWhere([
            'online_option_enable' => $this->online_option_enable,
        ]);

        if($this->companion == "0") {
            $query->andWhere([
                'companion' => null,
            ]);
        } else if($this->companion == "1") {
            $query->andWhere(['not',[
                'companion' => null,
            ]]);
            // var_dump($this->companion,$query->createCommand()->rawSql);exit;
        }
        $query->andFilterWhere([
            'adult_price1' => $this->adult_price1,
        ]);
        $query->andFilterWhere([
            'adult_price2' => $this->adult_price2,
        ]);
        $query->andFilterWhere([
            'adult_price3' => $this->adult_price3,
        ]);
        $query->andFilterWhere([
            'child_price1' => $this->child_price1,
        ]);
        $query->andFilterWhere([
            'child_price2' => $this->child_price2,
        ]);
        $query->andFilterWhere([
            'child_price3' => $this->child_price3,
        ]);
        $query->andFilterWhere([
            'infant_price1' => $this->infant_price1,
        ]);
        $query->andFilterWhere([
            'infant_price2' => $this->infant_price2,
        ]);
        $query->andFilterWhere([
            'infant_price3' => $this->infant_price3,
        ]);
        $query->andFilterWhere([
            'capacity' => $this->capacity,
        ]);
        $query->andFilterWhere([
            'subscription' => $this->capacity,
        ]);

        $query->andFilterWhere(['like', 'campaign_code', $this->campaign_code]);

        $query->andFilterWhere([
            'campaign_type' => $this->campaign_type,
        ]);

        $query->andFilterWhere([
            'support_entry' => $this->support_entry,
        ]);

        $query->andFilterWhere(['like', 'campaign_period', $this->campaign_period]);

        $query->andFilterWhere(['like', 'pre_order_code', $this->pre_order_code]);
        $query->andFilterWhere(['like', 'pre_order_period', $this->pre_order_period]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        $query->andFilterWhere(['like', 'coupon_name', $this->coupon_name]);

        $query->andFilterWhere(['like', 'coupon_code', $this->coupon_code]);

        $query->andFilterWhere(['like', 'option_name', $this->option_name]);

        $query->andFilterWhere(['like', 'place', $this->place]);

        return $dataProvider;
    }
}
