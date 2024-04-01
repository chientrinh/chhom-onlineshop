<?php

namespace frontend\widgets;


use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\BootstrapPluginAsset;

/**
 * @link $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/Nav.php $
 * @version $Id: Nav.php 1090 2015-06-17 06:31:27Z mori $
 */
class HeaderNavBar extends \yii\bootstrap\Widget
{
    /**
     * @var array the HTML attributes for the widget container tag. The following special options are recognized:
     *
     * - tag: string, defaults to "nav", the name of the container tag.
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];
    /**
     * @var array the HTML attributes for the container tag. The following special options are recognized:
     *
     * - tag: string, defaults to "div", the name of the container tag.
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $containerOptions = [];
    /**
     * @var string|boolean the text of the brand of false if it's not used. Note that this is not HTML-encoded.
     * @see http://getbootstrap.com/components/#navbar
     */
    public $brandLabel = false;
    /**
     * @param array|string|boolean $url the URL for the brand's hyperlink tag. This parameter will be processed by [[Url::to()]]
     * and will be used for the "href" attribute of the brand link. Default value is false that means
     * [[\yii\web\Application::homeUrl]] will be used.
     */
    public $brandUrl = false;
    /**
     * @var array the HTML attributes of the brand link.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $brandOptions = [];
    /**
     * @var string text to show for screen readers for the button to toggle the navbar.
     */
    public $screenReaderToggleText = 'Toggle navigation';
    /**
     * @var boolean whether the navbar content should be included in an inner div container which by default
     * adds left and right padding. Set this to false for a 100% width navbar.
     */
    public $renderInnerContainer = true;
    /**
     * @var array the HTML attributes of the inner container.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $innerContainerOptions = [];

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        $this->clientOptions = false;
        Html::addCssClass($this->options, 'navbar');
        if ($this->options['class'] === 'navbar') {
            Html::addCssClass($this->options, 'navbar-default');
        }
        Html::addCssClass($this->brandOptions, 'navbar-brand');
        if (empty($this->options['role'])) {
            $this->options['role'] = 'navigation';
        }
        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'nav');
        echo Html::beginTag($tag, $options);
        if ($this->renderInnerContainer) {
            if (!isset($this->innerContainerOptions['class'])) {
                Html::addCssClass($this->innerContainerOptions, 'container');
            }
            echo Html::beginTag('div', $this->innerContainerOptions);
        }
        echo Html::beginTag('div', ['class' => 'navbar-header']);
        if (!isset($this->containerOptions['id'])) {
            $this->containerOptions['id'] = "{$this->options['id']}-collapse";
        }

        if(Yii::$app->layout != 'facility-main') {
            // スマートフォンで表示するサイドメニュー
            echo $this->renderToggleButton();

            // スマートフォンで表示するカートボタン
            echo $this->renderCartButton();
            // スマートフォンで表示するログイン／ログアウトボタン
            echo $this->renderUserButton();
        }

        if ($this->brandLabel !== false) {
            Html::addCssClass($this->brandOptions, 'navbar-brand');
            echo Html::a($this->brandLabel, $this->brandUrl === false ? Yii::$app->homeUrl : $this->brandUrl, $this->brandOptions);
        }
        echo Html::endTag('div');
        Html::addCssClass($this->containerOptions, 'collapse');
        Html::addCssClass($this->containerOptions, 'navbar-collapse');
        $options = $this->containerOptions;
        $tag = ArrayHelper::remove($options, 'tag', 'div');
        echo Html::beginTag($tag, $options);
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        $tag = ArrayHelper::remove($this->containerOptions, 'tag', 'div');
        echo Html::endTag($tag);
        if ($this->renderInnerContainer) {
            echo Html::endTag('div');
        }
        $tag = ArrayHelper::remove($this->options, 'tag', 'nav');
        echo Html::endTag($tag, $this->options);
        BootstrapPluginAsset::register($this->getView());
    }

    /**
     * Renders collapsible toggle button.
     * @return string the rendering toggle button.
     */
    protected function renderToggleButton()
    {
        $bar = Html::tag('span', '', ['class' => 'icon-bar']);
        $screenReader = "<span class=\"sr-only\">{$this->screenReaderToggleText}</span>";

        return Html::button("{$screenReader}\n{$bar}\n{$bar}\n{$bar}", [
            'class' => 'navbar-toggle',
            'data-toggle' => 'collapse',
            'data-target' => "#{$this->containerOptions['id']}",
        ]);
    }

    /**
     * Renders collapsible toggle button.
     * @return string the rendering toggle button.
     */
    protected function renderCartButton()
    {
        $route     = '/cart';
        $itemCount = \frontend\modules\cart\Module::getItemCount();
        $glyph = 'glyphicon glyphicon-shopping-cart';
        $class = 'btn btn-md navbar-menu-toggle';
        if($itemCount > 0)
            $class .= ' btn-warning';
        else
            $class .= ' btn-primary';

        $label = sprintf('<span class="%s"></span>&nbsp;%d', $glyph, $itemCount);
        $title = sprintf('いまカートに %d 点あります', $itemCount);

        $screenReader = "<span class=\"sr-only\">{$this->screenReaderToggleText}</span>";

        return Html::a($screenReader. $label, [ $route ], [
                      'title' => $title,
                      'class' => $class,
                      'style' => 'font-weight:bold; color:white;',
                    ]);
    }

    /**
     * ログイン/ログアウトボタン設置
     * @return string the rendering toggle button.
     */
    protected function renderUserButton()
    {
        $glyph        = 'glyphicon glyphicon-shopping-cart';
        $style        = 'font-weight:bold; color:white;';
        $class        = 'btn btn-md navbar-menu-toggle';
        $itemCount    = \frontend\modules\cart\Module::getItemCount();
        $screenReader = "<span class=\"sr-only\">{$this->screenReaderToggleText}</span>";

        if(Yii::$app->user->isGuest) {
            $class     .= ' btn-success';
            $word       = 'ログイン';
            $route      = '/site/login';
            $dataMethod = 'get';
        } else {
            $class     .= ' btn-danger';
            $word       = 'ログアウト';
            $route      = '/site/logout';
            $dataMethod = 'post';
            $style     .= ' display:none;';
        }

        $label = sprintf('<span></span>&nbsp;%s', $word);
        $title = sprintf('いまカートに %d 点あります', $itemCount);

        return Html::a($screenReader. $label, [ $route ], [
                      'title' => $title,
                      'class' => $class,
                      'style' => $style,
                      'data-method' => $dataMethod
                      // 'onClick' => '!confirm("ログアウトします。よろしいですか？") return false;'
                    ]);
    }
}

