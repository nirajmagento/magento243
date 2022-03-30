<?php

namespace Magelearn\CartPopup\Block;

use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Element\Template;
use Magelearn\CartPopup\Helper\Data as Helper;

class Popup extends Template
{
    const CONFIG_CAROUSEL_AUTOPLAY = 'cartpopup/settings/carousel_autoplay';
    
    /** @var AssetRepository  */
    protected $assetRepository;
    
    /**
     * @var Helper
     */
    protected $helper;
    
    /**
     * Popup constructor.
     * @param Template\Context $context
     * @param AssetRepository $assetRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        AssetRepository $assetRepository,
        Helper $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->assetRepository = $assetRepository;
        $this->helper = $helper;
    }

    /**
     * Get shopping cart url
     * @return string
     */
    public function getShoppingCartUrl()
    {
        return $this->getUrl('checkout/cart');
    }

    /**
     * Returns a message to be displayed on the cart popup
     * @return string
     */
    public function getCartMessage()
    {
        $message  = __('A new item has been added to your Shopping Cart. ');
        $message .= __('You now have %s items in your Shopping Cart.');

        return sprintf($message, "<span id='cart-popup-total-count'></span>");
    }

    /**
     * Returns an icon to be displayed
     * @return string
     */
    public function getSuccessIcon()
    {
        return $this->assetRepository->getUrl('Magelearn_CartPopup::images/success_icon.png');
    }
    /**
     * Returns an icon to be displayed
     * @return string
     */
    public function getCarouselAutoPlay()
    {
        return $this->helper->getStoreConfig(self::CONFIG_CAROUSEL_AUTOPLAY);
    }
}