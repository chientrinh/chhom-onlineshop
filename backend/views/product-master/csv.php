<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-master/csv.php $
 * $Id: csv.php 2685 2016-07-09 02:32:05Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider of RemedyStock
 */

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

class MyWidget
{
    public static function getValues($model)
    {
        $vendor   = strtoupper(ArrayHelper::getValue($model,'category.vendor.key'));
        $seller   = strtoupper(ArrayHelper::getValue($model,'category.seller.key'));
        $remedy   = ArrayHelper::getValue($model,'remedy.abbr');
        $potency  = ArrayHelper::getValue($model,'potency.name');
        $vial     = ArrayHelper::getValue($model,'vial.name');
        $category = ArrayHelper::getValue($model,'category.name');
        $restrict = ArrayHelper::getValue($model,'restriction.name');
        $name     = ArrayHelper::getValue($model,'name');
        $kana     = ArrayHelper::getValue($model,'kana');
        $ean13    = ArrayHelper::getValue($model,'ean13');
        $price    = ArrayHelper::getValue($model,'price');
        $expired  = false;

        if($product = $model->product)
        {
            $code = ArrayHelper::getValue($product,'code');
            $pick = ArrayHelper::getValue($product,'pickcode');

            $expired = $model->product->isExpired();
        }
        elseif($stock = $model->stock)
        {
            $code   = ArrayHelper::getValue($stock,'code');
            $pick   = ArrayHelper::getValue($stock,'pickcode');
        }
        else
        {
            $code = '';
            $pick = '';
        }

        $row = [$ean13, $name, $kana, $model->dsp_priority, $seller, $vendor, $category, $price, $code, $pick, $remedy, $potency, $vial, $restrict, $model->in_stock];

        foreach($row as $k => $v)
            $row[$k] = strtr($v, ["\r"=>' ', "\n"=>' ', ","=>'&#44', '<br>'=>' ']);
        return $row;
    }
}

?>
# name,kana,dsp_priorityのみCSV一括編集に対応してます<br>
# ean13は主キーのため編集不可<br>
# <?= date('Y-m-d H:i:s') ?><br>
ean13,name,kana,dsp_priority,販社,供給社,カテゴリー,定価,品番,PICK,REMEDY,POTENCY,容器,公開区分,在庫<br>
<?php
$i = 0;
foreach($dataProvider->query->each() as $model)
{
    $values = \MyWidget::getValues($model);
    echo implode(',', $values),"<br>\n";

    $i++;
}
?>
以上<?= $i ?>点
