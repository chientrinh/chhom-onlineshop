<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/webdb/controllers/LiquorController.php $
 * $Id: LiquorController.php 3258 2017-04-19 07:07:41Z kawai $
 */

namespace app\modules\webdb\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ArrayDataProvider;

// ログインチェックをbeforeActionで行っているBaseControllerを継承させる
class LiquorController extends \backend\controllers\BaseController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionView($month=null,$year=null)
    {
        if(!$month)
            $month = date('m');
        if(!$year)
            $year  = date('Y');

        $matrix  = \backend\components\webdb\Liquor::getMatrixByVolume();
        $command = sprintf("
with t2 as
(with t1 as
(select
 d.denpyo_date as date
,d.denpyo_centerid as center
,i.d_item_syohin_num as pcode
,sum(i.d_item_syohin_count) as qty
,(case
  when i.d_item_syohin_num in ('%s') then   5
  when i.d_item_syohin_num in ('%s') then   8
  when i.d_item_syohin_num in ('%s') then  10
  when i.d_item_syohin_num in ('%s') then  15
  when i.d_item_syohin_num in ('%s') then  20
  when i.d_item_syohin_num in ('%s') then  50
  when i.d_item_syohin_num in ('%s') then 100
  when i.d_item_syohin_num in ('%s') then 150
  when i.d_item_syohin_num in ('%s') then 170
  when i.d_item_syohin_num in ('%s') then 500
  when i.d_item_syohin_num in ('%s') then 720
  else 0 end) as vol
 from tbld_item i
join tbldenpyo d on d.denpyo_num = i.denpyo_num and d.denpyo_num_division = i.denpyo_num_division
where i.d_item_syohin_num in
(select syo_mas_num 
from tblsyo_mas
 where
     syo_mas_num not in ('KM155A','KM156A','KM157A','KM062A','KM063A','KM064A')
 and syo_mas_num <> 'K'
 and syo_mas_num like 'K%%')
 and (d.denpyo_date like '%04d/%02d/%%')
 and d.customerid not in (66950,188884,189252) -- HJ Tokyo, HJ Osaka, HE Tokyo
group by i.d_item_syohin_num, d.denpyo_date, d.denpyo_centerid)
select t1.center, t1.date, sum(t1.qty * t1.vol) as ml from t1
left join tmdenpyo_center c on c.denpyo_centerid = t1.center
group by t1.date, t1.center)
select c.denpyo_center as center, t2.date, t2.ml from t2
left join tmdenpyo_center c on c.denpyo_centerid = t2.center
order by t2.center, t2.date
",
  implode("','", $matrix[  5]),
  implode("','", $matrix[  8]),
  implode("','", $matrix[ 10]),
  implode("','", $matrix[ 15]),
  implode("','", $matrix[ 20]),
  implode("','", $matrix[ 50]),
  implode("','", $matrix[100]),
  implode("','", $matrix[150]),
  implode("','", $matrix[170]),
  implode("','", $matrix[500]),
  implode("','", $matrix[720]),
  $year, $month);

        Yii::$app->webdb18->charset = 'utf8';

        $rows = Yii::$app->webdb18->createCommand($command)->queryAll();

        $provider = new ArrayDataProvider([
                'allModels' => $rows,
                'sort' => [
                    'attributes' => ['center', 'date', 'ml'],
                    ],
                'pagination' => false,
                ]);

        return $this->render('view', ['year'=>$year,'month'=>$month,'provider'=>$provider]);
    }
}
