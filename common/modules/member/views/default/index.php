<?php
/**
* $URL: https://tarax.toyouke.com/svn/MALL/common/modules/member/views/default/index.php $
* $Id: index.php 2991 2016-10-19 06:05:24Z mori $
*
* @var $dataProvider \yii\data\ActiveDataProvider
* @var $searchModel  \common\models\SearchMember
* @var $this         \yii\web\View
*/

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use \common\models\CustomerMembership;
use \common\models\Membership;
use \common\models\Payment;

$title = \yii\helpers\ArrayHelper::getValue($this,'context.title', "会員");
$this->title = sprintf('一覧 | %s | %s', $title , Yii::$app->name);

$intraRequest = null;
if($cookie = Yii::$app->request->cookies->get('ebisu-intra-request-json'))
    $intraRequest = \yii\helpers\Json::decode($cookie);

if(Yii::$app->user->identity instanceof \backend\models\Staff)
    $pid = Payment::PKEY_BANK_TRANSFER;
else
    $pid = Payment::PKEY_CASH;
?>

<div class="customer-default-index col-md-12">
    <h1><?= $title ?></h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'emptyText'    => '条件に一致する会員は見つかりませんでした',
        'columns' => [
            [
                'attribute' => 'code',
            ],
            [
                'attribute' => 'name',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->name, ['view','id'=>$data->customer_id]); },
            ],
            [
                'attribute' => 'pref_id',
                'value'     => function($data){ return ArrayHelper::getValue($data, 'pref.name'); },
            ],
            [
                'label'     => '会員種別',
                'attribute' => 'membership_id',
                'format'    => 'html',
                'value'     => function($data)use($pid){
                   if($data->getParent()->exists())
                       return '家族会員';

                    if($data->isMember())
                    {
                        $query = CustomerMembership::find()
                           ->toranoko()
                           ->andWhere(['customer_id' => $data->customer_id])
                           ->andWhere(['>','expire_date', new \yii\db\Expression('DATE_ADD(NOW(), INTERVAL 1 YEAR)')]) // 365 日以上未来の会員権がある
                           ->orderBy(['expire_date' => SORT_DESC]);

                        $html = '有効';

                        if(! $query->exists())
                            $html .= Html::a("延長",['update','id'=>$data->customer_id,'pid'=>$pid],['class'=>'btn btn-xs btn-primary pull-right']);

                        return $html;
                    }

                    if($data->wasMember()) return " 期限切れ" . Html::a("再開",['update','id'=>$data->customer_id,'pid'=>$pid],['class'=>'btn btn-xs btn-success pull-right']);

                    return "未入会" . Html::a("入会",['update','id'=>$data->customer_id,'pid'=>$pid],['class'=>'btn btn-xs btn-warning pull-right']);
                }
            ],
            [
                'label'  => '',
                'format' => 'raw',
                'value'  => function($data)use($intraRequest)
                {
                    if(! $intraRequest) return null;

                    $url = $intraRequest['route'] . sprintf('&customer_id=%d',$data->customer_id);
                    return Html::a("予約",$url,['class'=>'btn btn-xs btn-danger','title'=>$intraRequest['title']]);
                },
                'visible' => $intraRequest,
            ],
        ],
    ]) ?>
<?php $form = \yii\bootstrap\ActiveForm::begin(); ?>

    <p class="help-block">
        会員証NOまたは電話番号を入力してください
    </p>

    <?= $form->field($searchModel, 'tel')->label(false)->textInput([
        'name'        => 'tel',
        'style'       => 'width:50%',
        'placeholder' => '0000000000',
    ]) ?>

    <p class="pull-left">
    <?= Html::submitbutton('検索',['class'=>'btn btn-success']) ?>
    </p>

<?php $form->end() ?>

    <p class="pull-right">
    <?= Html::a('新規 入会手続き',['create'],['class'=>'btn btn-warning']) ?>
    </p>

</div>
