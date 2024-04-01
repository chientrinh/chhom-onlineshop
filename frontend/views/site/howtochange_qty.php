<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtochange_qty.php $
 * $Id: howtochange_qty.php 3401 2017-06-06 07:45:33Z naito $
 *
 * @var $this \yii\web\View
 */

$title = "数量の変更方法";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtochange_qty';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <h3>数量の変更方法</h3>
    <p>カート（お買い物かご）で、数量の数字をクリックすると、数量の入力が直接できるようになります。<br>（【＋】と【−】をクリックしても変更できます。）</p>
    <p><?= Html::img('@web/img/how_to_change_qty_01.jpg') ?></p>
    <p><?= Html::img('@web/img/how_to_change_qty_02.jpg') ?></p>
    <p><br></p>
    <p>数量を入力して【更新】ボタンをクリックします。</p>
    <p><?= Html::img('@web/img/how_to_change_qty_03.jpg') ?></p>
    <p><?= Html::img('@web/img/how_to_change_qty_04.jpg') ?></p>
    <p><br></p>

</div>