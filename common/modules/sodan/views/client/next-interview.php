<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/sodan/views/next-interview.php $
 * $Id: next-interview.php 2370 2016-04-03 09:02:34Z mori $
 *
 * $model \common\models\sodan\Interview
 */
use yii\helpers\Html;

$cdate = strtotime($model->itv_date);
$days  = 3;
for($i = 0; $i < $days; $i++)
{
    do{
        $cdate -= (60 * 60 * 24);
        $exp    = date('Y-m-d', $cdate);
        $query  = \common\models\sodan\Holiday::find()->active()
                                                      ->andWhere(['homoeopath_id'=> null,
                                                                  'date'         => $exp]);
    }
    while($query->exists());
}

?>

<style type="text/css">
<!--
h1 {
   font-size: 14pt;
}
h2 {
   font-size  :16pt;
   font-weight:bold;
   text-align :center;
}
-->
</style>

<page>

    <p style="text-align:right">
        健康相談会 <?= Yii::$app->formatter->asDate(date('Y-m-d'), 'php:Y年m月d日(D)') ?>
    </p>

    <p>
        相談会当日、必ずご持参ください。
    </p>
    <h2>
        次回のご相談
    </h2>

    <p>
        &nbsp;
    </p>

<?= \yii\widgets\DetailView::widget([
    'model'=>$model,
    'attributes' => [
        [
            'label'  => '',
            'format' => 'html',
            'value'  => '<h1>&nbsp;</h1>',
        ],
        [
            'label' => 'ご予約者',
            'value' => ($c = $model->client) ? $c->name . ' 様' : '',
        ],
        [
            'label'  => '',
            'format' => 'html',
            'value'  => '<h1>&nbsp;</h1>',
        ],
        [
            'label'  => 'ご予約日時',
            'format' => 'html',
            'value'  => Html::tag('h1',Yii::$app->formatter->asDate($model->itv_date,'php:Y年m月d日(D)')
                      . '&nbsp;'
                      . Yii::$app->formatter->asDate($model->itv_time,'php:H時i分'))
        ],
        [
            'attribute' => 'homoeopath_id',
            'format' => 'html',
            'value'     => Html::tag('h1',($h = $model->homoeopath) ? $h->name . ' 先生' : ''),
        ],
        [
            'label'  => '会場',
            'format' => 'html',
            'value'  => ($b = $model->branch)
                ? Html::tag('h1', $b->name)
                . Html::tag('p',  $b->addr)
                . Html::tag('p',  $b->tel)
                    : null,
        ],
        [
            'label'     => '相談会種別',
            'attribute' => 'product.name',
        ],
        [
            'label'  => 'キャンセル期限',
            'format' => 'html',
            'value'  => Yii::$app->formatter->asDate($cdate,'php:Y年m月d日(D) 17:00'),
        ],
        [
            'label'  => '',
            'format' => 'html',
            'value'  => '<h1>&nbsp;</h1>',
        ],
        [
            'label'  => '備考',
            'format' => 'html',
            'value'  => '<h1>&nbsp;</h1>',
        ],
    ],
]) ?>

    <p>
    <?= nl2br(sprintf('※予約の変更・キャンセルは健康相談会の3営業日前17時00分までにお電話にてお願いします。以降の変更は手数料（キャンセル料）を頂戴致します。（月曜・火曜・年末年始はお休みとなりますのでご注意ください。）
※手数料（キャンセル料金）
大人（中学生以上）     ：6,000円（税別）
小人(小学生以下)・動物：4,000円（税別）
※こちらのお客様控えは、お手数をお掛けいたしますが、相談会当日お持ちいただき、受付にてスタッフにお渡しください。
※当日来場が難しい場合は、電話相談に変更する事が可能です。
※お問い合わせは %s までお電話ください（9：30～17：30.月･火曜・年末年始を除く）',
( ($b = $model->branch) ? $b->tel : null )
)); ?>
    </p>

</page>
