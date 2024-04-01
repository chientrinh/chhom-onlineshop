<?php

namespace backend\models;

use Yii;
use \common\models\Branch;
use \common\models\Company;
use \common\models\Purchase;

/**
 * This is the model class for table "rtb_purchase_survery".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/PurchaseSurvey.php $
 * $Id: PurchaseSurvey.php 3407 2017-06-07 06:38:33Z naito $
 *
 * @property string $target_date
 * @property integer $company_id
 * @property integer $branch_id
 * @property integer $sales
 * @property integer $subtotal
 * @property integer $tax
 * @property integer $discount
 * @property integer $discount_item
 * @property integer $postage
 * @property integer $handling
 * @property integer $point_consume
 * @property integer $point_given
 * @property string $create_date
 * @property integer $created_by
 *
 * @property Staff $createdBy
 * @property Branch $branch
 * @property Company $company
 */
class PurchaseSurvey extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rtb_purchase_survery';
    }

    /* @inheritdoc */
    public function behaviors()
    {
        return [
            'date' => [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'create_date',
                'updatedAtAttribute' => 'create_date',
                'value' => new \yii\db\Expression('NOW()'),
            ],
            'staff' => [
                'class' => \yii\behaviors\BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'created_by',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['target_date', 'company_id', 'branch_id', 'subtotal', 'tax', 'discount', 'discount_item', 'postage', 'handling', 'point_consume', 'point_given'],'required'],
            [['target_date', 'create_date'], 'safe'],
            [['sales', 'subtotal', 'tax', 'discount', 'discount_item', 'postage', 'handling', 'point_consume', 'point_given'], 'integer'],
            [['target_date', 'company_id', 'branch_id'], 'unique', 'targetAttribute' => ['target_date', 'company_id', 'branch_id'], 'message' => '対象日, 販社, 拠点 の組み合わせが重複しています'],
            ['company_id','exist', 'targetClass' => Company::className()],
            ['branch_id', 'exist', 'targetClass' => Branch::className()],
            ['created_by', 'exist','targetClass' => Staff::className(), 'targetAttribute'=>'staff_id'],
            ['sales','default','value'=> new \yii\db\Expression('subtotal + tax + postage + handling - discount') ],
            ['create_date', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'target_date'   => '対象日',
            'company_id'    => '販社',
            'branch_id'     => '拠点',
            'sales'         => '売上高',
            'subtotal'      => '小計',
            'tax'           => '消費税',
            'discount'      => '値引き',
            'discount_item' => '単品値引き',
            'postage'       => '送料',
            'handling'      => '代引手数料',
            'point_given'   => 'Pt付与',
            'point_consume' => 'Pt値引',
            'create_date'   => '操作日',
            'created_by'    => '操作者',
        ];
    }

    public static function createModels($date, $branch_id)
    {
        $start = $date . ' 00:00:00';
        $end   = $date . ' 23:59:59';

        $query = Purchase::find()->active()
                                 ->andWhere(['between','create_date',$start,$end])
                                 ->andWhere(['branch_id' => $branch_id]);

        $matrix = [];
        foreach(Company::find()->all() as $company)
        {
            $matrix[$company->company_id] = [
                'subtotal'      => 0,
                'tax'           => 0,
                'discount'      => 0,
                'discount_item' => 0,
                'postage'       => 0,
                'handling'      => 0,
                'point_consume' => 0,
                'point_given'   => 0,
            ];
        }

        foreach($query->batch() as $rows) foreach($rows as $model)
        {
            $buf    = [];
            $charge = [];
            $share  = [];
            foreach(array_keys($matrix) as $com)
            {
                $buf[$com]    = array_fill_keys(array_keys($matrix[$com]), 0);
                $charge[$com] = 0;
                $share[$com]  = 0;
            }

            foreach($model->items as $item)
            {
                $com = $item->company_id;

                $buf[$com]['point_given']   += $item->pointTotal;
                $buf[$com]['discount_item'] += $item->discountAmount;

                $charge[$com]               += $item->charge; // 商品小計のSUMを会社ごとに累計
            }

            foreach($model->companies as $company)
            {
                $com = $company->company_id;
                $share[$com] = @($charge[$com] / array_sum($charge)); // 各社の占有率を計算しておく
            }

            $remain = [];
            foreach(['subtotal','tax','point_consume','discount','postage','handling'] as $attr)
            {
                $remain[$attr] = $model->$attr;
            }

            asort($share); // sort by value, low to high
            foreach($share as $com => $value)
            {
                foreach(['subtotal','tax','point_consume','discount','postage','handling'] as $attr)
                {
                    $total   = $model->$attr;
                    $portion = round($model->$attr * $value); // 四捨五入(試験的に)

                    $buf[$com][$attr] = $portion;
                    $remain[$attr]   -= $portion;
                }
            }
            // foreach()を抜けたが、$com は最後の値を保持している
            foreach($remain as $attr => $value)
            {
                if(0 != $value)
                    $buf[$com][$attr] += $value; // 按分の誤差を占有シェア最大のCompanyに与える
            }

            foreach(array_keys($matrix) as $com)
            {
                $matrix[$com]['subtotal']      += $buf[$com]['subtotal'];
                $matrix[$com]['tax']           += $buf[$com]['tax'];
                $matrix[$com]['discount']      += $buf[$com]['discount'];
                $matrix[$com]['discount_item'] += $buf[$com]['discount_item'];
                $matrix[$com]['postage']       += $buf[$com]['postage'];
                $matrix[$com]['handling']      += $buf[$com]['handling'];
                $matrix[$com]['point_consume'] += $buf[$com]['point_consume'];
                $matrix[$com]['point_given']   += $buf[$com]['point_given'];
            }
        }

        $models = [];
        foreach($matrix as $com => $stat)
        {
            if(0 == array_sum($stat)) // Attributeがすべてゼロ
                continue;

            $model = new PurchaseSurvey([
                'target_date'=> $date,
                'company_id' => $com,
                'branch_id'  => $branch_id
            ]);

            $model->load($stat, '');

            $models[] = $model;
        }

        return $models;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStaff()
    {
        return $this->hasOne(Staff::className(), ['staff_id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['branch_id' => 'branch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'company_id']);
    }
}
