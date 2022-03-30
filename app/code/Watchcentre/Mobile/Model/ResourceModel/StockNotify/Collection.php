<?php

namespace Watchcentre\Mobile\Model\ResourceModel\StockNotify;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Watchcentre\Mobile\Model\ResourceModel\StockNotify as ResourceModel;
use Watchcentre\Mobile\Model\StockNotify as Model;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'watchcenter_mobile_product_stock_notify_collection';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
