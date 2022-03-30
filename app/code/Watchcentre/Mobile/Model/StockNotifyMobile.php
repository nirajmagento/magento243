<?php

namespace Watchcentre\Mobile\Model;

use Watchcentre\Mobile\Api\StockNotifyInterface;
use Watchcentre\Mobile\Model\StockNotify;
use Watchcentre\Mobile\Model\ResourceModel\StockNotify as StockNotifyResource;

class StockNotifyMobile implements StockNotifyInterface
{
    /**
     * @var StockNotifyFactory
     */
    private $stockNotify;

    /**
     * @var StockNotifyResource
     */
    private $stockNotifyResource;

    /**
     * @param StockNotifyFactory $stockNotify
     * @param StockNotifyResource $stockNotifyResource
     */
    public function __construct(
        StockNotifyFactory         $stockNotify,
        StockNotifyResource $stockNotifyResource
    )
    {
        $this->stockNotify = $stockNotify;
        $this->stockNotifyResource = $stockNotifyResource;
    }

    /**
     * @param $productId
     * @param $customerId
     * @return array|StockNotifyInterface[]
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function post($productId, $customerId)
    {
        if ($productId && $customerId) {

            $isUniqueProductCustomer = $this->stockNotifyResource->getIsUniqueProductCustomer($productId, $customerId);

            if($isUniqueProductCustomer){

                $isAlreadyNotifyProductCustomer = $this->stockNotifyResource->getIsAlreadyNotifyProductCustomer($productId, $customerId);

                if(!$isAlreadyNotifyProductCustomer){
                    //Update is_notify
                    $stockNotifyId = $this->stockNotifyResource->getAlreadyNotifyProductCustomerId($productId, $customerId);
                    $stockNotifyModel = $this->stockNotify->create();
                    $stockNotifyModel->load($stockNotifyId);
                    $stockNotifyModel->setIsNotify(false);
                    $this->stockNotifyResource->save($stockNotifyModel);
                    $response['success'] = true;
                    $response['message'] = 'Data inserted successfully';
                }else{
                    //Insert new one
                    $stockNotifyModel = $this->stockNotify->create();
                    $stockNotifyModel->setData('customer_id', $customerId);
                    $stockNotifyModel->setData('product_id', $productId);
                    $stockNotifyModel->setIsNotify(false);
                    $this->stockNotifyResource->save($stockNotifyModel);
                    $response['success'] = true;
                    $response['message'] = 'Data inserted successfully';
                }

            }else{
                $response['success'] = false;
                $response['message'] = 'Data already inserted';
            }

        } else {
            $response['success'] = false;
            $response['message'] = "please enter product id and customer id";
        }

        $newData[] = $response;

        return $newData;

    }
}
