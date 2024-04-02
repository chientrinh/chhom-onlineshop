<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/PurchaseDocument.php $
 * $Id: PurchaseDocument.php 3956 2018-07-05 04:27:06Z mori $
 */

use Yii;
use \common\models\Branch;

class PurchaseDocument extends \yii\base\Widget
{
    const RUNTIME_DIR = '@app/runtime/doc/purchase';
    const STORAGE_DIR = '@common/storage/doc/purchase';

    /**
     * @var PDF_MERGER absolute path of `pdfunite`
     * Caution: this class is completely dependent on this executable, no warranty without it
     */
    const PDF_MERGER  = '/usr/bin/pdfunite';

    /**
     * @var string: (auto | delivery | picking) の 3 種
     * 'auto'     - 自動判別
     * 'delivery' - 納品書のみ
     * 'picking'  - 仕訳票のみ
     */
    public $target = 'auto';

    /**
     * @var bool: use cache of (pdf | html), defaults to true
     */
    public $cache = true;

    /**
     * @var \common\models\Purchase
     */
    public $model;

    /**
     * @var string: absolute path of pdf cache, should be read only
     */
    public $pdffile;

    /**
     * @var string: absolute path of html cache, should be read only
     */
    public $htmlfile;

    public function init()
    {
        parent::init();
        if(! is_executable(self::PDF_MERGER))
            throw new \yii\web\ServerErrorHttpException('application setup failure to manage pdf');

        if(! is_dir(Yii::getAlias(static::RUNTIME_DIR)))
            if(! \yii\helpers\BaseFileHelper::createDirectory(Yii::getAlias(static::RUNTIME_DIR)))
                throw new \yii\web\ServerErrorHttpException('application setup failure to create runtime dir');

        $prefix   = ('auto' == $this->target) ? 'purchase' : $this->target;
        $basename = sprintf('%06d', $this->model->primaryKey);
        $this->pdffile   = sprintf('%s/%s%s.pdf',  Yii::getAlias(static::STORAGE_DIR), $prefix, $basename);
        $this->htmlfile  = sprintf('%s/%s%s.html', Yii::getAlias(static::STORAGE_DIR), $prefix, $basename);

        if(\common\models\Payment::PKEY_DROP_SHIPPING == $this->model->payment_id)
        foreach($this->model->items as $k => $item)
        {
            if(0 < $item->discount_rate)  { $item->discount_rate   = 0; }
            if(0 < $item->discount_amount){ $item->discount_amount = 0; }
        }
    }

    public function run()
    {
        return $this->renderHtml();
    }

    protected function renderHtml()
    {
/*
        if($this->cache && is_file($this->htmlfile) &&
           (strtotime($this->model->update_date) < stat($this->htmlfile)['mtime']))
        {
            return $this->getCache();
        }
*/
        $html = [];

        switch($this->target)
        {
        case 'delivery':
            break;

        default:
        case 'auto':
            if(! in_array($this->model->branch_id, [Branch::PKEY_ATAMI,
                                                    Branch::PKEY_ROPPONMATSU]))
                break; // 発送所で受付していないものは auto では仕訳票を省略する

        case 'picking':
            $html[] = html_entity_decode(PickingList::widget(['model'  => $this->model]));
        }

        if('picking' != $this->target) // $target が picking でない場合はつねに納品書を出力する
        $html[] = html_entity_decode(DeliveryDocument::widget(['model' => $this->model]));

        $html   = implode("<pagebreak />", $html);

        if($this->cache)
            $this->setCache($html);

        return $html;
    }

    protected function getCache()
    {
        $fp   = fopen($this->htmlfile,'r');
        $html = fread($fp, filesize($this->htmlfile));
        fclose($fp);

        return $html;
    }

    protected function setCache($html)
    {
        // save as html
        $fp = fopen($this->htmlfile,'w');
        fwrite($fp, $html);
        fclose($fp);

        // save as pdf
        
        define('_MPDF_SYSTEM_TTFONTS',"@vendor/kartik-v/mpdf/ttfonts/");
        $JpFontName = 'ipapgothic';

        $mpdf = new \mPDF('ja+aCJK', 'A4', 0, 'SJIS', 10, 10, 5, 5, 0, 0, '');
        $mpdf->fontdata[$JpFontName] = array(
                'R' => 'ipagp.ttf',
        );
        $mpdf->available_unifonts[] = $JpFontName;
        $mpdf->default_available_fonts[] = $JpFontName;
        $mpdf->BMPonly[] = $JpFontName;

        $mpdf->SetDefaultFont($JpFontName);
        $footer = sprintf(" |  | %06d", $this->model->purchase_id);
        \common\components\mPdf::saveAs($this->pdffile, $html, ['footer' => $footer], $mpdf);

        return is_file($this->pdffile);
    }
    
    public static function getMergedPdf($models, $cache=true)
    {
        $pdffile = [];
        if(! $models)
            throw new \yii\base\Exception('model is empty');

        foreach($models as $model)
        {
            $widget = new static(['model'=>$model,'cache'=>$cache]);
            $widget->run();
            $pdffile[] = $widget->pdffile;
        }
        if(1 == count($pdffile))
            return array_shift($pdffile);

        $output = Yii::getAlias(sprintf('@runtime/%s_%s_%s.pdf',
                                        date('Ymd'),
                                        date('Hi'),
                                        \common\components\Security::generateRandomString(4)));

        $command = sprintf('%s %s %s', self::PDF_MERGER, implode(' ', $pdffile), $output);
        system($command);
        if(! is_file($output))
            Yii::error(sprintf('%s::%s failed',__CLASS__,__FUNCTION__));

        if(! is_file($output))
            throw new \yii\base\Exception('failed to generate pdf by command: '.$command);

        return $output;
    }

}
