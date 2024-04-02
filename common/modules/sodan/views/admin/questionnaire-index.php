<?php 
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/admin/questionnaire-index.php $
 * $Id: questionnaire-index.php 3851 2018-04-24 09:07:27Z mori $
 *
 * $this \yii\web\View
 * $dataProvider
 * $searchModel
 */
?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
]) ?>
