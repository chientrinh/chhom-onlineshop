<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\sodan\Interview;
use common\models\sodan\InterviewStatus;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/room/search-menu.php $
 * @version $Id: search-menu.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  common\models\sodan\Room
 */

$csscode = '
div.required label:after {
  content: "";
}';
$this->registerCss($csscode);

$year_min = date('Y');
$year_max = date('Y',strtotime(Interview::find()->max('itv_date')));
$years = range($year_min, $year_max);
$years = array_combine($years, $years);
krsort($years);

$months = range(1, 12);
$months = array_combine($months, $months);

$days   = range(1, 31);
$days   = array_combine($days, $days);

$wday   = [   // MySQL::WEEKDAY() format
    0 => '月', // 0: Monday
    1 => '火', 
    2 => '水',
    3 => '木',
    4 => '金',
    5 => '土',
    6 => '日', // 6: Sunday
];

$branch = \common\models\Branch::find()->center()->all();
$branch = ArrayHelper::map($branch,'branch_id','name');

$hpath = \common\models\CustomerMembership::find()
       ->active()
       ->with('customer')
       ->where(['membership_id'=>\common\models\Membership::PKEY_CENTER_HOMOEOPATH])
       ->all();
$hpath = ArrayHelper::map($hpath,'customer_id','customer.name');
ksort($hpath);

?>

<?php $form = \yii\widgets\ActiveForm::begin([
    'method'      => 'get',
    'fieldConfig' => [
        'template' => '{input}'
    ],
]) ?>

    <?= $form->field($searchModel, 'branch_id')->checkboxList($branch) ?>

    <?= $form->field($dateModel, 'year')->checkboxList($years) ?>
    <?= $form->field($dateModel, 'month')->checkboxList($months) ?>
    <?= $form->field($dateModel, 'wday')->checkboxList($wday) ?>
    <?= $form->field($dateModel, 'day')->checkboxList($days) ?>
    <?= Html::radioList('afternoon',Yii::$app->request->get('afternoon'),[0=>'午前中',1=>'午後']) ?>
    <?= $form->field($searchModel, 'homoeopath_id')->checkboxList($hpath) ?>

    <?= Html::submitbutton('検索',['class'=>'btn btn-primary']) ?>
    <?= Html::a('すべて解除',['index','reset'=>true],['class'=>'btn btn-default']) ?>

<?php $form->end() ?>
