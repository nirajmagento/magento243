<?php

namespace Magelearn\CartPopup\Model\Config\Source;

class ProductLimit implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 4, 'label' => 4],
            ['value' => 6, 'label' => 6],
            ['value' => 8, 'label' => 8],
            ['value' => 10, 'label' => 10],
            ['value' => 12, 'label' => 12]
            
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
            4 => 4,
            6 => 6,
            8 => 8,
            10 => 10,
            12 => 12
        ];
    }
}