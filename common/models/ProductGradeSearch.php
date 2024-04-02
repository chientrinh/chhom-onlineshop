<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProductGrade;

/**
 * ProductGradeSearch represents the model behind the search form about `common\models\ProductGrade`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/models/ProductGradeSearch.php $
 * $Id: CategorySearch.php 792 2015-03-14 00:23:21Z mori $
 */

class ProductGradeSearch extends ProductGrade
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_grade_id', 'product_id', 'grade_id', 'price', 'tax', 'tax_rate'], 'integer'],
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
        $query = ProductGrade::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if($this->product_id)
            $query->andFilterWhere([
               'product_id' => $this->product_id,
            ]);

        if($this->grade_id)
            $query->andFilterWhere([
               'grade_id' => $this->grade_id,
            ]);        
            

        return $dataProvider;
    }
}
