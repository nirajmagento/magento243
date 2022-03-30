<?php
namespace Tridhyatech\ChangeTaxCalculation\Model\Calculation;

use Magento\Tax\Api\Data\QuoteDetailsItemInterface;

class RowBaseCalculator extends \Magento\Tax\Model\Calculation\RowBaseCalculator
{

    /**
     * @inheritdoc
     */
    protected function calculateWithTaxNotInPrice(QuoteDetailsItemInterface $item, $quantity, $round = true)
    {
        $taxRateRequest = $this->getAddressRateRequest()->setProductClassId(
            $this->taxClassManagement->getTaxClassId($item->getTaxClassKey())
        );
        $rate = $this->calculationTool->getRate($taxRateRequest);
        $appliedRates = $this->calculationTool->getAppliedRates($taxRateRequest);

        $applyTaxAfterDiscount = $this->config->applyTaxAfterDiscount($this->storeId);
        $discountAmount = $item->getDiscountAmount();
        $discountTaxCompensationAmount = 0;

        // Calculate $rowTotal
        $price = $this->calculationTool->round($item->getUnitPrice());
        $rowTotal = $price * $quantity;
        $rowTaxes = [];
        $rowTaxesBeforeDiscount = [];
        $appliedTaxes = [];
        $rowTotalForTaxCalculation = $this->getPriceForTaxCalculation($item, $price) * $quantity;
        //Apply each tax rate separately
        foreach ($appliedRates as $appliedRate) {
            $taxId = $appliedRate['id'];
            $taxRate = $appliedRate['percent'];
            $rowTaxPerRate = $this->calculationTool->calcTaxAmount($rowTotalForTaxCalculation, $taxRate, false, false);
            $deltaRoundingType = self::KEY_REGULAR_DELTA_ROUNDING;
            if ($applyTaxAfterDiscount) {
                $deltaRoundingType = self::KEY_TAX_BEFORE_DISCOUNT_DELTA_ROUNDING;
            }
            $rowTaxPerRate = $this->roundAmount($rowTaxPerRate, $taxId, false, $deltaRoundingType, $round, $item);
            $rowTaxAfterDiscount = $rowTaxPerRate;

            //Handle discount
            if ($applyTaxAfterDiscount) {
                //TODO: handle originalDiscountAmount
                $taxableAmount = max($rowTotalForTaxCalculation - $discountAmount, 0);
                if ($taxableAmount && !$applyTaxAfterDiscount) {
                    $taxableAmount = $rowTotalForTaxCalculation;
                }
                $rowTaxAfterDiscount = $this->calculationTool->calcTaxAmount(
                    $taxableAmount,
                    $taxRate,
                    false,
                    false
                );
                $rowTaxAfterDiscount = $this->roundAmount(
                    $rowTaxAfterDiscount,
                    $taxId,
                    false,
                    self::KEY_REGULAR_DELTA_ROUNDING,
                    false,
                    $item
                );
            }
            $appliedTaxes[$taxId] = $this->getAppliedTax(
                $rowTaxAfterDiscount,
                $appliedRate
            );

            $rowTaxes[] = $rowTaxAfterDiscount;
            $rowTaxesBeforeDiscount[] = $rowTaxPerRate;
        }
        $rowTax = array_sum($rowTaxes);
        $rowTaxBeforeDiscount = array_sum($rowTaxesBeforeDiscount);
        $rowTotalInclTax = $rowTotal + $rowTaxBeforeDiscount;
        $priceInclTax = $rowTotalInclTax / $quantity;

        if ($round) {
            $priceInclTax = $this->calculationTool->round($priceInclTax);
        }

        return $this->taxDetailsItemDataObjectFactory->create()
            ->setCode($item->getCode())
            ->setType($item->getType())
            ->setRowTax($rowTax)
            ->setPrice($price)
            ->setPriceInclTax($priceInclTax)
            ->setRowTotal($rowTotal)
            ->setRowTotalInclTax($rowTotalInclTax)
            ->setDiscountTaxCompensationAmount($discountTaxCompensationAmount)
            ->setAssociatedItemCode($item->getAssociatedItemCode())
            ->setTaxPercent($rate)
            ->setAppliedTaxes($appliedTaxes);
    }

    /**
     * Get price for tax calculation.
     *
     * @param QuoteDetailsItemInterface $item
     * @param float $price
     * @return float
     */
    private function getPriceForTaxCalculation(QuoteDetailsItemInterface $item, float $price)
    {
        if ($item->getExtensionAttributes() && $item->getExtensionAttributes()->getPriceForTaxCalculation()) {
            $priceForTaxCalculation = $this->calculationTool->round(
                $item->getExtensionAttributes()->getPriceForTaxCalculation()
            );
        } else {
            $priceForTaxCalculation = $price;
        }

        return $priceForTaxCalculation;
    }

}
