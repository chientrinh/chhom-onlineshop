<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchStaff represents the model behind the search form about `app\models\Staff`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchStaff.php $
 * $Id: SearchStaff.php 1211 2015-07-29 23:48:09Z mori $
 */
class SearchStaff extends Staff
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['staff_id', 'company_id'], 'integer'],
            [['name01', 'name02', 'email', 'password_hash', 'auth_key', 'update_date'], 'safe'],
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
        $query = Staff::find()->active();

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
            'staff_id'   => $this->staff_id,
            'company_id' => $this->company_id,
        ]);

        $query->andFilterWhere(['like', 'name01', $this->name01])
              ->andFilterWhere(['like', 'name02', $this->name02])
              ->andFilterWhere(['like', 'email',  $this->email]);

        return $dataProvider;
    }
}
