<?php

namespace Jevy\CheckoutremoveRegion\Block;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

use Magento\Checkout\Block\Checkout\LayoutProcessor as origionalLayoutProcessor;

class LayoutProcessorMain implements LayoutProcessorInterface
{
    private $paymentModelConfig;

    public function __construct(
        \Magento\Payment\Model\Config $paymentModelConfig
    )
    {
        $this->paymentModelConfig = $paymentModelConfig;
    }


//    public function afterProcess(
//        origionalLayoutProcessor $subject,
//        array                    $jsLayout
//    )
//    {
////        foreach ($jsLayout['components']['checkout']['children']
////                 ['steps']['children']
////                 ['billing-step']['children']
////                 ['payment']['children']
////                 ['payments-list']['children'] as &$paymentMethod) {
////            $fields = &$paymentMethod['children']['form-fields']['children'];
////            if ($fields === null) {
////                continue;
////            }
////
////            if (isset($fields['region'])) {
////                $fields['region']['visible'] = false;
////            }
////
//////            if (isset($fields['region_id'])) {
//////                $fields['region_id']['visible'] = false;
//////            }
////
////        }
//        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/customfile.log');
//        $logger = new \Zend_Log();
//        $logger->addWriter($writer);
//        $logger->info('========================');
//        $logger->info('Array Log'.print_r($jsLayout, true));
//
//        unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
//            ['shippingAddress']['children']['shipping-address-fieldset']['children']['region']);
////
////        $logger->info('========================');
////        $logger->info('Array Log'.print_r($jsLayout, true));
//
//        $activePayments = $this->paymentModelConfig->getActiveMethods();
//        /* For Disable company field from checkout billing form */
//        if (count($activePayments)) {
//            foreach ($activePayments as $paymentCode => $payment) {
////                $jsLayout['components']['checkout']['children']['steps']['children']
////                ['billing-step']['children']['payment']['children']
////                ['payments-list']['children'][$paymentCode.'-form']['children']
////                ['form-fields']['children']['region'] = [
////                    'visible' => false
////                ];
//
//
//
//
//                unset($jsLayout['components']['checkout']['children']['steps']['children']
//                    ['billing-step']['children']['payment']['children']
//                    ['payments-list']['children'][$paymentCode . '-form']['children']
//                    ['form-fields']['children']['region']);
//                //echo print_r($jsLayout); die();
//            }
//        }
//
////        return $jsLayout;
//        return $jsLayout;
//    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['region']);
        unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']);

        foreach ($jsLayout['components']['checkout']['children']
                 ['steps']['children']
                 ['billing-step']['children']
                 ['payment']['children']
                 ['payments-list']['children'] as &$paymentMethod) {
            $fields = &$paymentMethod['children']['form-fields']['children'];
            if ($fields === null) {
                continue;
            }
            unset($fields['region']);
        }

        return $jsLayout;
    }
}
