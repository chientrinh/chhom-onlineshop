<?php
namespace common\widgets\doc\product;

/**
 * $URL: http://test-webhj.homoeopathy.co.jp:8000/svn/MALL/common/widgets/doc/remedy/RemedyLabel.php $
 * $Id: RemedyLabel.php 1223 2015-08-02 01:35:03Z mori $
 */

use Yii;
use \yii\helpers\Html;
use \common\models\Product;

class ProductLabel extends \yii\base\Widget
{
    /* @var Product model */
    public  $model;

    //public $pageWidth  = 46;
    //public $pageHeight = 37;
    public  $pageWidth  = 52;
    public $pageHeight = 29;

    public $pdffile;

    /**
     * @var string the layout that determines how different sections of the list view should be organized.
     * The following tokens will be replaced with the corresponding section contents:
     *
     * - `{name}`: the product name. See [[renderName()]].
     * - `{price}`: the product price. See [[renderPrice()]].
     * - `{barcode}`: the barcode. See [[renderBarcode()]].
     */
    public  $layout = "{barcode}\n{name}";

    /* @var string of CSS */
    public  $csscode = "

p, div {
    margin: 0mm;
    padding: 0mm;
    text-align: center;
}


div.divname {
   text-align: left;
   float:left;
}

div.divprice {
   text-align: right;
}

.wrapper {
    margin: 0 auto auto 1px;
    text-align: center;
}
.txt {
    margin: 0 auto auto 1px;
    display: inline-block;
    text-align: left;
}

.price {
    padding: 0mm;
    text-align: right;
}
";

    public function init()
    {
        parent::init();

        $this->csscode = sprintf($this->csscode, $this->pageWidth, $this->pageHeight);
        $this->view->registerCss($this->csscode);
    }

    public function run()
    {
        echo $this->beginWrapper();
        echo $this->generateLabel();
        echo $this->endWrapper();
    }

    private function generateLabel()
    {
        $content = preg_replace_callback("/{\\w+}/", function ($matches) {
            $content = $this->renderSection($matches[0]);
            return $content === false ? $matches[0] : $content;
        }, $this->layout);

        return $content;
    }

    private function beginWrapper()
    {
        return '<page>';
    }

    private function endWrapper()
    {
        return '</page>';
    }

    private function renderSection($name)
    {
        switch ($name) {
            case '{barcode}':
                return $this->renderBarcode();
            case '{name}':
                return $this->renderName();
            case '{price}':
                break;;
        //        return $this->renderPrice();
            default:
                return false;
        }
    }

    private function renderBarcode()
    {
        $html = sprintf('<div class="barcodecell"><barcode code="%s" type="EAN13" size=1.1 height="0.4" class="barcode" /></div>',
                        $this->model->barcode);
        return $html;
    }

    private function renderName()
    {
        $name = $this->model->name;
        
        if(15 < mb_strlen($name)){
            $price = sprintf('%s（税別）',Yii::$app->formatter->asCurrency($this->model->price));
            $count = mb_strlen($name);
            $pricecount = mb_strlen($price);
            $i = 0;
            while($count >  17) {
                    
	            $html .= sprintf('<div class="wrapper" align="center"><p class="txt">%s</p></div>',mb_substr($name, $i, 17));
                    //$html .= sprintf('<table border=0 width="100%%"><tr><td style="padding:0px; margin:0px;">%s</td></tr></table>',mb_substr($name, $i, 20));
                    $i += 17;
                    $count -= 17;
                    continue;

            }
//var_dump(18-$count);
//var_dump($pricecount);exit;
            if(17 - $count+3 >= $pricecount) {
                $html .= sprintf('<table style="border:0; padding:0; margin:-2px 0 0 0px;" width="100%%"><tr><td style="border:0; padding:0; margin:0;" align="left">%s</td><td align="right">%s</td></tr></table>',mb_substr($name, $i),$price);
            } else {
                $html .= sprintf('<div class="wrapper" align="center"><p class="txt">%s</p></div>',mb_substr($name, $i)).$this->renderPrice();
            }

            
        } else {
            $html = Html::tag('p',$this->model->name,['class'=>'name']).$this->renderPrice();
        }
//var_dump($html);exit;
	return $html;
    }

    private function renderPrice()
    {
        return Html::tag('p', sprintf("%s（税別）",Yii::$app->formatter->asCurrency($this->model->price)),['class'=>'price']);
    }

    public function renderPdf()
    {
        if(! $this->pdffile)
             $this->pdffile = tempnam(Yii::getAlias('@runtime'), basename(__FILE__));

        $this->generatePdf();

        return $this->pdffile;
    }

    private function generatePdf()
    {
        require_once(Yii::getAlias('@common/components/mPdf.php'));

        define('_MPDF_SYSTEM_TTFONTS',"@vendor/kartik-v/mpdf/ttfonts/");
        $JpFontName = 'ipapgothic';
        
        $html = $this->render('layout',[
            'content' => $this->generateLabel(),
        ]);

//        $mpdf = new \mPDF('ja+aCJK', array($this->pageWidth,$this->pageHeight),8,'SJIS',1,1,4,0,0,0);
        $mpdf = new \mPDF('ja+aCJK', array($this->pageWidth,$this->pageHeight),8,'SJIS',1,1,3,0,0,0);
        $mpdf->fontdata[$JpFontName] = array(
                'R' => 'ipagp.ttf',
        );
        $mpdf->available_unifonts[] = $JpFontName;
        $mpdf->default_available_fonts[] = $JpFontName;
        $mpdf->BMPonly[] = $JpFontName;

        $mpdf->SetDefaultFont($JpFontName);
        return \common\components\mPdf::saveAs($this->pdffile, $html, [], $mpdf);
    }
}
