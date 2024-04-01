<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchStaff represents the model behind the search form about `app\models\Staff`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchStaffRole.php $
 * $Id: SearchStaffRole.php 1505 2015-09-18 13:50:50Z mori $
 */
class SearchStaffRole extends StaffRole
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['staff_id', 'role_id', 'branch_id'], 'integer'],
            ['staff_id',  'exist', 'targetClass' => Staff::className()],
            ['role_id',   'exist', 'targetClass' => Role::className()],
            ['branch_id', 'exist', 'targetClass' => \common\models\Branch::className()],
        ];
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
        $query = StaffRole::find();

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
            'staff_id'  => $this->staff_id,
            'role_id'   => $this->role_id,
            'branch_id' => $this->branch_id,
        ]);

        return $dataProvider;
    }
}
