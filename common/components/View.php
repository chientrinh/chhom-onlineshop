<?php
namespace common\components;

/**
 * @link $URL: https://tarax.toyouke.com/svn/MALL/common/components/View.php $
 * @version $Id: View.php 1972 2016-01-13 05:09:01Z mori $
 * @copyright Copyright (c) 2015 Homoeopathic Education Co Ltd
 * @author Reiko Mori <mori@homoeopathy.co.jp>
 */

use Yii;
use \yii\helpers\ArrayHelper;

class View extends \yii\web\View
{
    public function beginPage()
    {
        $this->initTitle();

        parent::beginPage();
    }

    /* preset html > head > title from breadcrumbs */
    protected function initTitle()
    {
        if($this->title || ! isset($this->params['breadcrumbs']))
            return;

        $labels = ArrayHelper::getColumn($this->params['breadcrumbs'], 'label');
        foreach($labels as $k => $v)
            if(! isset($v) || (0 == strlen($v))){ unset($labels[$k]); }

        krsort($labels);
        array_push($labels, Yii::$app->name);

        $this->title = implode(' | ', $labels);
    }
}
