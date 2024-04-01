<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Storage;

/**
 * SearchStorage represents the model behind the search form about `app\models\Storage`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchStorage.php $
 * $Id: SearchStorage.php 804 2015-03-19 07:31:58Z mori $
 */
class SearchStorage extends Storage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['storage_id', 'src_id', 'dst_id', 'staff_id'], 'integer'],
            [['ship_date', 'pick_date', 'create_date', 'update_date'], 'safe'],
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
        $query = Storage::find();

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
            'storage_id' => $this->storage_id,
            'src_id' => $this->src_id,
            'dst_id' => $this->dst_id,
            'staff_id' => $this->staff_id,
            'ship_date' => $this->ship_date,
            'pick_date' => $this->pick_date,
            'create_date' => $this->create_date,
            'update_date' => $this->update_date,
        ]);

        return $dataProvider;
    }
}
