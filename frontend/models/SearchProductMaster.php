<?php

namespace frontend\models;

use Yii;
use \common\models\ProductMaster;
use \common\models\RemedyVial;
use \common\models\Company;
use \common\models\Category;

/**
 * ProductSearch represents the model behind the search form about `common\models\Product`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/models/SearchProductMaster.php $
 * $Id: SearchProductMaster.php 4249 2020-04-24 16:42:58Z mori $
 */
class SearchProductMaster extends \yii\base\Model
{
    public $category_id;
    public $subcategory_id;
    public $keywords;
    public $company;
    public $customer;
    public $ean13;
    public $kana;
    public $name;
    public $price;
    public $vial_id;
    public $potency_id;

    /* @inheritdoc */
    public function rules()
    {
        return [
            [['category_id','subcategory_id'], 'integer'],
            [['category_id'],    'exist', 'targetClass' => \common\models\Category::className() ],
            [['subcategory_id'], 'exist', 'targetClass' => \common\models\Subcategory::className() ],
            [['vial_id'],        'exist', 'targetClass' => \common\models\RemedyVial::className() ],
            [['potency_id'],     'exist', 'targetClass' => \common\models\RemedyPotency::className() ],
            [['keywords'], 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            [['keywords'], 'string', 'max' => 255],
            [['kana'], 'string'],
            [['name', 'price'], 'safe']
 
       ];
    }

    /* @inheritdoc */
    public function attributeLabels()
    {
        return [
            'category' => "カテゴリー",
            'category_id' => "カテゴリー",
            'keywords' => "キーワード",
        ];
    }

    /* @inheritdoc */
    public function attributes()
    {
        return ['category','keywords'];
    }

    /* @inheritdoc */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return parent::scenarios();
    }

    public function init()
    {
        parent::init();

        if(null === $this->customer)
            $this->customer = new \common\models\NullCustomer();
    }

    public function search($params = [], $remedyStock=false)
    {
        $query = ProductMaster::find()->where(['or',
                                               ['vial_id' => null ],
                                               ['not', ['vial_id' => RemedyVial::DROP]]])
                                      ->andWhere(['or',
                                               ['remedy_id' => null ],
                                               ['>', 'remedy_id', 0]])
                                      ->andWhere(['not', ['name' => '']])
                                      ->andWhere(['NOT IN', 'mvtb_product_master.category_id', Category::find()->where(['seller_id'=> Company::PKEY_HP])->select('category_id')]);

        // TODO 2018/09/05 指定顧客が購入できる商品を増やす対応、期間が終了したら削除する
        if ($this->customer->customer_id === 27321) {
            $query->andWhere(['or',
                    ['<=', 'restrict_id', $this->customer->grade_id],
                    ['in', 'product_id', ['471', '474', '475']]
                ]);
        } // -- ここまで --
        else if ($this->subcategory_id == \common\models\Subcategory::PKEY_MAGAZINE_CAMPAIGN)
            // 特典商品の場合は制限を参照しない
            $query->andWhere(['<=', 'restrict_id', 99]);
        else if($this->customer->customer_id)
            $query->andWhere(['<=','restrict_id',$this->customer->grade_id]);
        else // isGuest
            $query->andWhere(['restrict_id' => 0]);

        // ライブ配信チケットはモールでは検索対象外
        if('echom-frontend' != Yii::$app->id)
            $query->andWhere(['NOT IN', 'mvtb_product_master.category_id', [24]]);

        $this->load($params);

        // イベント参加者限定キャンペーンコード入力時処理
        $campaign_code = Yii::$app->session->get('campaign_code');
        if ($campaign_code) {
            $ecampaign = \common\models\EventCampaign::find()->where(['campaign_code' => $campaign_code])->one();
            $query->orFilterWhere(['ean13' => \common\models\ProductSubcategory::find()
                    ->joinWith('subcategory', true, 'INNER JOIN')
                    ->andWhere(['or', [\common\models\Subcategory::tableName().'.subcategory_id' => $ecampaign->subcategory_id], [\common\models\Subcategory::tableName().'.subcategory_id' => $ecampaign->subcategory_id2]])
                    ->select('ean13')
            ]);
        }

        if($this->subcategory_id)
            $query->andFilterWhere(['ean13' => \common\models\ProductSubcategory::find()
                    ->joinWith('subcategory', true, 'INNER JOIN')
                    ->andWhere(['or', [\common\models\Subcategory::tableName().'.subcategory_id' => $this->subcategory_id], [\common\models\Subcategory::tableName().'.parent_id' => $this->subcategory_id]])
                    ->select('ean13')
        ]);
        if(strlen($this->kana))
            $query->andFilterWhere(['or',
                                   ['like', 'kana', $this->kana],
                                   ['like', 'name', $this->kana]]);

        if(strlen($this->name))
            $query->andFilterWhere(['or',
                                   ['like', 'kana', $this->name],
                                   ['like', 'name', $this->name]]);


        if(strlen($this->price))
            $query->andFilterWhere(['like', 'mvtb_product_master.price', $this->price]);

        if($this->company)
            $query->company($this->company);

        if($this->category_id)
            $query->andFilterWhere(['category_id' => $this->category_id]);

        if($this->vial_id)
            $query->andFilterWhere(['vial_id' => $this->vial_id]);

        if($this->potency_id)
            $query->andFilterWhere(['potency_id' => $this->potency_id]);

        if($this->keywords &&
          ($keywords = \common\components\KanaHelper::split($this->keywords))
        ){
            $whereKeywordsArray = ['AND'];
            foreach(\common\components\KanaHelper::split($this->keywords) as $keyword) {
                $whereKeywordsArray[] = ['like', 'keywords', $keyword];
            }
            $query->andFilterWhere(['OR',
                                   ['like', 'kana',        $keywords],
                                   ['like', 'name',        $keywords],
            ])->orFilterWhere($whereKeywordsArray);
            $this->keywords = implode(' ', $keywords); // restore keywords
        }
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query->distinct(),
        ]);

        return $dataProvider;
    }


    /**
     * 代理店向け機能　卸売かご、小売りレジの商品検索
     *
     **/
    public function searchForAgency($params = [], $remedyStock=false)
    {
        $query = ProductMaster::find()
                ->where(['or',
                            ['vial_id' => null ],
                            ['not', ['vial_id' => RemedyVial::DROP]]])
                ->andWhere(['not', ['name' => '']])
                ->andWhere(['NOT IN', 'mvtb_product_master.category_id', Category::find()->where(['seller_id'=> Company::PKEY_HP])->select('category_id')]);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query->distinct(),
        ]);

        if($this->customer->customer_id) {
            $query->andWhere(['<','restrict_id',99]);
            $query->orWhere(['ean13' => \common\models\ProductSubcategory::find()
                    ->joinWith('subcategory', true, 'INNER JOIN')
                    ->andWhere(['or', 
                        [\common\models\Subcategory::tableName().'.subcategory_id' => \common\models\Subcategory::PKEY_ONLY_HE], 
                        [\common\models\Subcategory::tableName().'.parent_id' => \common\models\Subcategory::PKEY_ONLY_HE]])
                    ->select('ean13')]);
        } else {  // isGuest
            $query->andWhere(['restrict_id' => 0]);
        }

        $this->load($params);

        // イベント参加者限定キャンペーンコード入力時処理
        $campaign_code = Yii::$app->session->get('campaign_code');
        if ($campaign_code) {
            $ecampaign = \common\models\EventCampaign::find()->where(['campaign_code' => $campaign_code])->one();
            $query->orFilterWhere(['ean13' => \common\models\ProductSubcategory::find()
                    ->joinWith('subcategory', true, 'INNER JOIN')
                    ->andWhere(['or', [\common\models\Subcategory::tableName().'.subcategory_id' => $ecampaign->subcategory_id], [\common\models\Subcategory::tableName().'.parent_id' => $ecampaign->subcategory_id]])
                    ->select('ean13')
            ]);
        }

        if($this->subcategory_id)
            $query->andFilterWhere(['ean13' => \common\models\ProductSubcategory::find()
                    ->joinWith('subcategory', true, 'INNER JOIN')
                    ->andWhere(['or', [\common\models\Subcategory::tableName().'.subcategory_id' => $this->subcategory_id], [\common\models\Subcategory::tableName().'.parent_id' => $this->subcategory_id]])
                    ->select('ean13')
        ]);
        if(strlen($this->kana))
            $query->andFilterWhere(['or',
                                   ['like', 'kana', $this->kana],
                                   ['like', 'name', $this->kana]]);

        if(strlen($this->name))
            $query->andFilterWhere(['or',
                                   ['like', 'kana', $this->name],
                                   ['like', 'name', $this->name]]);


        if(strlen($this->price))
            $query->andFilterWhere(['like', 'mvtb_product_master.price', $this->price]);

        if($this->company)
            $query->company($this->company);

        if($this->category_id)
            $query->andFilterWhere(['category_id' => $this->category_id]);

        if($this->vial_id)
            $query->andFilterWhere(['vial_id' => $this->vial_id]);

        if($this->potency_id)
            $query->andFilterWhere(['potency_id' => $this->potency_id]);

        if($this->keywords &&
          ($keywords = \common\components\KanaHelper::split($this->keywords))
        ){
            $query->andFilterWhere(['OR',
                                   ['like', 'kana',        $keywords],
                                   ['like', 'name',        $keywords],
            ]);
            $this->keywords = implode(' ', $keywords); // restore keywords
        }


        $ean13s = array();

        if(Company::PKEY_HJ == $this->company) {
            // 酒販売免許を所持していない場合
            if(!$this->customer->hasLiquorLicense()) {
                // 酒類を排除する
                $dataProvider->query->andWhere(['or', ['vial_id' => null], ['not in', 'vial_id', RemedyVial::isLiquorVials()]]);
            }
            // HJ代理店用割引対象外商品のean13配列を取得
            $ean13s = $this->getExcludes([\common\models\Subcategory::PKEY_HJ_AGENCY_EXCLUDE]);
            $dataProvider->query->andWhere(['not like', 'name', '特別レメディー']);
        }

        if(Company::PKEY_HE == $this->company) {

            // ライブ配信チケット販売用処理 2020/04/21 : kawai
            if('echom-frontend' == Yii::$app->id) {
                $dataProvider->query->andWhere(['in', 'category_id', [Category::LIVE]]);
            } else {
                // 表示すべきカテゴリ
                $dataProvider->query->andWhere(['in', 'category_id', [Category::FOOD, Category::COSMETIC, Category::GOODS]]);


                // HE代理店用割引対象外商品のean13配列を取得
                $ean13s = $this->getExcludes([\common\models\Subcategory::PKEY_HE_AGENCY_EXCLUDE]);
            }
        }

        if(Company::PKEY_HP == $this->company) {

            // HP代理店用割引対象外商品のean13配列を取得
            $ean13s = $this->getExcludes([\common\models\Subcategory::PKEY_HP_AGENCY_EXCLUDE,\common\models\Subcategory::PKEY_HP_OTHER_PUBLISHER]);

        }

        // 代理店用割引対象外商品を除外する
        $dataProvider->query->andWhere(['not in', 'ean13', $ean13s])->orderBy(['dsp_priority' => SORT_DESC]);        

        return $dataProvider;
    }

    private function getExcludes($subcategory_ids)
    {
        $excludes = \common\models\ProductSubcategory::find()
                                            ->select('ean13')
                                            ->where(['in', 'subcategory_id', $subcategory_ids])
                                            ->all();

        return yii\helpers\ArrayHelper::getColumn($excludes, 'ean13');

    }

}
