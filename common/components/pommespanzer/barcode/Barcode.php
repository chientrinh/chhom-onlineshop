<?php
namespace common\components\pommespanzer\barcode;

/**
 * @link   https://github.com/EddieZhao/yii2-barcode
 * @link   https://github.com/Pommespanzer/yii2-barcode
 * @link   $URL: https://tarax.toyouke.com/svn/MALL/common/components/pommespanzer/barcode/Barcode.php $
 * @author https://github.com/EddieZhao
 * @author https://github.com/Pommespanzer
 * @author Reiko Mori <mori@homoeopathy.co.jp>
 * @version $Id: Barcode.php 1210 2015-07-26 12:50:45Z mori $
 */

require_once('class' . DIRECTORY_SEPARATOR . 'BCGColor.php');
require_once('class' . DIRECTORY_SEPARATOR . 'BCGBarcode.php');
require_once('class' . DIRECTORY_SEPARATOR . 'BCGDrawing.php');
require_once('class' . DIRECTORY_SEPARATOR . 'BCGFontFile.php');
include_once('class' . DIRECTORY_SEPARATOR . 'BCGcode128.barcode.php');
include_once('class' . DIRECTORY_SEPARATOR . 'BCGean13.barcode.php');

use \BCGArgumentException;
use \BCGean13;
use \BCGcode128;
use \BCGColor;
use \BCGDrawException;
use \BCGDrawing;
use \BCGFontFile;

class Barcode
{
    /**
     * @param string $message
     * @param int $format
     * @return resource
     * @throws BCGArgumentException
     * @throws BCGDrawException
     */
    public function run($text, $filepath, $options)
    {
        $colors = [
            'black' => New BCGColor(0,   0,   0),
            'white' => New BCGColor(255, 255, 255),
        ];
        $formats = [
            'png'=> BCGDrawing::IMG_FORMAT_PNG,
            'jpg'=> BCGDrawing::IMG_FORMAT_JPEG,
            'gif'=> BCGDrawing::IMG_FORMAT_GIF,
            'bmp'=> BCGDrawing::IMG_FORMAT_WBMP,
        ];
        if(isset($options['format']) && isset($formats[$options['format']]))
            $format = $formats[$options['format']];
        else
            $format = array_shift($formats);

        if((13 == strlen($text)) && is_numeric($text))
            $barcode = new BCGean13();
        else
            $barcode = new BCGcode128();

        self::setup($barcode);

        $barcode->setScale(max(1, min(4, 4)));
        $barcode->setBackgroundColor($colors['white']);
        $barcode->setForegroundColor($colors['black']);
        if(isset($options['label']))
            $barcode->setLabel($options['label']);
        $barcode->parse($text);

        $drawing = new BCGDrawing($filepath, $colors['white']);
        $drawing->setBarcode($barcode);
        $drawing->setRotationAngle(0);
        $drawing->setDPI(isset($options['dpi']) ? $options['dpi'] : 72);
        $drawing->draw();
        $drawing->finish($format);
    }

    /**
     * @param BCGcode128 $barcode
     * @throws BCGArgumentException
     */
    private static function setup($barcode)
    {
        $font = new BCGFontFile(__DIR__ . '/font/Arial.ttf', 15);
        $barcode->setFont($font);
        $barcode->setThickness(max(9, min(90, 25)));
    }

}
