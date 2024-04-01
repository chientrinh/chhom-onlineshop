<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use \common\components\ean13\CheckDigit;
use \common\models\Campaign;
use \common\models\CampaignDetail;
use \common\models\Category;
use \common\models\Subcategory;
use \common\models\ProductSubcategory;
use \common\models\ProductMaster;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchCampaignDetail.php $
 * $Id: $
 *
 */
class SearchCampaignDetail extends CampaignDetail
{
    public $keywords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['campaign_id', 'category_id', 'campaign_name', 'start_date', 'end_date', 'status', 'branch_id'], 'safe'],
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
        $query = Campaign::find()
                    ->joinWith('branch');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['campaign_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) 
        {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'campaign_id' => $this->campaign_id,
            'status'      => $this->status,
            // 'branch_id'   => $this->branch_id,
        ]);

        $query
            ->andFilterWhere(['like', 'campaign_code', $this->campaign_code])
            ->andFilterWhere(['like', 'campaign_name', $this->campaign_name]);

        if ($this->start_date)
        {
            $start_date = date('Y-m-d 00:00:00', strtotime($this->start_date));
            $query->andWhere('start_date >= :start_date', [':start_date' => $start_date]);
        }

        if ($this->end_date) 
        {
            $end_date = date('Y-m-d 23:59:59', strtotime($this->end_date));
            $query->andWhere('end_date <= :end_date', [':end_date' => $end_date]);
        }

        if ($this->branch_id) 
            $query->andFilterWhere(['like', \common\models\Branch::tableName().'.name', $this->branch_id]);


        if($this->grade_id)
            $query->andFilterWhere(['like', \common\models\CustomerGrade::tableName().'name', $this->grade_id]);

        return $dataProvider;
    }

    public static function searchCategories($campaign_id, $withProducts = false)
    {
        $query = CampaignDetail::find()
                    ->andWhere(['campaign_id' => $campaign_id])
                    ->andWhere(CampaignDetail::tableName().'.category_id IS NOT NULL')
                    ->leftJoin(['c' => Category::tableName()], CampaignDetail::tableName(). '.category_id=c.category_id');

        if ($withProducts) 
            $query->leftJoin(['p' => ProductMaster::tableName()], CampaignDetail::tableName().'.category_id=p.category_id');

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 20 ],
            'sort'  => [ 'defaultOrder' => ['category_id' => SORT_DESC] ],
        ]);

    }

    public static function searchSubCategories($campaign_id, $withProducts = false)
    {
        $query = CampaignDetail::find()
                    ->andWhere(['campaign_id' => $campaign_id])
                    ->andWhere(CampaignDetail::tableName().'.subcategory_id IS NOT NULL')
                    ->innerJoin(['c' => Subcategory::tableName()], CampaignDetail::tableName(). '.subcategory_id=c.subcategory_id');

        if ($withProducts)
            $query->innerJoin(['sp' => ProductSubcategory::tableName()], 
                                    CampaignDetail::tableName(). '.subcategory_id=sp.subcategory_id'
                                )
                  ->innerJoin(['m' => ProductMaster::tableName()], 'sp.ean13=m.ean13');

        $query->distinct();

        return new ActiveDataProvider([
            'pagination' => [ 'pageSize' => 20 ],
            'query' => $query,
            'sort'  => [ 'defaultOrder' => ['subcategory_id' => SORT_DESC] ],
        ]);

    }

    public static function searchProducts($campaign_id)
    {
        $query = CampaignDetail::find()
                    ->andWhere(['campaign_id' => $campaign_id])
                    ->andWhere(CampaignDetail::tableName().'.ean13 IS NOT NULL')
                    ->innerJoin(['m' => ProductMaster::tableName()], CampaignDetail::tableName().'.ean13=m.ean13');

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 20 ],
            'sort'  => [ 'defaultOrder' => ['ean13' => SORT_DESC] ],
        ]);
    }   
}
