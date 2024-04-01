<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtoremedyabc.php $
 * $Id: howtoremedyabc.php 3492 2017-07-11 03:20:33Z naito $
 *
 * @var $this \yii\web\View
 */

$title = "商品の選び方（レメディーABCから検索）";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtoremedyabc';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <h3>商品の選び方（レメディーABCから検索）</h3>
    <p>【レメディー・ハーブ酒】をクリックします。</p>
    <p align="center"><?= Html::img('@web/img/how_to_remedy_01.jpg') ?></p>
    <p><br></p>
    <p>レメディー検索の下のアルファベットＡ〜Ｚをクリックします。</p>
    <p align="center"><?= Html::img('@web/img/how_to_remedy_2_01.jpg') ?></p>
    <p><br></p>
    <p>例として、Aconを検索してみましょう。</p>
    <p align="center"><?= Html::img('@web/img/how_to_remedy_2_02.jpg') ?></p>
    <p><br></p>
    <p>ポーテンシーを選択します。<br>
       200Cのタブを選ぶと、200Cの商品が表示されます。<br>
       容器（小瓶、大瓶、アルポ(5ml)のどれか）を選んでから、<br>【カートに入れる】ボタンをクリックします。</p>
    <p align="center"><?= Html::img('@web/img/how_to_remedy_2_03.jpg') ?></p>
    <p><br></p>
    <p>LMポーテンシーを検索することもできます。<br>（LMポーテンシーは、ログインした場合のみ表示されます。） </p>
    <p align="center"><?= Html::img('@web/img/how_to_remedy_2_04.jpg') ?></p>
    <p><br></p>
</div>