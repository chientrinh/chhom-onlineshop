<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use \common\components\ean13\CheckDigit;
use \common\models\AgencyRank;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchAgencyRank.php $
 * $Id: $
 */
class SearchAgencyRank extends AgencyRank 
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rank_id', 'name', 'liquor_rate', 'remedy_rate', 'goods_rate', 'other_rate'], 'safe'],
        ];
    }

    public function init()
    {
        parent::init();
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
        $query = AgencyRank::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['rank_id' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) 
        {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'rank_id' => $this->rank_id,
        ]);

        $query
            ->andFilterWhere(['like', 'name', $this->name]);


        return $dataProvider;
    }
}
