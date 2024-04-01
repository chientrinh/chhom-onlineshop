<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProductPickcode;

/**
 * SearchProdouctPickcode represents the model behind the search form about `\common\models\ProductPickcode`.
 */
class SearchProductPickcode extends ProductPickcode
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ean13', 'product_code', 'pickcode'], 'safe'],
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
        $query = ProductPickcode::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'ean13', $this->ean13])
            ->andFilterWhere(['like', 'product_code', $this->product_code])
            ->andFilterWhere(['like', 'pickcode', $this->pickcode]);

        return $dataProvider;
    }
}