<?php

namespace common\modules\invoice;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/invoice/Frontend.php $
 * $Id: Frontend.php 1674 2015-10-16 20:19:06Z mori $
 */

class Frontend extends \yii\base\Module
{
    public $controllerNamespace = 'common\modules\invoice\controllers';
    public $defaultRoute        = 'invoice/agency/index';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
