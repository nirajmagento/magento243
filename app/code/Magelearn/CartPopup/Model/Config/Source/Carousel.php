<?php

namespace Magelearn\CartPopup\Model\Config\Source;

class Carousel implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '_bestSellerProducts', 'label' => __('Best Sellers')],
            ['value' => '_latestProducts', 'label' => __('Latest Products')],
            ['value' => '_randomProducts', 'label' => __('Random Products')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            '_bestSellerProducts' => __('Best Sellers'),
            '_latestProducts' => __('Latest Products'),
            '_randomProducts' => __('Random Products')
        ];
    }
}