<?php

namespace frontend\widgets;

/**
 * @link $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/Nav.php $
 * @version $Id: Nav.php 1090 2015-06-17 06:31:27Z mori $
 */
class Nav extends \yii\bootstrap\Nav
{
    /**
     * @inheritdoc
     */
    public function renderItems()
    {
        $this->options = []; // no id, no class for <ul>

        return parent::renderItems();
    }

}

