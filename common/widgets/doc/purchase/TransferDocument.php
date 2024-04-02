<?php
namespace common\widgets\doc\purchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/TransferDocument.php $
 * $Id: TransferDocument.php 1769 2015-11-05 09:55:16Z mori $
 */

use Yii;

class TransferDocument extends PurchaseDocument
{
    const RUNTIME_DIR = '@app/runtime/doc/transfer';
    const STORAGE_DIR = '@common/storage/doc/transfer';

    protected function renderHtml()
    {
        if($this->cache && is_file($this->htmlfile) && 
           (strtotime($this->model->update_date) < stat($this->htmlfile)['mtime']))
        {
            return $this->getCache();
        }

        $html = [];
        $html[] = PickingList::widget(['model'      => $this->model,'title'=>'仕訳伝票 (店舗間移動)']);
        $html[] = DeliveryDocument::widget(['model' => $this->model,'title'=>'納品書 (店舗間移動)']);
        $html   = implode("<pagebreak />", $html);

        $this->setCache($html);

        return $html;
    }

}
