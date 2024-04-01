<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DelivTime;

/**
 * SearchDelivTime represents the model behind the search form about `common\models\DelivTime`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchDelivTime.php $
 * $Id: SearchDelivTime.php 804 2015-03-19 07:31:58Z mori $
 */
class SearchDelivTime extends DelivTime
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dtime_id', 'deliveror_id'], 'integer'],
            [['time', 'name'], 'safe'],
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
        $query = DelivTime::find();

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
            'dtime_id' => $this->dtime_id,
            'deliveror_id' => $this->deliveror_id,
            'time' => $this->time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
