<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/magazine/controllers/BaseController.php $
 * $Id: BaseController.php 2011 2016-01-27 02:03:13Z mori $
 */

namespace app\modules\magazine\controllers;

use Yii;
use yii\data\ArrayDataProvider;

abstract class BaseController extends \yii\web\Controller
{ 
    public $defaultAction = 'home';

    public function init()
    {
        parent::init();
    }

    public function actionHome($demo=null)
    {
        // prepare 'Feature' articles

        $genres = $this->module->genres;
        $i = array_search('interview', $genres);
        if(false === $i)
            \Yii::warning("internal error");
        else
            unset($genres[$i]);
        $i = array_search('home', $genres);
        if(false === $i)
            \Yii::warning("internal error");
        else
            unset($genres[$i]);

        shuffle($genres);

        $opt1 = [
            'genre' => 'interview', // the first article must be the latest 'interview'
            'limit' => 1,
            'dir'   => isset($demo) ? '201503' : $this->module->latestDir,
        ];
        $opt2 = [
            'genre' => array_shift($genres),
            'limit' => 1,
            'random'=> true,
            'dir'   => isset($demo) ? '201503' : $this->module->latestDir,
        ];
        $opt3 = [
            'genre' => array_shift($genres),
            'limit' => 1,
            'random'=> true,
            'dir'   => isset($demo) ? '201503' : $this->module->latestDir,
        ];
        $feature = array_merge(
            $this->module->findArticle($opt1),
            $this->module->findArticle($opt2),
            $this->module->findArticle($opt3)
        );

        // prepare 'General' articles
        $dir = isset($demo) ? '201503' : $this->module->latestDir;
        $regular = array_merge(
            $this->module->findArticle(['genre'=>'farm',      'limit'=> 2 ,'dir'=>$dir]),
            $this->module->findArticle(['genre'=>'botanical', 'limit'=> 1 ,'dir'=>$dir]),
            $this->module->findArticle(['genre'=>'essay',     'limit'=> 1 ,'dir'=>$dir]),
            $this->module->findArticle(['genre'=>'recipe',    'limit'=> 2 ,'dir'=>$dir]),
            $this->module->findArticle(['genre'=>'astrology', 'limit'=> 1 ,'dir'=>$dir]),
            $this->module->findArticle(['genre'=>'product',   'limit'=> 1, 'random'=>true, 'dir'=>$dir])
        );

        // prepare top images
        $img = [];
        $img = $this->module->globTopImage($dir);

        $this->view->params = ['genre'=>'home'];

        return $this->render('home', ['topImage'=>$img, 'feature'=>$feature, 'regular'=>$regular]);
    }

    public function actionInterview()
    {
        $label    = $this->module->title['interview'];
        $subtitle = $this->module->subtitle['interview'];

        $option = [
            'genre' => 'interview',
        ];
        $articles = $this->module->findArticle($option);
        $this->view->params = $option;
        $this->view->title  = $this->module->title['interview'];

        return $this->render('list', ['articles'=>$articles, 'label'=>$label, 'subtitle'=>$subtitle]);
    }

    public function actionFarm()
    {
        $label    = $this->module->title['farm'];
        $subtitle = $this->module->subtitle['farm'];

        $option = [
            'genre' => 'farm',
        ];
        $articles = $this->module->findArticle($option);
        $this->view->params = $option;
        $this->view->title  = $this->module->title['farm'];

        return $this->render('list', ['articles'=>$articles, 'label'=>$label, 'subtitle'=>$subtitle]);
    }

    public function actionBotanical()
    {
        $label    = $this->module->title['botanical'];
        $subtitle = $this->module->subtitle['botanical'];

        $option = [
            'genre' => 'botanical',
        ];
        $articles = $this->module->findArticle($option);
        $this->view->params = $option;
        $this->view->title  = $this->module->title['botanical'];

        return $this->render('list', ['articles'=>$articles, 'label'=>$label, 'subtitle'=>$subtitle]);
    }

    public function actionEssay()
    {
        $label    = $this->module->title['essay'];
        $subtitle = $this->module->subtitle['essay'];

        $option = [
            'genre' => 'essay',
        ];
        $articles = $this->module->findArticle($option);
        $this->view->params = $option;
        $this->view->title  = $this->module->title['essay'];

        return $this->render('list', ['articles'=>$articles, 'label'=>$label, 'subtitle'=>$subtitle]);
    }

    public function actionRecipe()
    {
        $label    = $this->module->title['recipe'];
        $subtitle = $this->module->subtitle['recipe'];

        $option = [
            'genre' => 'recipe',
        ];
        $articles = $this->module->findArticle($option);
        $this->view->params = $option;
        $this->view->title  = $this->module->title['recipe'];

        return $this->render('list', ['articles'=>$articles, 'label'=>$label, 'subtitle'=>$subtitle]);
    }

    public function actionAstrology()
    {
        $label    = $this->module->title['astrology'];
        $subtitle = $this->module->subtitle['astrology'];

        $option = [
            'genre' => 'astrology',
            'limit' => 1, // get the latest article
        ];
        $this->view->params = $option;
        $article = array_shift($this->module->findArticle($option));
        if(! $article)
            throw new \yii\web\HttpException(404);

        $img = [];
        foreach($article->images as $i)
        {
            $img[] = sprintf('%s/%s', $article->dirname, $i);
        }
        $article->images = $img;

        return $this->render('article', ['model'=>$article]);
    }

    public function actionProduct()
    {
        $label    = $this->module->title['product'];
        $subtitle = $this->module->subtitle['product'];

        $option = [
            'genre' => 'product',
        ];
        $products = $this->module->findProduct($option);
        $this->view->params = $option;
        $this->view->title  = $this->module->title['product'];

        return $this->render('product', ['products'=>$products, 'label'=>$label, 'subtitle'=>$subtitle]);
    }

    public function actionArticle($dir, $page=null)
    {
        if(null === $page)
        {
            $articles = $this->module->findArticle(['id'=>$dir]);

            return $this->render('list', ['articles'=>$articles]);
        }

        $fullpath = $this->module->abspath($dir, $page);
        if(! $fullpath)
            throw new \yii\web\HttpException(404);
            
        $article = $this->module->getArticle($dir, $page);

        // forbid viewing individual article for particular genres
        if(in_array($article->genre, ['astrology','product']))
            return $this->redirect([$article->genre], 301 /* Moved Permanently */);

        $option = [
            'genre' => $article->genre
        ];
        $this->view->params = $option;
        $this->view->title  = sprintf('Vol. %d %s | %s',
                                      $article->vol,
                                      $article->title,
                                      $article->genreLabel);

        if(Yii::$app->user->isGuest && (! in_array($dir, ['201503','201507'])))
            $truncate = true;
        else
            $truncate = false;

        return $this->render('article', ['model'=>$article, 'truncate'=>$truncate]);
    }

    public function actionView($id, $page=null)
    {
        if(null === $page)
            return $this->actionHome($id);

        // draw binary data as it is
        if(! preg_match('/.html$/', $page))
            return $this->module->sendfile($id, $page);

        // 
        $fullpath = $this->module->abspath($id, $page);
        if(! $fullpath)
            throw new \yii\web\HttpException(404);

        return $this->actionArticle($id, $page);
    }
}
