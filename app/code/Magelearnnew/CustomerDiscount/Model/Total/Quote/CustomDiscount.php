<?php

namespace Magelearnnew\CustomerDiscount\Model\Total\Quote;

use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Custom
 * @package Mageplaza\HelloWorld\Model\Total\Quote
 */
class CustomDiscount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;
    private $customerRepository;
    private $customerSession;

    /**
     * Custom constructor.
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        CustomerSession                                   $customerSession
    )
    {
        $this->_priceCurrency = $priceCurrency;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;

    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this|bool
     */
    public function collect(
        \Magento\Quote\Model\Quote                          $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total            $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);

        if ($this->customerSession->isLoggedIn()) {

            $customerId = $this->customerSession->getCustomer()->getId();
            $customer = $this->customerRepository->getById($customerId);
            $baseDiscount = $customer->getCustomAttribute('customer_discount')->getValue();

            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/customfile.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info('===========test=============');
            $logger->info('Array Log' . print_r($baseDiscount, true));

            //echo $baseDiscount; die();
            //$baseDiscount = 10;
            if ($baseDiscount) {
                $discount = $this->_priceCurrency->convert($baseDiscount);
                $total->addTotalAmount('customer_discount', -$discount);
                $total->addBaseTotalAmount('customer_discount', -$baseDiscount);
                $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseDiscount);
                $total->setCustomerDiscount(-$discount);
                $quote->setCustomerDiscount(-$discount);
            }
        }
        return $this;
    }

    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        if ($this->customerSession->isLoggedIn()) {

            $customerId = $this->customerSession->getCustomer()->getId();
            $customer = $this->customerRepository->getById($customerId);
            $baseDiscount = $customer->getCustomAttribute('customer_discount')->getValue();

//            if ($baseDiscount != null || $baseDiscount != '') {
//                $baseDiscount = 0.00;
//            }
            return [
                'code' => 'customer_discount',
                'title' => 'Customer Discount',
                'value' => -$baseDiscount
            ];
        }
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Customer Discount');
    }
}
