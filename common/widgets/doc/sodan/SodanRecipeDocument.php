<?php
namespace common\widgets\doc\sodan;

use Yii;
use common\models\Recipe;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/recipe/RecipeDocument.php $
 * $Id: RecipeDocument.php 1175 2015-07-21 05:44:45Z mori $
 */

class SodanRecipeDocument extends \yii\base\Widget
{
    public $model;

    public function __construct($config=[])
    {
        parent::__construct($config);
    }

    public function init()
    {
        parent::init();

        if(! $this->model)
            $this->model = new Recipe(); // Null object
    }

    public function run()
    {
        return $this->render('print', ['model' => $this->model]);
    }

}