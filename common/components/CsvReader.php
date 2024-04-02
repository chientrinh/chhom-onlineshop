<?php

namespace common\components;

use \yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/CsvReader.php $
 * $Id: CsvReader.php 2694 2016-07-10 06:22:45Z mori $
 */

class CsvReader extends \yii\base\Model
{
    private $rows = [];

    public function getRows()
    {
        return $this->rows;
    }

    public function feed($content)
    {
        $buff = explode("\n", $content); // split by new line
        do
        {
            $header = explode(",", trim(array_shift($buff)));
        }
        while(preg_match('/^#/', $header[0])); // ignore line start with '#'

        $this->rows = [];

        foreach($buff as $line)
        {
            $line = trim($line); // remove CR if any
            if(! $line)
            {
                continue;
            }

            $row   = [];
            $chunk = explode(',', $line); // remove CR if any

            if(count($chunk) < count($header))
            {
                $this->addError('rows', "列数が不足しています: $line");
                continue;
            }

            foreach($header as $idx => $label)
                $row[$label] = ArrayHelper::getValue($chunk, $idx);

            $this->rows[] = $row;
        }

        return (0 < count($this->rows));
    }

}
