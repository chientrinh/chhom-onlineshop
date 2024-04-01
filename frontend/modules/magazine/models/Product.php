<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/magazine/models/Product.php $
 * $Id: Product.php 1283 2015-08-13 04:35:18Z mori $
 */

namespace app\modules\magazine\models;

use app\modules\magazine\Module;

//require \Yii::getAlias('common/components/simple_html_dom') . '.php';

class Product extends \app\modules\magazine\models\Article
{
    private $_id      = null;
    private $_summary = null;
    private $_url     = null;

    public function getId()
    { 
        if(isset($this->_id))
            return $this->_id;

        $this->_id = trim($this->dom->find('span.product_id',0)->plaintext);

        return $this->_id;
    }

    public function getSummary()
    {
        if(isset($this->_summary))
            return $this->_summary;

        $p = $this->dom->find('p', 0);
        if($p)
            $text = trim(mb_convert_kana($p->plaintext, "s")); // remove spaces
        else
            $text = '';

        $this->_summary = $text;

        return $this->_summary;
    }

    public function getUrl()
    { 
        if(isset($this->_url))
            return $this->_url;

        $baseurl = trim($this->dom->find('span.baseurl',0)->plaintext);
        if($baseurl)
            $this->_url = sprintf('%s%s',$baseurl,$this->id);

        return $this->_url;
    }

}
