<?php
namespace common\widgets;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/CustomerView.php $
 * $Id: CustomerView.php 1818 2015-11-16 18:26:27Z mori $
 */

use Yii;

class CustomerView extends \yii\base\Widget
{
    /* @var Customer model */
    public $model;

    private $_backend;

    public function init()
    {
        parent::init();

        $this->_backend = ('app-backend' === Yii::$app->id);
    }

    public function run()
    {
        $this->renderDetailView();
    }

    public function renderDetailView()
    {
        echo $this->render('customer-view-detail',['model'=>$this->model,'backend'=>$this->_backend]);
    }

    public function renderMemberships()
    {
        echo $this->render('customer-view-memberships',['model'=>$this->model]);
    }

    public function renderNewsletter()
    {
        echo $this->render('customer-view-newsletter',['model'=>$this->model,'backend'=>$this->_backend]);
    }

    public function renderPointings()
    {
        $query = $this->model->getPointings();
        $user  = Yii::$app->user->identity;

        if(! $user instanceof \backend\models\Staff)
            $query->andWhere(['seller_id' => $user->customer_id]);

        echo $this->render('customer-view-pointings',['model'=>$this->model, 'query'=>$query, 'backend'=>$this->_backend]);
    }

    private function renderRecipes()
    {
        echo $this->render('customer-view-recipes',['model'=>$this->model]);
    }

}
