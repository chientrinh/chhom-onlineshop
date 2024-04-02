<?php
namespace common\widgets\doc\sodan;

use Yii;
use common\models\sodan\Interview;
use \common\widgets\doc\sodan\SodanRecipeDocument;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/sodan/SodanDocument.php $
 * $Id: SodanDocument.php 3945 2018-06-22 03:15:29Z mori $
 */

class SodanDocument extends \yii\base\Widget
{
    /* @var Interview */
    public $model;

    public function init()
    {
        parent::init();

        if(! $this->model)
             $this->model = new Interview(); // Null object
    }

    public function run()
    {
        return $this->renderItems();
    }

    public function renderItems()
    {
        $html[] = $this->renderCard();
        $html[] = $this->renderRecipe();
        $html[] = $this->renderRecipeInstruction();

        return implode('<p style="page-break-after:always;"></p>', $html);
    }

    public function pdf()
    {
        $mpdf = new \mPDF('ja+aCJK', 'A4', 0, 'SJIS', 10, 10, 5, 5, 0, 0, '');

        $mpdf->writeHtml($this->renderCard());
        $mpdf->AddPage();

        $mpdf->writeHtml($this->renderRecipe());
        $mpdf->AddPage();

        $mpdf->writeHtml($this->renderRecipeInstruction());

        $mpdf->output();
    }

    public function reserve()
    {
        $mpdf = \common\components\mPdf::mPDF();

        $mpdf->writeHtml($this->renderReserve());

        $mpdf->output();
    }

    private function renderCard()
    {
        return $this->render('layout',[
            'content' => $this->render('message-card',[
                'model'=>$this->model,
                'img' => $this->imageData('graph.jpg')
            ])
        ]);
    }

    private function renderRecipe()
    {
        $recipe = ($recipe_id = Yii::$app->request->get('recipe_id')) ? \common\models\Recipe::findOne($recipe_id) : $this->model->recipe;
        return SodanRecipeDocument::widget(['model' => $recipe]);
    }

    private function renderReserve()
    {
        return SodanReserveDocument::widget(['model' => $this->model]);
    }

    private function renderRecipeInstruction()
    {
        $recipe = ($recipe_id = Yii::$app->request->get('recipe_id')) ? \common\models\Recipe::findOne($recipe_id) : $this->model->recipe;
        return $this->render('how-to-use-remedy',[
            'imgA' => $this->imageData('glass-05ml.jpg'),
            'imgB' => $this->imageData('glass-20ml.jpg'),
            'imgC' => $this->imageData('plastic.jpg'),
            'model' => $recipe
        ]);
    }

    private function imageData($basename)
    {
        $filename = $this->viewPath . '/' . $basename;
        $binary   = file_get_contents($filename);
        $type     = pathinfo($filename, PATHINFO_EXTENSION);
        $ascii    = 'data:image/' . $type . ';base64,' . base64_encode($binary);

        return $ascii;
    }

}
