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

class RemedyLabelCsv extends \yii\base\Widget
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

    public $eol = "\r\n";

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
        $csv = $this->renderCsv();

        return $csv;
    }

    private function renderCsv()
    {        

        if($this->model instanceof MachineRemedyForm) {
            $p1 = \common\models\RemedyPotency::findOne($this->model->potency1);
            $p2 = \common\models\RemedyPotency::findOne($this->model->potency2);
            
            $potency1 = "";
            $potency2 = "";
            if($this->model->abbr1) 
                 $items[] = $this->title;
                 $items[] = "特";
                 $potency1 .= "+";
                 $potency1 .= $this->model->abbr1." ". (isset($p1) ? $p1->name : "");
                 $items[] = $potency1;
                 
            if($this->model->abbr2) 
                 $potency2 .= "+";
                 $potency2 .= $this->model->abbr2." ". (isset($p2) ? $p2->name : "");
                 if(strlen($potency2) > 1) 
                     $items[] = $potency2;
              
        } else {
            $items = [
                $this->title,
                $this->getVialName(),
            ];
            // 滴下されるレメディー数は最大、小瓶２、大瓶４、チンクチャー５、バックエンドは無制限だが、カラム数を全体でそろえずともプリンタが処理できるため、あるだけカンマ区切りでつなげる
            $count = count($this->model->drops);
            if($count > 0) {
                foreach($this->model->drops as $drop) {
                    $drop_name = "";
                    if(! $drop->remedy){
                         foreach($drop->attributes as $attribute) {
                             $drop_name .= $attribute;
                         }
                    } else {
                        $name = isset($drop->remedy) ? $drop->remedy->name : "error";
                        $potency = isset($drop->potency) ? preg_replace('/combination/','',$drop->potency->name) : "error";

                        $drop_name .= "+";
                        $drop_name .= $name;
                        $drop_name .= $potency;
                    }
                    $items[] = $drop_name;
                }
            }
        }
        if(count($items) == 0)
            return null;
        
        $csv = '"' . implode(',', $items) . ',"'.$this->eol;
        return $csv;

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
