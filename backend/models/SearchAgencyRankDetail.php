<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use \common\components\ean13\CheckDigit;
use \common\models\AgencyRank;
use \common\models\AgencyRankDetail;
use \common\models\Category;
use \common\models\Subcategory;
use \common\models\ProductSubcategory;
use \common\models\ProductMaster;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchAgencyRankDetail.php $
 * $Id: $
 *
 */
class SearchAgencyRankDetail extends AgencyRankDetail
{
    public $keywords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
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
        $query = AgencyRank::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['rank_id' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) 
        {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'rank_id' => $this->rank_id,
        ]);

        $query
            ->andFilterWhere(['like', 'name', $this->name]);


        return $dataProvider;
    }


    public static function searchSubCategories($rank_id, $withProducts = false)
    {
        $query = AgencyRankDetail::find()
                    ->andWhere(['rank_id' => $rank_id])
                    ->andWhere(AgencyRankDetail::tableName().'.subcategory_id IS NOT NULL')
                    ->innerJoin(['c' => Subcategory::tableName()], AgencyRankDetail::tableName(). '.subcategory_id=c.subcategory_id');

        if ($withProducts)
            $query->innerJoin(['sp' => ProductSubcategory::tableName()], 
                                    AgencyRankDetail::tableName(). '.subcategory_id=sp.subcategory_id'
                                )
                  ->innerJoin(['m' => ProductMaster::tableName()], 'sp.ean13=m.ean13');

        $query->distinct();

        return new ActiveDataProvider([
            'pagination' => [ 'pageSize' => 20 ],
            'query' => $query,
            'sort'  => [ 'defaultOrder' => ['subcategory_id' => SORT_ASC] ],
        ]);

    }

    public static function searchProducts($rank_id)
    {
        $query = AgencyRankDetail::find()
                    ->andWhere(['rank_id' => $rank_id])
                    ->andWhere(AgencyRankDetail::tableName().'.sku_id IS NOT NULL')
                    ->innerJoin(['m' => ProductMaster::tableName()], AgencyRankDetail::tableName().'.sku_id=m.sku_id');

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 20 ],
            'sort'  => [ 'defaultOrder' => ['sku_id' => SORT_ASC] ],
        ]);
    }   
}
