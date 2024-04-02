<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/mPdf.php $
 * $Id: mPdf.php 2729 2016-07-16 07:16:04Z mori $
 */
namespace common\components;
use Yii;

define('_MPDF_TTFONTDATAPATH',Yii::getAlias('@app/runtime/mpdf'));
define('_MPDF_TEMP_PATH',     Yii::getAlias('@app/runtime/mpdf'));

require_once(Yii::getAlias('@common/../vendor/kartik-v/mpdf/mpdf.php'));

/**
 * The EYiiPdfException exception class.
 * @author Borales <bordun.alexandr@gmail.com>
 * @link https://github.com/Borales/yii-pdf
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @package application.extensions.yii-pdf.EYiiPdf
 * @version 0.4.0
 */
class mPdf extends \yii\base\Object
{
    private $fonts = [
        'mincho' => 'kozminproregular',
        'gothic' => 'kozgopromedium',
    ];

    public function init()
    {
        parent::init();

        // set default params: font, page, margin
        
        if(! isset($this->params['font']))
            $this->params['font'] = $this->fonts['mincho'];

        if(! isset($this->params['page']))
            $this->params['page'] = 'A4';

        if(! isset($this->params['margin']))
            $this->params['margin'] = array(15, 15, 15, 8); // left, top, right, buttom
    }

	/**
	 * @return HTML2PDF
	 */
	public function mPDF()
	{
        $mpdf = new \mPDF('ja+aCJK','A4',9,'SJIS');
        $mpdf->defaultheaderline = 0;
        $mpdf->defaultfooterline = 0;
		// $args = func_get_args();
        // if(isset($args['font']))
        //     $this->params['font'] = $args['font'];

        //$mpdf->setDefaultFont('SJIS');

        return $mpdf;
	}

    public static function saveAs($filename, $html, $options, $mpdf=null)
    {
        if(! $mpdf)
            $mpdf = new \mPDF('ja+aCJK','A4',9,'SJIS');

        $mpdf->defaultheaderline = 0;
        $mpdf->defaultfooterline = 0;

        if(isset($options['header']))
            $mpdf->setHeader($options['header']);

        if(isset($options['footer']))
            $mpdf->setFooter($options['footer']);

        $mpdf->WriteHtml($html);
        $mpdf->Output($filename);

        return is_file($filename);
    }

}
