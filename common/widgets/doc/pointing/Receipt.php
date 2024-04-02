<?php
namespace common\widgets\doc\pointing;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/pointing/Receipt.php $
 * $Id: Receipt.php 1223 2015-08-02 01:35:03Z mori $
 */

use Yii;

class Receipt extends \yii\base\Widget
{
    /* @var pointing_id to find Pointing Model,
       if it is invalid pointing_id, a bogus receipt will be rendered */
    public  $pointing_id;

    /* @var Pointing model itself.
       if $pointing_id is specified, it will be overridden */
    public  $model;

    /* @var document title */
    public  $title = '領収書';

    private $_items;

    public function init()
    {
        parent::init();

        if($this->pointing_id)
            $this->model = \common\models\Pointing::findOne($this->pointing_id);

        elseif(! $this->model)
            $this->model = new \common\models\Pointing();
    }

    public function run()
    {
        if(! $this->model instanceof \common\models\Pointing)
            throw new \yii\base\NotSupportedException('model %s is not Pointing', $this->model->className());
        
        if($this->model->isExpired())
            return \yii\helpers\Html::tag('p', "この伝票は無効です");
        
        return $this->generateHtml();
    }

    private function generateHtml()
    {
        return $this->render('receipt', [
            'model' => $this->model,
            'title' => $this->title,
        ]);
    }

}
