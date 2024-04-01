<?php
namespace backend\models\stat;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/stat/AgencyCsvSummary.php $
 * $Id: AgencyCsvSummary.php 2020-03-10  kawai $
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

use Yii;
use \common\models\Branch;
use \common\models\Company;
use \common\models\Purchase;
use \common\models\PurchaseItem;
use \common\models\PurchaseStatus;
use \common\models\RemedyVial;
use \common\models\Category;

class AgencyCsvSummary extends \yii\base\Model
{
    public $start_date;
    public $end_date;
    public $company_id;
    public $branch_id;
    public $class; // 分類
    public $payment_id;
    public $agency;
    public $customer_id;
    private $_purchaseCount;
    private $_itemCount;

    public function init()
    {
        parent::init();

        if(! isset($this->start_date))
            $this->start_date = date('Y-m-d 00:00:00');

        if(! isset($this->end_date))
            $this->end_date = date('Y-m-d 23:59:59');

        if(!isset($this->agency))
            $this->agency = 1;
    }

    public function rules()
    {
        return [
            ['company_id', 'exist', 'targetClass'=>Company::className(), 'targetAttribute'=>'company_id'],
            ['branch_id',  'exist', 'targetClass'=> Branch::className(), 'targetAttribute'=> 'branch_id'],
            [['start_date','end_date'], 'date'],
            [['agency'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'customer_id'    => "顧客ID",
            'name'           => "顧客名",
            'quantity'       => "数量",
            'price_total'    => "定価合計（税別）",
            'wholesale_total_excluding_tax'   => "卸価格合計（税別）",
            'tax'           => "税額",
            'wholesale_total'   => "卸価格合計（税込）",
            'hj_agency_rank'  => "HJ代理店ランク",
            'he_agency_rate'  => "HE代理店割引率",
            'agency'         => "",
            'start_date'     => "開始日",
            'end_date'       => "終了日"
        ];
    }

    public function getQuery()
    {
        $model = new AgencyCsvSummary;

        if($this->agency == 1) {
            $sql = "SELECT
  p.customer_id AS customer_id
  , CONCAT(c.name01, c.name02) AS name
  , SUM(pi.quantity) AS quantity
  , SUM(pi.price * pi.quantity) AS price_total
  , SUM(pi.unit_price * pi.quantity) AS wholesale_total_excluding_tax
  , SUM(pi.unit_tax * pi.quantity) AS tax
  , SUM((pi.unit_price + pi.unit_tax) * pi.quantity) AS wholesale_total
  , rank.name AS hj_agency_rank
FROM
  dtb_purchase p 
  LEFT JOIN dtb_purchase_item pi 
    ON p.purchase_id = pi.purchase_id 
  LEFT JOIN dtb_customer c 
    ON c.customer_id = p.customer_id
  LEFT JOIN dtb_customer_agency_rank c_rank
    ON c_rank.customer_id = p.customer_id
  LEFT JOIN mtb_agency_rank rank
    ON rank.rank_id = c_rank.rank_id 
WHERE
  p.customer_id IN ( 
    SELECT
      c.customer_id 
    FROM
      dtb_customer c 
      LEFT JOIN dtb_customer_membership cm 
        ON c.customer_id = cm.customer_id 
    WHERE
      cm.membership_id IN (13, 14) 
     AND cm.start_date <= p.create_date AND cm.expire_date >= p.create_date
    GROUP BY
      cm.customer_id
  ) 
  AND pi.company_id = 2 
  AND p.create_date BETWEEN '".$this->start_date."' AND '".$this->end_date."' 
  AND p.status <= 7 
GROUP BY
  p.customer_id";
            $query =  Yii::$app->db->createCommand($sql);

        } else if($this->agency == 2) { // HE
            $sql = "SELECT
  p.customer_id AS customer_id
  , CONCAT(c.name01, c.name02) AS name
  , SUM(pi.quantity) AS quatity
  , SUM(pi.price * pi.quantity) AS price_total
  , SUM(pi.unit_price * pi.quantity) AS wholesale_total_excluding_tax
  , SUM(pi.unit_tax * pi.quantity) AS tax
  , SUM((pi.unit_price + pi.unit_tax) * pi.quantity) AS wholesale_total
  , rate.discount_rate AS he_agency_rate
FROM
  dtb_purchase p 
  LEFT JOIN dtb_purchase_item pi 
    ON p.purchase_id = pi.purchase_id 
  LEFT JOIN dtb_customer c 
    ON c.customer_id = p.customer_id 
  LEFT JOIN rtb_agency_rating rate 
    ON rate.customer_id = p.customer_id 
WHERE
  p.customer_id IN ( 
    SELECT
      c.customer_id 
    FROM
      dtb_customer c 
      LEFT JOIN dtb_customer_membership cm 
        ON c.customer_id = cm.customer_id 
    WHERE
      cm.membership_id = 12
        AND cm.expire_date >= p.create_date AND cm.start_date <= p.create_date
    GROUP BY
      cm.customer_id
  ) 
  AND pi.company_id = 3 
  AND p.create_date BETWEEN '".$this->start_date."' AND '".$this->end_date."'
  AND p.status <= 7 
GROUP BY
  p.customer_id";
            $query =  Yii::$app->db->createCommand($sql);
            
        }
        return $query;
    }


    public function getTax()
    {
        $value = (int) $this->query->sum('tax');

        return $this->company_id ? (float) ($value * $this->share) : $value;
    }

    public function getTotalCharge()
    {
        $value = (int) $this->query->sum('total_charge');

        return $this->company_id ? (float) ($value * $this->share) : $value;
    }

    private static function narrowQuery($query, $branch_id)
    {
        $query->andWhere(['dtb_purchase.branch_id' => $branch_id]);
    }

}
