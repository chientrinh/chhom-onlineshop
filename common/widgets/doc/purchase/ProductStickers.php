<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/ProductStickers.php $
 * $Id: ProductStickers.php 3053 2016-10-30 02:38:25Z mori $
 */

use Yii;
use \yii\helpers\ArrayHelper;
use \yii\helpers\Html;
use \common\models\Product;

class ProductStickers extends \yii\base\Widget
{
    const PDF_MERGER  = '/usr/bin/pdfunite';

    public $models;

    /**
     * @var array
     * The default configuration used by ProductLabel when creating a new widget.
     */
    public $fieldConfig = [];

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $pdffile = [];

        foreach($this->models as $model)
            foreach($model->items as $item)
        {
            $filename = $this->renderFile($item);

            for($i = 0; $i < $item->quantity; $i++)
                $pdffile[] = $filename;
        }

        $output = $this->unitePdf($pdffile);

        return $output;
    }

    public function renderFile($item)
    {
        $model = (object) [
            'name'    => $item->name,
            'barcode' => ArrayHelper::getValue($item, 'model.barcode', $item->code),
            'price'   => $item->price,
        ];

        $this->fieldConfig['model'] = $model;
        $widget = \common\widgets\doc\product\ProductLabel::begin($this->fieldConfig);
        $widget->renderPdf();

        return $widget->pdffile;
    }

    private function unitePdf($pdffile)
    {
        $basename = \common\components\Security::generateRandomString(16);
        $output   = Yii::getAlias('@runtime/' . $basename . '.pdf');

        if(0 == count($pdffile))
            throw new \yii\base\Exception('no item to print');
        elseif(1 == count($pdffile))
            $output = array_shift($pdffile);
        else
        {
            $command = sprintf('%s %s %s', self::PDF_MERGER, implode(' ', $pdffile), $output);
            system($command);

            if(! is_file($output))
                Yii::error(sprintf('%s::%s failed',__CLASS__,__FUNCTION__));

            if(! is_file($output))
                throw new \yii\base\Exception('failed to generate pdf by command: '.$command);
        }

        return $output;
    }

}
