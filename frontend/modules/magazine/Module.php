<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/magazine/Module.php $
 * $Id: Module.php 1845 2015-12-04 07:43:28Z mori $
 */

namespace app\modules\magazine;

use Yii;
use app\modules\magazine\models\Article;
use app\modules\magazine\models\Product;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\magazine\controllers';

    public $genres = [
        'interview',
        'farm'     ,
        'botanical',
        'essay'    ,
        'recipe'   ,
        'astrology',
        'product'  ,
        'home'     ,
    ];

    public $title = [
        'interview'  => "Special Interview",
        'farm'       => "農場通信",
        'botanical'  => "植物図鑑",
        'essay'      => "エッセイ",
        'recipe'     => "豊受食堂レシピ",
        'astrology'  => "12星座占い",
        'product'    => "新商品情報",
        'home'       => "ホーム",
    ];

    public $subtitle = [
        'interview'  => "由井寅子の",
        'farm'       => "日本豊受自然農の今を現場からリポート",
        'botanical'  => "日本人の心と日本の花",
        'essay'      => "季節をいつくしむ暮らし方",
        'recipe'     => "季節のめぐみをいただく",
        'astrology'  => "星が導く健やかな未来",
        'product'    => "安心して肌に使え、口にできるものを",
    ];

    private $_latest = null;

    public function init()
    {
        parent::init();

        Yii::$app->layout = 'magazine';
    }

    public function getArticle($dir, $file)
    {
        $fullpath = $this->abspath($dir, $file);

        if(! $fullpath)
            return null;

        $article = new Article;
        $article->fullpath = $fullpath;

        return $article;
    }

    public function findArticle($option)
    {
        if(isset($option['genre']))
            if(! isset($option['prefix']))
            {
                $option['prefix'] = $option['genre'];
                unset($option['genre']);
            }

        $files = $this->globArticlePath($option);


        $articles = [];
        foreach($files as $file)
        {
            $article = new Article;
            $article->fullpath = $file;

            $articles[] = $article;
        }

        return $articles;
    }

    public function findProduct($option)
    {
        $articles = $this->findArticle($option);
        $products = [];

        foreach($articles as $article)
        {
            $p = new Product;
            $p->fullpath = $article->fullpath;

            $products[] = $p;
        }

        return $products;
    }

    public function globArticlePath($option)
    {
        $option['suffix'] = '.html';
        return $this->globPath($option);
    }

    public function globImagePath($option)
    {
        $option = array_merge(['sort'=>'asc'], $option);
        $option['suffix'] = '.{jpg,jpeg,png,gif}';

        return $this->globPath($option);
    }

    /**
     * get Top Images in latest issue
     * @return arry of string
     */
    public function globTopImage($dir=null)
    {
        $option = [
            'dir'    => $dir ? $dir : $this->latestDir,
            'sort'   => 'asc',
            'suffix' => '00.{jpg,jpeg,png,gif}',
        ];

        $images = [];
        foreach($this->genres as $genre)
        {
            $option['prefix'] = $genre;
            foreach($this->globPath($option) as $path)
            {
                $img = new \app\modules\magazine\models\Image;
                $img->fullpath = $path;
                $images[] = $img;
            }
        }

        return $images;
    }

    /**
     * check if filename like "genre_article1_00.jpg"
     * @return bool
     */
    public function isTopImage($path)
    {
        return preg_match('/_00\.(jpg|jpeg|png|gif)$/', $path);
    }

    public function getLatestDir()
    {
        if(isset($this->_latest))
            return $this->_latest;

        $paths = $this->globPath(['limit'=>1,'sort'=>'desc']);
        $path  = array_shift($paths);

        $this->_latest = basename(dirname($path));

        return $this->_latest;
    }

    private function globPath($option)
    {
        // set default value for options
        if(! array_key_exists('prefix', $option))
            $option['prefix'] = null;

        if(! array_key_exists('dir', $option))
            $option['dir'] = '*';

        if(! array_key_exists('limit', $option))
            $option['limit'] = 1000;

        if(! array_key_exists('offset', $option))
            $option['offset'] = 0;

        if(! array_key_exists('random', $option))
            $option['random'] = false;

        if(! array_key_exists('sort', $option))
            $option['sort'] = 'desc';

        if(! array_key_exists('suffix', $option))
            $option['suffix'] = '';

        if(! array_key_exists('affix', $option))
            $option['affix'] = '*';

        $opt = (object) $option;
        $argv = sprintf('%s/%s/%s%s%s',
                        \Yii::getAlias('@common/content/magazine'),
                        $opt->dir,
                        $opt->prefix,
                        $opt->affix,
                        $opt->suffix
        );

        $files = [];
        $ret   = glob($argv, GLOB_BRACE);
        if(false === $ret)
        {
            \Yii::error(sprintf("glob() failed: argv=%s", $argv), self::className());
            return [];
        }
        foreach($ret as $path)
        {
            // remove directory and un-accessible files
            if(is_file($path) && is_readable($path))
                $files[] = $path;
        }

        if($opt->random)
            shuffle($files);
        elseif('desc' == $opt->sort)
            arsort($files); // reverse order

        if((0 < $opt->offset) && (0 < count($files)))
        {
            $files = array_slice($files, $opt->offset); 
        }
        if($opt->limit < count($files))
        {
            $files = array_slice($files, 0, $opt->limit);
        }

        return array_values($files);
    }

    public function abspath($id, $file)
    {
        $fullpath = sprintf('%s/%s/%s',
                        \Yii::getAlias('@common/content/magazine'),
                        $id,
                        $file);

        if(! is_readable($fullpath))
            return null;

        return $fullpath;
    }

    public function fread($fullpath)
    {
        $fp      = fopen($fullpath, 'r');
        $content = fread($fp, filesize($fullpath));
        fclose($fp);

        return $content;
    }

    public function sendfile($id, $file)
    {
        $fullpath = $this->abspath($id, $file);
                                       
        if(! $fullpath)
            throw new \yii\web\HttpException(404);

        // send the content as binary data
        $response = \Yii::$app->getResponse();
        $response->sendFile($fullpath, $file, ['inline'=>true]);
        return $response->send();
    }

    public static function truncateText($html)
    {
        $dom = Yii::$app->dom->getDom($html);
        if(! $dom)
            return $html;

        foreach($dom->find('text') as $k => $text)
            $dom->find('text',$k)->innertext = '';

        return $dom->html;
    }
}
