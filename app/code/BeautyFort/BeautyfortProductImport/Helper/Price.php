<?php
namespace BeautyFort\BeautyfortProductImport\Helper;

class Price
{
    public function calculatePrice(float $supplierCostExVat): float
    {
        $shipping = 4.00;
        $vatRate = 0.20;
        $discountRate = 0.10; // 10%
        $profitMargin = 0.35; // 25%

        // Step 1: Cost + shipping
        $subtotal = $supplierCostExVat + $shipping;

        // Step 2: VAT
        $vat = $subtotal * $vatRate;

        // Step 3: Landed cost
        $landedCost = $subtotal + $vat;

        // Step 4: Discount
        $discounted = $landedCost * (1 - $discountRate);

        // Step 5: Profit (ADD, don’t multiply)
        $profit = $discounted * $profitMargin;

        return round($discounted + $profit, 2);
    }


}
