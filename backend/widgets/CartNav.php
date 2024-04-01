<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/widgets/CartNav.php $
 * $Id: CartNav.php 2812 2016-08-06 06:07:50Z mori $
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\widgets;

use Yii;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\helpers\ArrayHelper;

/**
 * CartNav widget renders a Nav from cookie sets.
 *
 * initialization
 *
 * ```php
 * CartNav::register([
 *    'route'     => '/cart/default/create',
 *    'itemCount' => 7
 * ]);
 * ```
 *
 * then render the widget
 *
 * ```php
 * CartNav::widget()
 * ```
 *
 * call release() if you want remove associated cookies
 *
 * ```php
 * CartNav::release()
 * ```
 *
 * call hasContent() to check if the cart contains something
 *
 * ```php
 * CartNav::hasContent()
 * ```
 * @author Reiko Mori <mori@homoeopathy.co.jp>
 */
class CartNav extends \yii\bootstrap\Widget
{
    public function init()
    {
        parent::init();
    }

    public static function widget()
    {
        if(false == self::hasContent())
            return; // no need to render this widget

        $route      = Yii::$app->request->cookies->getValue('CartNav-route', '/casher/default/create');
        $itemCount  = Yii::$app->request->cookies->getValue('CartNav-itemCount', 0);
        $customer   = Yii::$app->request->cookies->getValue('CartNav-customer',  0);

        if(preg_match('/default/',$route))
        {
            $class = 'btn btn-small btn-warning';
            $glyph = 'glyphicon glyphicon-shopping-cart';
        }
        else
        {
            $class = 'btn btn-small btn-success';
            $glyph = 'glyphicon glyphicon-phone-alt';
        }

        $label = sprintf('<span class="%s"></span>&nbsp;%d', $glyph, $itemCount);
        $title = sprintf('いまカートに %d 点あります', $itemCount);

        echo \yii\bootstrap\Nav::widget([
            'options'         => ['class' => 'navbar-nav navbar-right'],
            'items'           => [
                Html::tag('li',
                          Html::a($label, [ $route ], [
                              'title' => $title,
                              'class' => $class,
                              'style' => 'font-weight:bold; color:white;',
                          ])
                ),
            ],
            'encodeLabels' => false,
        ]);
    }

    public static function register($config)
    {
        $cookies = Yii::$app->response->cookies;

        foreach(['route','itemCount','customer'] as $key)
        {
            if(isset($config[$key]))
                $cookies->add(new \yii\web\Cookie([
                    'path'  => Url::base(),
                    'name'  => "CartNav-$key",
                    'value' => $config[$key],
                    'expire'=> time() + 3600, // 1 hour
            ]));
            else
                $cookies->remove("CartNav-$key");
        }
    }

    /**
     * Cookie を消去する
     * @return void
     */
    public static function release()
    {
        $cookies = Yii::$app->response->cookies;

        foreach(['route','itemCount','customer'] as $key)
            $cookies->add(new \yii\web\Cookie([
                'path'  => Url::base(),
                'name'  => "CartNav-$key",
                'value' => 0,             // empty value
                'expire'=> time() - 3600, // expire now!
            ]));
    }

    /**
     * 記憶してるURLを返す
     * @return string
     */
    public static function getRoute()
    {
        $route = Yii::$app->request->cookies->getValue('CartNav-route', '/casher/default/create');

        return Url::toRoute($route);
    }

    /**
     * カートの中に品物または顧客が存在するかどうか返す
     * @return bool
     */
    public static function hasContent()
    {
        $itemCount = Yii::$app->request->cookies->getValue('CartNav-itemCount', 0);
        $customer  = Yii::$app->request->cookies->getValue('CartNav-customer',  0);

        return ($itemCount || $customer);
    }
}
