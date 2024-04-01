<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CustomerAddrbook;

/**
 * SearchAddrbook represents the model behind the search form about `common\models\CustomerAddrbook`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchAddrbook.php $
 * $Id: SearchAddrbook.php 804 2015-03-19 07:31:58Z mori $
 */
class SearchAddrbook extends CustomerAddrbook
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'pref_id'], 'integer'],
            [['name01', 'name02', 'kana01', 'kana02', 'zip01', 'zip02', 'addr01', 'addr02', 'tel01', 'tel02', 'tel03'], 'safe'],
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
        $query = CustomerAddrbook::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'pref_id' => $this->pref_id,
        ]);

        $query->andFilterWhere(['like', 'name01', $this->name01])
            ->andFilterWhere(['like', 'name02', $this->name02])
            ->andFilterWhere(['like', 'kana01', $this->kana01])
            ->andFilterWhere(['like', 'kana02', $this->kana02])
            ->andFilterWhere(['like', 'zip01', $this->zip01])
            ->andFilterWhere(['like', 'zip02', $this->zip02])
            ->andFilterWhere(['like', 'addr01', $this->addr01])
            ->andFilterWhere(['like', 'addr02', $this->addr02])
            ->andFilterWhere(['like', 'tel01', $this->tel01])
            ->andFilterWhere(['like', 'tel02', $this->tel02])
            ->andFilterWhere(['like', 'tel03', $this->tel03]);

        return $dataProvider;
    }
}
