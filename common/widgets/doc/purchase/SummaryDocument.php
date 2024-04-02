<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/SummaryDocument.php $
 * $Id: SummaryDocument.php 2170 2016-02-28 01:41:43Z mori $
 */

use Yii;

class SummaryDocument extends \yii\base\Widget
{
    /* @var Purchase model */
    public  $model;

    /* @var array represents DetailView::attributes */
    public $attributes = [];

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $this->registerCss();

        $html  = '';
        $html .= $this->beginWrapper();
        $html .= $this->renderHtml();
        $html .= $this->endWrapper();

        return $html;
    }

    private function beginWrapper()
    {
        return '<page>';
    }

    private function endWrapper()
    {
        return '</page>';
    }

    private function renderHtml()
    {
        return $this->render('summary', [
            'model'      => $this->model,
            'attributes' => $this->attributes,
        ]);
    }

    private function registerCss()
    {
        $csscode = <<<CSS
#SummaryDocument > thead { display: table-header-group }
#SummaryDocument > tfoot { display: table-row-group }
#SummaryDocument > tr { page-break-inside: avoid }

body {
    lang: ja;
}

#SummaryDocument > h1 { width:100%; text-align:center; font-size: 12pt; margin-top:5px; margin-bottom:5; }

#SummaryDocument > p, th, td
{
    font-size:9pt;
}

.text-center { text-align:center }
.text-right  { text-align:right }
.text-left   { text-align:left }

#SummaryDocument > table
{
    width:  100%;
    vertical-align:top;
    border-collapse:collapse;
    cellspacing:0;
}
#SummaryDocument > #delivery-summary td,
{
    font-weight: bold;
}
CSS;
        $this->view->registerCss($csscode);
    }
}
