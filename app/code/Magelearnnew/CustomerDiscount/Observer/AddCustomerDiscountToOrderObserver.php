<?php
declare(strict_types=1);

namespace Magelearnnew\CustomerDiscount\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Magento\Framework\DataObject\Copy;
use phpDocumentor\Reflection\Types\This;


class AddCustomerDiscountToOrderObserver implements ObserverInterface
{
    /**
     * @var Copy
     */
    protected $objectCopyService;

    /**
     * @param Copy $objectCopyService
     */
    public function __construct(
        Copy $objectCopyService
    ) {
        $this->objectCopyService = $objectCopyService;
    }

    public function execute(Observer $observer): void
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getData('order');
        /** @var Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        $this->objectCopyService->copyFieldsetToTarget('sales_convert_quote', 'to_order', $quote, $order);

        $customerDiscount = $quote->getCustomerDiscount();
        if (!$customerDiscount) {
            return ;
        }
        //Set customer discount data to order
        $order->setData('customer_discount', $customerDiscount);

    }
}
