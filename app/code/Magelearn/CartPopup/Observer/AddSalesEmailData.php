<?php
declare(strict_types=1);

namespace Magelearn\CartPopup\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Sales\Model\Order;

class AddSalesEmailData implements ObserverInterface
{
    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var OrderIdentity
     */
    protected $identityContainer;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    protected $storeRepository;

    public function __construct(
        LoggerInterface $logger,
        OrderIdentity $identityContainer,
        StoreRepositoryInterface $storeRepository,
        PaymentHelper $paymentHelper
    )
    {
        $this->_logger = $logger;
        $this->identityContainer = $identityContainer;
        $this->storeRepository = $storeRepository;
        $this->paymentHelper = $paymentHelper;
    }

    public function execute(Observer $observer): void
    {
        try {
            $sender = $observer->getEvent()->getSender();

            $transport = $observer->getEvent()->getTransportObject();
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/customfile.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info('================================');
            $logger->info('Array Log'.print_r($transport['order_data'], true));

//            if ($transport->getOrder() != null)
//            {
//                if ($transport->getOrderData() != null)
//                {
                    $transport['order_data'] =  [
                        'is_not_virtual' => 'test'
                    ];
//                }
//            }
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/customfile.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info('==================1==============');
            $logger->info('Array Log'.print_r($transport['order_data'], true));
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        // if ($transport->getOrder() != null)
        // {
       // echo '<pre>'; print_r($sender);die();
        ////echo '<pre>'; print_r($transport['store']);die();
    }
}
