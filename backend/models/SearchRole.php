<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Role;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchRole.php $
 * $Id: SearchRole.php 1505 2015-09-18 13:50:50Z mori $
 *
 * SearchRole represents the model behind the search form about `\backend\models\Role`.
 */
class SearchRole extends Role
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id'], 'integer'],
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
        $query = Role::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'role_id' => $this->role_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
