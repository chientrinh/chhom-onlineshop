<?php
use \yii\widgets\ListView;
use \yii\data\ArrayDataProvider;
?>

<table class="table table-bordered">

<?= ListView::begin([
            'dataProvider' => new ArrayDataProvider([
                    'allModels'  => $descriptions,
                    'pagination' => false,
            ]),
            'itemView'     => function ($d, $key, $index, $widget){
                        $layout = '<tr data-key="%d"><th>%s</th><td style=\"word-break:break-all;\">%s</td></tr>';
                        return sprintf($layout, $key, $d->title, wordwrap(nl2br($d->body), 46, '<br />', true));
                    }
            ])->renderItems ();

?>
</table>
