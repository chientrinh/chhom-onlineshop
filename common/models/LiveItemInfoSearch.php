<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LiveItemInfo;

/**
 * LiveItemInfoSearch represents the model behind the search form about `common\models\LiveItemInfo`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/models/LiveItemInfoSearch.php $
 * $Id: LiveItemInfoSearch.php 792 2015-03-14 00:23:21Z mori $
 */

class LiveItemInfoSearch extends LiveItemInfo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['info_id','product_id'], 'integer'],
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
        $query = LiveItemInfo::find();

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

        return $dataProvider;
    }
}
