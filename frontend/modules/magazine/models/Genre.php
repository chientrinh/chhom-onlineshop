<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/magazine/models/Genre.php $
 * $Id: Genre.php 848 2015-04-04 11:36:35Z mori $
 */

namespace app\modules\magazine\models;

use app\modules\magazine\Module;

//require \Yii::getAlias('common/components/simple_html_dom') . '.php';

class Genre extends \yii\base\Model
{
    private static $_genres     = [
        'home'       => "Home",
        'interview'  => "Special Interview",
        'farm'       => "農場通信",
        'botanical'  => "",
        'essay'      => "",
        'recipe'     => "",
        'astrology'  => "",
        'product'    => "",
        ];

    public function getLabel($id)
    {
        if(isset($this->_genre[$id]))
            return $this->_genre[$id];

        return '';
    }
}