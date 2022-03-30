<?php

namespace Watchcentre\Mobile\Cron;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManager;

class SendNotifyEmail
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var ScopeConfigInterface
     */
    private  $scopeConfig;

    /**
     * @var StoreManager
     */
    private  $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    private  $productRepository;

    /**
     * @param ResourceConnection $resourceConnection
     * @param CustomerRepositoryInterface $customerRepository
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManager $storeManager
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        CustomerRepositoryInterface $customerRepository,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $scopeConfig,
        StoreManager $storeManager,
        ProductRepositoryInterface $productRepository
    )
    {
        $this->resourceConnection = $resourceConnection;
        $this->customerRepository = $customerRepository;
        $this->transportBuilder  = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
    }

    public function execute()
    {
        $connection = $this->resourceConnection->getConnection();

        $product_stock_notify_cron_table = $connection->getTableName('watchcenter_mobile_product_stock_notify_cron_email');

        $select = $connection->select()
            ->from(['cb1' => $product_stock_notify_cron_table])
            ->where('cb1.is_send = ? ', 0)
            ->limit(100);

        $cronData = $connection->fetchAll($select);

        foreach ($cronData as $cron){
            /** @var Customer $customerModel */
            $customerModel = $this->customerRepository->getById($cron['customer_id']);

            /** @var Product $productModel */
            $productModel = $this->productRepository->getById($cron['product_id']);

            $customerEmail = $customerModel->getEmail();

            /** Email Send Start*/

            $sentToEmail = $this->scopeConfig->getValue('trans_email/ident_general/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            $sentToName = $this->scopeConfig->getValue('trans_email/ident_general/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            $storeId = $this->storeManager->getStore()->getId();
            $name = $customerModel->getFirstname();

            $this->inlineTranslation->suspend();
            try {
                $templateVars = array(
                    'customer_name' => $name,
                    'email_id' => $customerEmail,
                    'product' => $productModel,
                    'product_url' => $productModel->getUrl()
                );

                $sender = [
                    'name' => $sentToName,
                    'email' => $customerEmail,
                ];

                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $transport = $this->transportBuilder
                    ->setTemplateIdentifier('watchcenter_mobile_product_stock_notify_email_template')
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $storeId,
                        ]
                    )
                    ->setTemplateVars($templateVars)
                    ->setFrom($sender)
                    ->addTo($customerEmail,$name)
                    ->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            } catch (\Exception $e) {
                $this->inlineTranslation->resume();
            }
            /** Email Send End*/

            $query23 = "DELETE from `watchcenter_mobile_product_stock_notify_cron_email` where id = '".$cron['id']."'";
            $connection->query($query23);
        }

    }

}
