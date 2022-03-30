<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Watchcentre\Mobile\Api;

/**
 * @api
 * @since 100.0.2
 */
interface StockNotifyInterface
{
    /**
     * Set notify about out of stock product
     *
     * @param string $productId
     * @param string $customerId
     * @return \Watchcentre\Mobile\Api\StockNotifyInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function post($productId, $customerId);
}
