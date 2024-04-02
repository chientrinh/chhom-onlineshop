<?php
namespace common\widgets\doc\remedy;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/remedy/RemedyLabel.php $
 * $Id: RemedyLabel.php 2621 2016-06-24 09:57:25Z mori $
 */

use Yii;
use \common\models\RemedyStock;
use \common\models\ComplexRemedy;
use \common\models\MachineRemedyForm;

class RemedyLabel extends \yii\base\Widget
{
    /* @var document title, such as {purchase_id} */
    public  $title;

    /* @var ComplexRemedyForm model, or null
       if $model is specified, $vial and $dro will be ignored */
    public  $model;

    /* @var RemedyStock */
    public  $vial;

    /* @var array of RemedyStock */
    public  $drops;

    public  $csscode = "
span {
line-height: 7.0pt;
font-size:   7.0pt;
}
";

    public function init()
    {
        parent::init();

        if(! $this->title)
             $this->title = date('Y-m-d H:i');

        $this->initModel();

    }

    private function initModel()
    {
        if($this->model && $this->model instanceof ComplexRemedy)
        {
            if(! $this->model->drops)
                 $this->model->drops = [ $this->model->vial ];
        }
        elseif($this->model && $this->model instanceof RemedyStock)
        {
            $stock = $this->model;

            $this->model = new \common\models\ComplexRemedy([
                'vial'  => $stock,
                'drops' => [$stock],
            ]);
        }
        elseif($this->model && $this->model instanceof MachineRemedyForm)
        {
            ; // do nothing so far
        }
        elseif($this->model)
        {
            throw new \yii\base\NotSupportedException(sprintf('model %s != ComplexRemedy', $this->model->className()));
        }
        else
        {
            $this->model = new \common\models\ComplexRemedy();

            if($this->vial)
                $this->model->vial = $this->vial;

            if($this->drops)
                $this->model->drops = $this->drops;
        }

        return;
    }

    public function run()
    {
        $html = $this->generateLabel();

        return $html;
    }

    private function generateLabel()
    {
        if($this->model instanceof MachineRemedyForm)
            return $this->render('label-machine', ['model' => $this->model, 'title' => $this->title]);

        return $this->render('label', [
            'model' => $this->model,
            'title' => $this->title,
            'vial_name' => $this->getVialName(),
        ]);
    }

    private function getVialName()
    {
        $model = $this->model;

        $name = 'オリジナル';

        switch($model->vial->vial_id)
        {
        case \common\models\RemedyVial::SMALL_BOTTLE:
        case \common\models\RemedyVial::MIDDLE_BOTTLE:
            $name .= '小';
            break;
        case \common\models\RemedyVial::LARGE_BOTTLE:
            $name .= '大';
            break;
        case \common\models\RemedyVial::GLASS_5ML:
            $name .= 'アルポ (5ml)';
        default:
        }

        if($model->vial->remedy_id)
        {
            if($model->vial->remedy_id == $model->drops[0]->remedy_id)
                $name = preg_replace('/オリジナル/', '単品', $name);
            else
                $name .= $model->vial->remedy->name;
        }
        elseif(1 == count($model->drops))
            $name = preg_replace('/オリジナル/', '単品', $name);

        return $name;
    }

}
