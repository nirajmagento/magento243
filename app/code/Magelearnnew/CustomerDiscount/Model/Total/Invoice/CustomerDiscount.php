<?php

namespace Magelearnnew\CustomerDiscount\Model\Total\Invoice;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class CustomerDiscount extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $invoice->setCustomerDiscount(0.00);

        $amount = $invoice->getOrder()->getCustomerDiscount();
        $invoice->setCustomerDiscount($amount);


        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getCustomerDiscount());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getCustomerDiscount());

        return $this;
    }
}
