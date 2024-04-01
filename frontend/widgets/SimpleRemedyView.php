<?php

namespace frontend\widgets;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/SimpleRemedyView.php $
 * $Id: SimpleRemedyView.php 2675 2016-07-08 04:26:01Z mori $
 */
use Yii;
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

use \backend\models\Staff;
use \common\models\Company;
use \common\models\Customer;
use \common\models\CustomerGrade;
use \common\models\Membership;
use \common\models\Remedy;
use \common\models\RemedyPotency;
use \common\models\RemedyStock;
use \common\models\RemedyVial;
use \common\models\RemedyPriceRangeItem;
use \common\models\ProductMaster;

class SimpleRemedyView extends \yii\base\Widget
{
    const POTENCY_X   = 'X';
    const POTENCY_C   = 'C';
    const POTENCY_M   = 'M';
    const POTENCY_LM  = 'LM';
    const POTENCY_COMBI = 'conbination';

    /* @var \common\models\Customer | \backend\models\Staff */
    public $user;

    /* @var \common\models\Remedy */
    public $remedy;

    /* @var \common\models\RemedyStock */
    public $stock;

    /* @var array of [remedy_id => Remedy::abbr] */
    private $_abbrs;

    /* @var array of [potency_id => name] */
    private $_potencies;

    /* @var cache life cycle */
    private $duration = 3600;

    /* @var GridView::emptyText */
    private $emptyText = '';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $user = $this->user;
        if($user && $user->isMemberOf([Membership::PKEY_AGENCY_HJ_A,
                                       Membership::PKEY_AGENCY_HJ_B])
        )
            $user->grade_id = CustomerGrade::PKEY_NA;
    }

    public function getVials()
    {
        $vid = [RemedyVial::MICRO_BOTTLE,
                RemedyVial::SMALL_BOTTLE,
                RemedyVial::LARGE_BOTTLE];

        $vials = RemedyVial::find()->andWhere(['vial_id' => $vid])
                                   ->asArray()
                                   ->select(['vial_id','name'])
                                   ->all();

        return ArrayHelper::map($vials, 'vial_id', 'name');
    }

    public function getPotencies()
    {
        if($this->_potencies)
            return $this->_potencies;

        $potencies = [
            self::POTENCY_X  => 'X',
            self::POTENCY_C  => 'C',
            self::POTENCY_M  => 'M,CM,MM',
        ];

        if(($u = $this->user) && $u->grade_id)
            $potencies[self::POTENCY_LM] = 'LM';

        $this->_potencies = $potencies;

        return $potencies;

    }

    public function getAbbrs()
    {
        if($this->_abbrs)
            return $this->_abbrs;

        $user = $this->user;
        $cache_id = "remedystock-droppables-abbr-" . ($user instanceof Staff ? null : ($user ? $user->grade_id : 0));
        if(! $abbrs = Yii::$app->cache->get($cache_id))
        {
            $query = RemedyStock::find()->active()
                                        ->andWhere(['vial_id'=>[RemedyVial::DROP,
                                                                RemedyVial::MICRO_BOTTLE,
                                                                RemedyVial::MIDDLE_BOTTLE,
                                                                RemedyVial::LARGE_BOTTLE,]])
                                        ->distinct('remedy_id');

            if(! $user instanceof Staff)
                $query->forcustomer($this->user)
                      ->andWhere(['in_stock'=>1]);

            $abbrs = $query->with('remedy')->all();
            
            $abbrs = array_keys(ArrayHelper::map($abbrs, 'remedy.abbr', 'remedy_id'));
            Yii::$app->cache->set($cache_id, $abbrs, $this->duration);
        }
        $this->_abbrs = $abbrs;

        return $this->_abbrs;
    }

    public function run()
    {
    }

    public function renderRemedies()
    {
        echo \yii\grid\GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $this->search(),
            ]),
            'emptyText'   => $this->emptyText,
            'showOnEmpty' => $this->emptyText,
            'tableOptions'=>['class'=>'table table-condensed table-striped table-bordered'],
            'columns' => [
                [
                    'attribute' => 'remedy.abbr',
                    'format'    => 'html',
                    'value'     => function($data)
                    {
                        if($r = $data->remedy) return Html::a($r->name,['/remedy/view','id'=>$r->remedy_id]);
                    },
                ],
                'remedy.ja',
                'potency.name',
                'vial.name',
                [
                    'label'  => '',
                    'format' => 'html',
                    'value'  => function($data)
                    {
                        return Html::a('カートに入れる',
                                        ['cart/remedy/add',
                                         'rid'=>$data->remedy_id,
                                         'pid'=>$data->potency_id,
                                         'vid'=>$data->vial_id
                        ],['class'=>'btn btn-sm btn-warning'])  
                        . ' '. Html::a('もっと見る',['remedy/viewbyname',
                                         'name' => $data->remedy->name,
                                         '#'    => $data->potency->name,
                        ],['class'=>'btn btn-sm btn-info']);
                    },
                    'contentOptions' => ['class'=>'text-center'],
                ],
            ],
            'summaryOptions' => ['class'=>'text-right'],
        ]);
    }

    /* @return ActiveQuery */
    private function search()
    {
        if(! $this->stock->potency_id &&
           ! $this->stock->vial_id    &&
           ! $this->remedy->abbr
        )
            return ProductMaster::find()->where('0 = 1');

        $this->emptyText = 'お探しのレメディーは見つかりませんでした';

        $query = ProductMaster::find()
               ->andWhere(['vial_id' => [ RemedyVial::MICRO_BOTTLE  ,
                                          RemedyVial::SMALL_BOTTLE  ,
                                          RemedyVial::LARGE_BOTTLE  ,
                                          RemedyVial::GLASS_5ML     ,]])
               ->with('remedy')
               ->with('potency')
               ->with('vial');

        // filter by Customer::grade_id
        if($user = $this->user)
            $query->andWhere(['<=', 'restrict_id', $user->grade_id]);
        else
            $query->andWhere(['restrict_id' => 0]);

        // filter by Remedy::abbr
        if($abbr = trim($this->remedy->abbr))
        {
            if($model = Remedy::findOne(['abbr' => $abbr]))
                $query->andWhere(['remedy_id' => $model->remedy_id]);
            else
                $query->andWhere(['remedy_id' => Remedy::find()->andWhere(['like', 'abbr', $abbr])->column() ]);
        }

        // filter by RemedyVial
        if($vid = $this->stock->vial_id)
        {
            $query->andWhere(['vial_id' => $vid]);
        }

        // filter by RemedyPotency
        if($potencies = $this->stock->potency_id)
        {
            $name = [];
            if(in_array(self::POTENCY_COMBI, $potencies))
                $name[] = self::POTENCY_COMBI;

            if(in_array(self::POTENCY_C, $potencies))
                $name[] = '%C';

            if(in_array(self::POTENCY_X, $potencies))
                $name[] = '%X';

            if(in_array(self::POTENCY_M, $potencies))
                $name[] = '%M';

            if(in_array(self::POTENCY_LM, $potencies))
                $name[] = 'LM%';

            $query->andFilterWhere([
                'potency_id' => RemedyPotency::find()->andFilterWhere(['like', 'name', $name, false])
                                                     ->column()
            ]);
        }

        return $query;
    }

}
