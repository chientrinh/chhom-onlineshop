<!DOCTYPE html>
<html lang="ja-JP">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>豊受モール</title>
</head>
<body id="Home">
 <div class="wrap">
      <?= \yii\helpers\Html::a(\yii\helpers\Html::img("@web/img/under_construction.jpg", [
          'alt'  =>"ただいま工事中です",
          'style'=>'max-width:100%; min-width:800px;']),[
              '/magazine',
          ],[
              'title' => "一部公開中のページへジャンプします",
          ]
     )?>
</div>
</body>
</html>
