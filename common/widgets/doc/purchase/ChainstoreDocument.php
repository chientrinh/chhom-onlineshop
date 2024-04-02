<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/ChainstoreDocument.php $
 * $Id: ChainstoreDocument.php 2945 2016-10-10 02:43:10Z mori $
 */

use Yii;
use \yii\helpers\ArrayHelper;
use \common\models\Company;

class ChainstoreDocument extends \yii\base\Widget
{
    const IMAGE_FILE  = 'chainstore.png';

    /**
     * @var \common\models\Purchase
     */
    public $model;

    /**
     * @var string: absolute path of pdf cache, should be read only
     */
    private $_pdffile;

    public function init()
    {
        parent::init();

        $prefix   = 'chainstore';
        $basename = sprintf('%06d', $this->model->primaryKey);

        $this->_pdffile = sprintf('%s/%s%s.pdf', Yii::getAlias('@runtime'), $prefix, $basename);
    }

    public function run()
    {
        return $this->render('chainstore',['model'=>$this->model,'background'=>$this->backgroundImage]);
    }

    public function getBackgroundImage()
    {
        $filename = sprintf('%s/views/%s', __DIR__, self::IMAGE_FILE);
        $binary   = file_get_contents($filename);
        $type     = pathinfo($filename, PATHINFO_EXTENSION);
        $ascii    = 'data:image/' . $type . ';base64,' . base64_encode($binary);

        return $ascii;
    }

    public function getPdffile()
    {
        if(is_file($this->_pdffile) &&
           (strtotime($this->model->update_date) < stat($this->_pdffile)['mtime']))
        {
            return $this->_pdffile;
        }

        $html = $this->run();

        $pdf = new \mPDF('',
                         array(297,210),
                         8, // font size
                         'SJIS', // default fond
                         20,     // margin_left
                         20,     // margin right
                         44,     // margin top
                         44,     // margin bottom
                         0,
                         0,
                         'P');

        $pdf->WriteHtml($html);
        $pdf->Output($this->_pdffile, 'F');

        return $this->_pdffile;
    }
}
