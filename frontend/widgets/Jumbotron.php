<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/Jumbotron.php $
 * $Id: Jumbotron.php 4250 2020-04-24 17:14:57Z mori $
 */

namespace frontend\widgets;
use Yii;
/**
 * Jumbotron widget renders a Jumbobron content, which is desined for top page of the site
 *
 * @author Takashi Ooishi <ooishi@tak-zone.net>
 * @author Reiko Mori <mori@homoeopathy.co.jp>
 */
class Jumbotron extends \yii\bootstrap\Widget
{
    /**
     * @var array the options for rendering the close button tag.
     */
    public $closeButton = [];

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if('echom-frontend' == Yii::$app->id)
            return; //$this->render('echom/jumbotron');

        return $this->render('jumbotron');
    }
}
