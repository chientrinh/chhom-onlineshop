<?php
namespace backend\models\stat;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/stat/Payoff.php $
 * $Id: MonthlySummary.php 2018-08-29  mori $
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

use Yii;
use \common\models\Branch;
use \common\models\Company;
use \common\models\Category;
use \common\models\Payment;

class Payoff extends \yii\db\ActiveRecord
{
/*
    public $year;
    public $month;
    public $company_id; // 所属会社
*/

    public function init()
    {
        parent::init();

        if(! isset($this->year))
            $this->year = date('Y');

        if(! isset($this->month))
            $this->month = date('m');
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dtb_payoff';
    }

    public function rules()
    {
        return [
            ['company_id', 'exist', 'targetClass'=>Company::className(), 'targetAttribute'=>'company_id'],
            [['year', 'month'], 'integer'],
            [['sales', 'point_given', 'point_consume'], 'default', 'value' => 0],
        ];
    }

    /* @inheritdoc */
    public function behaviors()
    {
        return [
            'date' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['create_date','update_date'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => 'update_date',
                ],
                'value' => function () {
                    return new \yii\db\Expression('NOW()');
                },
            ],
            'log' => [
                'class'  => \common\models\ChangeLogger::className(),
                'owner'  => $this,
                'user'   => Yii::$app->has('user') ? Yii::$app->user : null,
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'year'             => "年",
            'month'            => "月",
            'company_id'       => "所属会社",
            'sale'             => "通販売上",
            'point_given'      => "付与ポイント",
            'point_consume'    => "使用ポイント",
            'create_date'      => "作成日",
            'update_date'      => "更新日",
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'company_id']);
    }
}
