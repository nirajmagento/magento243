<?php

namespace Watchcentre\Mobile\Model;

use Magento\Framework\Model\AbstractModel;
use Watchcentre\Mobile\Model\ResourceModel\StockNotify as ResourceModel;

class StockNotify extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'watchcenter_mobile_product_stock_notify_model';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

}
