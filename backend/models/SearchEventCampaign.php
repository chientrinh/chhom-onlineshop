<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use \common\components\ean13\CheckDigit;
use common\models\EventCampaign;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchEventCampaign.php $
 * $Id: $
 */
class SearchEventCampaign extends EventCampaign
{
    public $ecampaign_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ecampaign_id', 'campaign_code', 'start_date', 'end_date'], 'safe'],
            ['campaign_code', 'default', 'value' => null],
        ];
    }

    public function init()
    {
        parent::init();
        $this->campaign_code = null;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
        $query = EventCampaign::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['ecampaign_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) 
        {
            return $dataProvider;
        }
        
        if ($this->campaign_code) {
            $query->andWhere(['campaign_code' => $this->campaign_code]);
        }
            
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

        return $dataProvider;
    }
}