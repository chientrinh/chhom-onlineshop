<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Material;

/**
 * SearchMaterial represents the model behind the search form about `app\models\Material`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchMaterial.php $
 * $Id: SearchMaterial.php 804 2015-03-19 07:31:58Z mori $
 */
class SearchMaterial extends Material
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['material_id'], 'integer'],
            [['name'], 'safe'],
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
        $query = Material::find();

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
            'material_id' => $this->material_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
