<?php
namespace common\modules\recipe\widgets;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/widgets/DropDown.php $
 * $Id: DropDown.php 1761 2015-11-03 17:15:32Z mori $
 *
 */

use Yii;
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

class DropDown extends \yii\bootstrap\Dropdown
{
    const CACHE_TIMEOUT = 3600;

    public $index;
    public $value;
    public $label;

    public function init()
    {
        parent::init();

        // init this->items
        $this->items = [];
        $allModels   = self::getRecipeInstructions();
        foreach($allModels as $model)
            $this->items[] = [
                'label' => ArrayHelper::getValue($model, 'name'),
                'url'   => [
                    'add',
                    'target'=> 'instruction',
                    'index' => $this->index,
                    'value' => ArrayHelper::getValue($model, 'instruct_id'),
                ],
            ];

        // init this->label
        if(! isset($this->label) && $this->value)
        {
            if($model = \common\models\RecipeInstruction::findOne($this->value))
                $this->label = ArrayHelper::getValue($model, 'name');
        }
    }

    public function run()
    {
        return $this->beginWrapper()
             . parent::run()
             . $this->endWrapper();
    }

    public function beginWrapper()
    {
        return  '<div class="dropdown">'
             .  Html::a($this->label.'<b class="caret"></b>','#',['data-toggle'=>"dropdown", 'class'=>"dropdown-toggle"]);
    }

    public function endWrapper()
    {
        return '</div>';
    }

    public static function getRecipeInstructions()
    {        
        $db   = Yii::$app->db;
        return $db->createCommand('SELECT instruct_id, name FROM mtb_recipe_instruction')->queryAll();
        $rows = $db->cache(function ($db) {

            // the result of the SQL query will be served from the cache
            // if query caching is enabled and the query result is found in the cache
            return $db->createCommand('SELECT instruct_id, name FROM mtb_recipe_instruction')->queryAll();

        }, self::CACHE_TIMEOUT);

        return $rows;
    }

}
