<?php

namespace frontend\widgets;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/SubcategoryMenu.php $
 * $Id: SubcategoryMenu.php 2905 2016-09-30 08:43:57Z mori $
 */

use Yii;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \common\models\Company;

class SubcategoryMenuForSmartPhone extends \yii\widgets\Menu
{
    public $company = null;
    public $cat_id;
    public $sub_id;
    public $items = null;
    public $seeds = [];
    public $title = '';
    
    public $submenuTemplate = '<ul class="category-list">{items}</ul>';
    public $submenuDivClass = 'form-group field-searchform-subcategory search-sm-main subCategory';
    public $activeCssClass  = 'alert-info';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if(is_numeric($this->company))
            $this->company = Company::findOne($this->company);

        if(! $this->sub_id)
            $this->sub_id = Yii::$app->request->get('id');

        if(! is_array($this->sub_id))
            $this->sub_id = array($this->sub_id);

        $this->loadItems();
    }

    public function run()
    {
        if(! $this->items)
            return '';

        echo $this->renderHeader();
        echo $this->renderItems();
        echo $this->renderFooter();
    }

    public function renderHeader()
    {
	    return implode('', [
            '<div class="'. $this->submenuDivClass. '">',
		    '<label class="control-label line-dot" for="searchform-subcategory">'.$this->title.'</label>',
        ]);
    }

    public function renderItems($items = null)
    {
        if(null === $items)
            $items = $this->items;

        return implode('',[
            '<ul class="category-list">',
            parent::renderItems($items),
            '</ul>'
        ]);
    }

    public function renderFooter()
    {
		return implode('', [
            '</div>'
        ]);
    }

    private function loadItems()
    {
        $query = \common\models\Subcategory::find()->andFilterWhere([
            'company_id' => ($this->company instanceof Company ? $this->company->company_id : $this->company),
        ])->orderBy('weight DESC, subcategory_id ASC');
        if($this->seeds)
            $query->andWhere(['subcategory_id'=>$this->seeds]);

        if(! $user = Yii::$app->user->identity)
            $query->andWhere(['restrict_id' => 0]);
        else
            $query->andWhere(['<=', 'restrict_id', $user->grade_id ? $user->grade_id : 0]);

        $models = $query->all();

        $tree   = \yii\helpers\ArrayHelper::map($models, 'subcategory_id', 'name', 'parent_id');
        $keys   = array_keys($tree);

        asort($keys);
        foreach($keys as $key)
        {
            if(! isset($tree[$key]))
                continue;

            foreach($tree[$key] as $sub_id => $name)
            {
                $this->items[$sub_id] = [
                    'label' => $name,
                    'url'   => $this->seeds
                           ? Url::current(['subcategory_id'=>$sub_id]) // used only at category/viewbyname
                           : [sprintf('/%s/subcategory',$this->company->key),'id'=>$sub_id],
                    'items' => [],
                    'active' => in_array($sub_id, $this->sub_id),
                ];

                if(isset($tree[$sub_id]))
                {
                    $this->items[$sub_id]['items'] = $this->appendItems($tree[$sub_id]);
                    unset($tree[$sub_id]);
                }
            }
        }

    }

    private function appendItems($rows)
    {
        $items = [];
        foreach($rows as $sub_id => $name)
        {
            $items[$sub_id] = [
                'label' => $name,
                'url'   => $this->seeds
                        ? Url::current(['subcategory_id'=>$sub_id])
                        : [sprintf('/%s/subcategory',$this->company->key),'id'=>$sub_id],
                'items' => [],
                'active' => in_array($sub_id, $this->sub_id),
            ];
        }
        return $items;
    }

    
}
