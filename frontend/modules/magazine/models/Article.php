<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/magazine/models/Article.php $
 * $Id: Article.php 1285 2015-08-13 06:25:14Z mori $
 */

namespace app\modules\magazine\models;

use app\modules\magazine\Module;

class Article extends \yii\base\Model
{
    public $fullpath = null;
    public $suffix   = '.html';

    private $_init       = false;

    private $_basename   = null;
    private $_dirname    = null;
    private $_genre      = null;
    private $_title      = null;
    private $_subtitle   = null;
    private $_vol        = null;
    private $_excerpt    = null;
    private $_date       = null;
    private $_images     = null;
    private $_html       = null;
    private $_dom        = null;
    private $_recents    = null;
    private $_prev       = null;
    private $_next       = null;

    public function init()
    {
        parent::init();

        mb_language('Japanese');
        mb_internal_encoding('UTF-8');

        if(isset($this->fullpath))
        {
            // do something in order to construct this model
            $this->_init = true;
        }
    }

    public function getTitle()
    {
        if(isset($this->_title))
            return $this->_title;

        $h1 = $this->dom->find('h1', 0);
        if($h1)
            $this->_title = $h1->plaintext;

        return $this->_title;
    }
    public function getSubTitle()
    {
        if(isset($this->_subtitle))
            return $this->_subtitle;

        $h2 = $this->dom->find('h2', 0);
        if($h2)
            $this->_subtitle = $h2->plaintext;
        else
            $this->_subtitle = '';

        return $this->_subtitle;
    }

    public function getVol()
    {
        if(isset($this->_vol))
            return $this->_vol;

        $vol = 0;
        if($span = $this->dom->find('span[id=vol]', 0))
            $vol = $span->plaintext;
        elseif(preg_match('/\d+$/', $this->basename, $matches))
            $vol = array_shift($matches);

        $this->_vol = (int) $vol;

        return $this->_vol;
    }

    public function getExcerpt()
    {
        $maxlen = 100;
        if(isset($this->_excerpt))
            return $this->_excerpt;

        $this->_excerpt = ''; // avoid loop

        $p = $this->dom->find('p', 0);
        if($p)
        {
            $text = trim(mb_convert_kana($p->plaintext, "s")); // remove spaces

            if($maxlen < mb_strlen($text))
                $this->_excerpt = mb_substr($text, 0, $maxlen) . '...';
            else
                $this->_excerpt = $text;
        }

        return $this->_excerpt;
    }

    public function getHtml()
    {
        if(isset($this->_html))
            return $this->_html;

        if(! is_readable($this->fullpath) || (0 == filesize($this->fullpath)))
        {
            return '<body></body>';
        }
        $fp      = fopen($this->fullpath, 'r');
        $html    = fread($fp, filesize($this->fullpath));
        fclose($fp);

        $this->_html = $html;

        return $this->_html;
    }

    public function getDom()
    {
        if(isset($this->_dom))
            return $this->_dom;

        $this->_dom = \Yii::$app->dom->getDom($this->html);

        return $this->_dom;
    }

    public function getGenre()
    {
        if(isset($this->_genre))
            return $this->_genre;

        if(preg_match('/^[a-z]+/', $this->basename, $matches))
           $this->_genre = $matches[0];

        return $this->_genre;
    }
    public function setGenre($str)
    {
        $this->_genre = $str;
    }

    /**
     * @return string
     */
    public function getGenreLabel()
    {
        if(! isset(\Yii::$app->controller->module->title[$this->genre]))
            return $this->genre;
        else
            return \Yii::$app->controller->module->title[$this->genre];
    }

    public function getUrl()
    {
        return \yii\helpers\Url::toRoute(
            sprintf('/%s/%s/%s',
                    \Yii::$app->controller->module->id,
                    $this->dirname,
                    basename($this->fullpath)
            ));
    }

    public function getDirname()
    {
        if(! isset($this->_dirname))
            $this->_dirname = basename(dirname($this->fullpath));

        return $this->_dirname;
    }

    public function getBasename()
    {
        if(! isset($this->_basename))
            $this->_basename = basename($this->fullpath, $this->suffix);

        return $this->_basename;
    }

    public function getImages()
    {
        if(isset($this->_images))
            return $this->_images;

        $this->_images = [];

        $option = [
            'dir'    => $this->dirname,
            'prefix' => $this->basename,
        ];
        $images = \Yii::$app->controller->module->globImagePath($option);
        foreach($images as $i)
        {
            if(\Yii::$app->controller->module->isTopImage($i))
                continue;

            $this->_images[] = basename($i);
        }

        return $this->_images;
    }
    public function setImages($arr)
    {
        $this->_images = $arr;
    }

    public function getImageTop()
    {
        if(0 < count($this->images))
            return $this->images[0];

        return null;
    }

    /**
     * return the last modified time of the file
     */
    public function getDate()
    {
        if(isset($this->_date))
            return $this->_date;

        $s = stat($this->fullpath);
        $this->_date = $s['mtime'];

        return $this->_date;
    }

    public function getIsNew()
    {
        return ($this->dirname == \Yii::$app->controller->module->latestDir);
    }

    public function getPrev()
    {
        if(isset($this->_prev))
            return $this->_prev;

        $this->_prev = false; // set default value, to avoid looping

        $option = [
            'genre' => $this->genre,
            'sort'  => 'asc',
        ];
        $paths = \Yii::$app->controller->module->globArticlePath($option);

        for($i = 0; $i < count($paths); $i++)
        {
            $path = $paths[$i];
            if((0 < $i) && ($path == $this->fullpath))
            {
                $prev     = $paths[$i - 1];
                $dir      = basename(dirname($prev));
                $basename = basename($prev);

                $this->_prev = \Yii::$app->controller->module->getArticle($dir, $basename);
                break;
            }
        }

        return $this->_prev;
    }

    public function getNext()
    {
        if(isset($this->_next))
            return $this->_next;

        $this->_next = false; // set default value, to avoid looping

        $option = [
            'genre' => $this->genre,
            'sort'  => 'asc',
        ];
        $paths = \Yii::$app->controller->module->globArticlePath($option);

        for($i = 0; $i < count($paths); $i++)
        {
            $path = $paths[$i];
            if(($path == $this->fullpath) && isset($paths[$i+1]))
            {
                $next     = $paths[$i + 1];
                $dir      = basename(dirname($next));
                $basename = basename($next);

                $this->_next = \Yii::$app->controller->module->getArticle($dir, $basename);
                break;
            }
        }

        return $this->_next;
    }

    public function getRecents()
    {
        if(isset($this->_recents))
            return $this->_recents;

        $option = [
            'genre' => $this->genre,
            'limit' => 4,
        ];
        $this->_recents = \Yii::$app->controller->module->findArticle($option);

        return $this->_recents;
    }
}
