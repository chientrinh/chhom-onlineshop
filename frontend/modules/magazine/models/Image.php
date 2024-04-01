<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/magazine/models/Image.php $
 * $Id: Image.php 1284 2015-08-13 06:23:36Z mori $
 */

namespace app\modules\magazine\models;

class Image extends Article
{
    private $_article = null;

    public function init()
    {
        parent::init();

        $this->suffix = '.jpg';
    }

    public function getArticle()
    {
        if(isset($this->_article))
            return $this->_article;

        $file = preg_replace('/(_00|)$/', '.html', $this->basename, 1);

        $this->_article = \Yii::$app->controller->module->getArticle($this->dirname, $file);
        if(! $this->_article)
            $this->_article = false;

        return $this->_article;
    }

}
