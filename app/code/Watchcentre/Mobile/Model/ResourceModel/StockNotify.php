<?php

namespace Watchcentre\Mobile\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;

class StockNotify extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'watchcenter_mobile_product_stock_notify_resource_model';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('watchcenter_mobile_product_stock_notify', 'id');
    }

    public function getIsUniqueProductCustomer($productId, $customerId)
    {
        $select = $this->getConnection()->select()
            ->from(['cb' => $this->getMainTable()])
            ->where('cb.product_id = ?  ', $productId)
            ->where('cb.customer_id = ?  ', $customerId)
            ->where('cb.is_notify = ?  ', 0);

        if ($this->getConnection()->fetchRow($select)) {
            return false;
        }
        return true;
    }

    public function getIsAlreadyNotifyProductCustomer($productId, $customerId)
    {
        $select = $this->getConnection()->select()
            ->from(['cb' => $this->getMainTable()])
            ->where('cb.product_id = ?  ', $productId)
            ->where('cb.customer_id = ?  ', $customerId)
            ->where('cb.is_notify = ?  ', 1);

        if ($this->getConnection()->fetchRow($select)) {
            return false;
        }
        return true;
    }

    public function getAlreadyNotifyProductCustomerId($productId, $customerId)
    {
        $select = $this->getConnection()->select()
            ->from(['cb' => $this->getMainTable()])
            ->where('cb.product_id = ?  ', $productId)
            ->where('cb.customer_id = ?  ', $customerId)
            ->where('cb.is_notify = ?  ', 1);

        if ($row = $this->getConnection()->fetchRow($select)) {
            return $row['id'];
        }
        return false;
    }
}
