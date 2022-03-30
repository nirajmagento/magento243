<?php

namespace Magelearnnew\CustomerDiscount\Plugin\Checkout\Model;

use Magento\Customer\Model\Session as CustomerSession;

class ShippingInformationManagement
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param CustomerSession $customerSession
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository              $quoteRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        CustomerSession                                   $customerSession
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement   $subject,
                                                                $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        if ($this->customerSession->isLoggedIn()) {

            $customerId = $this->customerSession->getCustomer()->getId();
            $customer = $this->customerRepository->getById($customerId);
            $baseDiscount = $customer->getCustomAttribute('customer_discount')->getValue();
            $quote = $this->quoteRepository->getActive($cartId);
            if ($baseDiscount) {
                $quote->setCustomerDiscount(-$baseDiscount);
            } else {
                $quote->setCustomerDiscount(NULL);
            }
        }
    }
}
