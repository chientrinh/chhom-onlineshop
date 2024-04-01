<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy/_csv.php $
 * $Id: _csv.php 840 2015-04-01 08:44:28Z mori $
 */
echo implode(
    ',',
    [$model->remedy_id, $model->abbr, $model->latin, $model->ja, $model->concept, $model->on_sale]
    ),
    "\n";
?>
