<?php

namespace common\widgets;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/AtoZ.php $
 * $Id: AtoZ.php 1199 2015-07-25 05:31:28Z mori $
 */

use \yii\helpers\Html;

class AtoZ extends \yii\bootstrap\Nav
{
    public $action = 'search';
    public $target = 'remedy';
    public $param  = 'startwith';
    public $items;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $alphabet = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZ");

        foreach($alphabet as $char)
        {
            $this->items[] = Html::tag('li',Html::a(
                $char, [
                    $this->action,
                    'target'      => $this->target,
                    $this->param  => $char
                ]
            ));
        }

        $this->options['class'] = 'initial';
    }
}
