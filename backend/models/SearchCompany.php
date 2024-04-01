<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Company;

/**
 * SearchCompany represents the model behind the search form about `common\models\Company`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchCompany.php $
 * $Id: SearchCompany.php 804 2015-03-19 07:31:58Z mori $
 */
class SearchCompany extends Company
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'pref_id'], 'integer'],
            [['name', 'manager', 'email', 'zip01', 'zip02', 'addr01', 'addr02', 'tel01', 'tel02', 'tel03'], 'safe'],
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
        $query = Company::find();

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
            'company_id' => $this->company_id,
            'pref_id' => $this->pref_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'manager', $this->manager])
            ->andFilterWhere(['like', 'email', $this->email])
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
