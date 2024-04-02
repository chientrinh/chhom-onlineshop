<?php
namespace common\widgets\doc\invoice;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/invoice/InvoiceDocument.php $
 * $Id: InvoiceDocument.php 3778 2017-12-01 04:58:09Z kawai $
 */

use Yii;
use \common\models\Company;

class InvoiceDocument extends \yii\base\Widget
{
    const STORAGE_PATH = '@common/storage/doc/invoice';
    const STAMP_PATH   = '@backend/web/img/stamp';
    const STAMP_FORMAT = 'png';

    /* @var Invoice model */
    public  $model;

    /* @var Company model */
    public  $company;

    /* @var cache file path */
    public  $pdffile;

    public function init()
    {
        parent::init();

        if(! $this->company)
             $this->company = Company::findOne(Company::PKEY_TY);

        $dirname = Yii::getAlias(self::STORAGE_PATH);
        if(! is_dir($dirname))
            mkdir($dirname, 0777, true);

        $filename      = sprintf(self::STORAGE_PATH.'/%s_%06d_%06d.pdf', date('Ym', strtotime($this->model->target_date)), $this->model->invoice_id, $this->model->customer_id);
        $this->pdffile = Yii::getAlias($filename);
    }

    public function run()
    {
        \common\assets\BootstrapAsset::register($this->view);
        $this->view->registerCss('p,th,td{font-size:9pt;}');

        $html = $this->renderContent();

        return $html;
    }

    private function renderContent()
    {
        return $this->render('print', [
            'model'   => $this->model,
            'company' => $this->company,
            'stamp'   => $this->stampImage,
        ]);
    }

    public function renderPdf()
    {
        ini_set("memory_limit", "3G");
        set_time_limit(0);

        $filename = $this->pdffile;

        if(! is_file($filename))
            $this->generatePdf();

        elseif(stat($filename)['mtime'] < strtotime($this->model->update_date))
            $this->renewPdf();

        if(! is_file($filename))
            Yii::error('InvoiceDocument::generatePdf() failed');

        return $filename;
    }

    private function generatePdf()
    {
        $cssfile = Yii::getAlias('@common/widgets/views/bootstrap.css');
        $fp      = fopen($cssfile, "r");
        $cssdata = fread($fp, filesize($cssfile));

        $this->view->registerCss($cssdata);

        $html = $this->render('layout',[
            'content' => $this->renderContent(),
        ]);

        return \common\components\mPdf::saveAs($this->pdffile, $html, ['footer'=>$footer]);        
    }

    public function getStampImage()
    {
        $filename = sprintf('%s/%s.%s', Yii::getAlias(self::STAMP_PATH),
                                        $this->company->key,
                                        self::STAMP_FORMAT);
        $binary   = file_get_contents($filename);
        $type     = pathinfo($filename, PATHINFO_EXTENSION);
        $ascii    = 'data:image/' . $type . ';base64,' . base64_encode($binary);

        return $ascii;
    }

    private function renewPdf()
    {
        $this->voidCache();

        return $this->generatePdf();
    }

    private function voidCache()
    {
        $i = 0;
        do {
            $voidName = sprintf('%s.void.%d', $this->pdffile, $i++);
        } while(is_file($voidName));

        rename($this->pdffile, $voidName);
    }
}
