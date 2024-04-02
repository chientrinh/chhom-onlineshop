<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/views/chainstore.php $
 * $Id: chainstore.php 2951 2016-10-10 06:34:52Z mori $
 *
 * @var $model Purchase
 */

$com = $model->company;
$key = $com ? $com->key : '';
$tel = '';
$code= '';
$id  = '';

if('he' == $key){ $tel = '0557-86-3075'; $code = '0000401'; }
if('hj' == $key){ $tel = '0557-86-3070'; $code = '0000348'; }

// とにかく備考欄に数字があればそれを伝票番号とする
if(preg_match('/[0-9]+/u', mb_convert_kana($model->note,'n'), $match))
    $id = array_shift($match);
?>

<style>
body {
    border:  0;
    padding: 0;
    margin:  0;
}
div {
    padding: 0;
    margin-bottom: 0;
    height: 8 mm;
}
#label1 {
    position:absolute;
    top:   59 mm;
    left:  28 mm;
    width: 48 mm;
    height: 4 mm;
//  background: aqua;
}
#label2 {
    position:absolute;
    top:   55 mm;
    left: 186 mm;
    width: 45 mm;
    height:12 mm;
//  background: silver;
}
.top1 {
    float: left;
    width: 136 mm;
    text-align: right;
//  background: orange;
}
.top2 {
    float: left;
    width: 18 mm;
    text-align: right;
//  background: yellow;
}
.top3 {
    float: left;
    width: 79 mm;
    text-align: right;
//  background: pink;
}
.top4 {
    float: left;
    width: 15 mm;
    text-align: center;
//  background: green;
}
.attr1 {
    float: left;
    clear: left;
    width:  60 mm;
    padding-left: 4 mm;
//  background: green;
}
.attr2 {
    float: left;
    width: 30 mm;
//  background: yellow;
}
.attr3 {
    float: left;
    width: 48 mm;
    text-align: right;
//  background: orange;
}
.attr4 {
    position: absolute;
    left: 185mm;
    float: left;
    width: 26 mm;
    text-align: right;
    font-size: 9pt;
//  background: yellow;
}
.attr5 {
    position: absolute;
    left: 212mm;
    float: left;
    width: 26 mm;
    text-align: right;
    font-size: 9pt;
//  background: orange;
}
.bottom {
    position:absolute;
    top:  145 mm;
    left: 217 mm;
    width: 20 mm;
    text-align: right;
    font-size: 9pt;
//  background: pink;
}
</style>
<body>

<div style="position: absolute; top:44mm; left:20mm; height:122mm; width:257mm">
    <img width="100%" height="100%" alt="background" src="<?= $background ?>">
</div>

<div id="label1">
<?= $com ? $com->name : null ?>
</div>

<div id="label2">
<?= $com ? $com->name : null ?><br>
<?= $tel ?>
</div>

<div style="width:100%;height:19mm">
    &nbsp;
</div>

<div class="top1">
    <?= $id ?>
</div>
<div class="top2">
<?= $code ?>
</div>
<div class="top3">
    <?= date('y-m-d', strtotime($model->create_date)) ?>
</div>
<div class="top4">
    <?= date('y-m-d') ?>
</div>

<div style="width:100%;height:2mm">
    &nbsp;
</div>

<?php foreach($model->items as $item): ?>

    <div class="attr1">
        <?= $item->name ?>
    </div>

    <div class="attr2">
        <?= ($m = $item->model) ? $m->barcode : null ?>
    </div>

    <div class="attr3">
        <?= $item->quantity ?>
    </div>

    <div class="attr4">
        <?= $item->charge / min(1, (int)$item->quantity) ?>
    </div>

    <div class="attr5">
        <?= $item->charge ?>
    </div>

<?php endforeach ?>

    <div class="bottom">
        ￥<?= $model->subtotal ?>
    </div>

</body>
