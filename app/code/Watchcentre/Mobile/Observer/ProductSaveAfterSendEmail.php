<?php declare(strict_types=1);

namespace Watchcentre\Mobile\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Watchcentre\Mobile\Model\ResourceModel\StockNotify as StockNotifyResource;
use Watchcentre\Mobile\Model\ResourceModel\StockNotify\CollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Watchcentre\Mobile\Model\StockNotifyFactory;

class ProductSaveAfterSendEmail implements ObserverInterface
{
    /**
     * @var StockNotifyFactory
     */
    private $stockNotify;

    /**
     * @var CollectionFactory
     */
    private  $collection;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var StockNotifyResource
     */
    private $stockNotifyResource;

    /**
     * @param CollectionFactory $collection
     * @param ResourceConnection $resourceConnection
     * @param StockNotifyFactory $stockNotify
     * @param StockNotifyResource $stockNotifyResource
     */
    public function __construct(
        CollectionFactory $collection,
        ResourceConnection $resourceConnection,
        StockNotifyFactory         $stockNotify,
        StockNotifyResource $stockNotifyResource
    )
    {
        $this->collection = $collection;
        $this->resourceConnection = $resourceConnection;
        $this->stockNotify = $stockNotify;
        $this->stockNotifyResource = $stockNotifyResource;
    }

    public function execute(Observer $observer): void
    {
        /** Test Email Start*/
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerRepository = $objectManager->create(\Magento\Customer\Api\CustomerRepositoryInterface::class);
        $productRepository = $objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $scopeConfig = $objectManager->create(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $storeManager = $objectManager->create(\Magento\Store\Model\StoreManager::class);
        $inlineTranslation = $objectManager->create(\Magento\Framework\Translate\Inline\StateInterface::class);
        $transportBuilder = $objectManager->create(\Magento\Framework\Mail\Template\TransportBuilder::class);

        $connection = $this->resourceConnection->getConnection();

        $product_stock_notify_cron_table = $connection->getTableName('watchcenter_mobile_product_stock_notify_cron_email');

        $select = $connection->select()
            ->from(['cb1' => $product_stock_notify_cron_table])
            ->where('cb1.is_send = ? ', 0)
            ->limit(100);

        $cronData = $connection->fetchAll($select);

        foreach ($cronData as $cron){
            $customerModel = $customerRepository->getById($cron['customer_id']);

            $productModel = $productRepository->getById($cron['product_id']);

            $customerEmail = $customerModel->getEmail();

            /** Email Send Start*/

            $sentToEmail = $scopeConfig->getValue('trans_email/ident_general/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            $sentToName = $scopeConfig->getValue('trans_email/ident_general/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            $storeId = $storeManager->getStore()->getId();
            $name = $customerModel->getFirstname();

            $inlineTranslation->suspend();
            try {
                $templateVars = array(
                    'store' => $storeManager->getStore(),
                    'customer_name' => $name,
                    'email_id' => $customerEmail,
                    'product' => $productModel,
                    'product_url' => $productModel->getProductUrl()
                );

                $sender = [
                    'name' => $sentToName,
                    'email' => $customerEmail,
                ];

                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $transport = $transportBuilder
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
                $inlineTranslation->resume();
            } catch (\Exception $e) {
                $inlineTranslation->resume();
            }

            /** Email Send End*/
            $query23 = "DELETE from `watchcenter_mobile_product_stock_notify_cron_email` where id = '".$cron['id']."'";
            $connection->query($query23);
        }
        /** Test Email Start*/

        $product = $observer->getProduct();
        $productId = $product->getId();

        $origStockData = $product->getOrigData('quantity_and_stock_status');
        $origQty = $origStockData['qty'];
        $origIsInStock = $origStockData['is_in_stock'];
        $newStockData = $product->getStockData();
        $newQty = (float)$newStockData['qty'];
        $newIsInStock = (bool)$newStockData['is_in_stock'];
        $hasChangedFor = $product->dataHasChangedFor('quantity_and_stock_status');

        if($origQty != $newQty || $origIsInStock != $newIsInStock){

            if(!$origIsInStock || $origQty <= 0) {

                if ($newQty && $newIsInStock > 0) {

                    $notifyData = $this->collection->create()->addFieldToFilter('product_id', $productId)->addFieldToFilter('is_notify', 0);

                    if($notifyData->getSize()) {

                        foreach ($notifyData->getData() as $notify) {

                            $stockNotifyId = $notify['id'];

                            $connection = $this->resourceConnection->getConnection();

                            $product_stock_notify_cron_table = $connection->getTableName('watchcenter_mobile_product_stock_notify_cron_email');

                            $query2 = "INSERT INTO `" . $product_stock_notify_cron_table . "`(`product_id`, `customer_id`) VALUES ('" . $notify['product_id'] . "', '" . $notify['customer_id'] . "')";
                            $connection->query($query2);

                            $stockNotifyModel = $this->stockNotify->create();
                            $stockNotifyModel->load($stockNotifyId);
                            $stockNotifyModel->setIsNotify(true);
                            $this->stockNotifyResource->save($stockNotifyModel);

                        }

                    }
                }
            }
        }
    }
}
