<?php
namespace common\widgets;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/CsvView.php $
 * $Id: CsvView.php 2734 2016-07-17 04:07:12Z mori $
 */

use Yii;
use Closure;

class CsvView extends \yii\base\Widget
{
    /* @var string */
    public  $charset = 'SJIS-WIN';

    /* @var string End Of Line */
    public  $eol = "\r\n";

    /* @var ActiveQuery query */
    public  $query;

    public  $header = [];

    /* @var array of string */
    public  $attributes;

    public function init()
    {
        parent::init();

        if(! $this->header)
            foreach($this->attributes as $key =>$attr)
                $this->header[] = is_string($attr) ? $attr :$key;
    }

    public function run()
    {
        $this->renderHeader();
        $this->renderItems();
    }

    private function renderHeader()
    {
        $this->output(implode(',', $this->header));
    }

    private function renderItems()
    {
        foreach($this->query->batch() as $rows)
        {
            foreach($rows as $row)
                $this->renderItem($row);

            if( ob_get_level() > 0 ) ob_flush();
        }
    }

    private function renderItem($model)
    {
        $row = [];

        foreach($this->attributes as $attr)
            if($attr instanceof Closure)
                $row[] = call_user_func($attr, $model);
            else
                $row[] = \yii\helpers\ArrayHelper::getValue($model, $attr, "エラー: $attr は存在しません");

        $this->output(implode(',', $row));
    }

    private function output($line)
    {
        if(Yii::$app->charset !== $this->charset)
            $line = mb_convert_encoding($line, $this->charset, Yii::$app->charset);

        echo $line, $this->eol;
    }
}
