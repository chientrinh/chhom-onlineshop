<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Manufacture;

/**
 * SearchManufacture represents the model behind the search form about `app\models\Manufacture`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchManufacture.php $
 * $Id: SearchManufacture.php 804 2015-03-19 07:31:58Z mori $
 */
class SearchManufacture extends Manufacture
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['manufacture_id', 'branch_id', 'staff_id', 'quantity'], 'integer'],
            [['craete_date'], 'safe'],
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
        $query = Manufacture::find();

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
            'manufacture_id' => $this->manufacture_id,
            'branch_id' => $this->branch_id,
            'staff_id' => $this->staff_id,
            'quantity' => $this->quantity,
            'craete_date' => $this->craete_date,
        ]);

        return $dataProvider;
    }
}
