<?php
use \yii\widgets\ListView;
use \yii\data\ArrayDataProvider;

?>



<!-- レメディーの補足説明 -->
<table class="table table-bordered">

<?= ListView::begin([
            'dataProvider' => new ArrayDataProvider([
                    'allModels'  => $descriptions['remedyDescriptions'],
                    'pagination' => false,
            ]),
            'itemView'     => function ($d, $key, $index, $widget){
                        $layout = '<tr data-key="%d"><th>%s</th><td style=\"word-break:break-all;\">%s</td></tr>';
                        return sprintf($layout, $key, $d->title, nl2br($d->body));
                    }
            ])->renderItems ();

?>
</table>

<!-- レメディーカテゴリーの補足説明 -->
<table class="table table-bordered">

<?= ListView::begin([
            'dataProvider' => new ArrayDataProvider([
                    'allModels'  => $descriptions['categroyDescriptions'],
                    'pagination' => false,
            ]),
            'itemView'     => function ($d, $key, $index, $widget){
                        $layout = '<tr data-key="%d"><th>%s</th><td style=\"word-break:break-all;\">%s</td></tr>';
                        return sprintf($layout, $key, $d->title, nl2br($d->body));
                    }
            ])->renderItems ();

?>
</table>

