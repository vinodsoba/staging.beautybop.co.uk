<?php
declare(strict_types=1);

namespace BeautyBop\Core\Block\Product\View;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class Rrp extends Template
{
    private Registry $registry;
    private PriceHelper $priceHelper;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        PriceHelper $priceHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registry = $registry;
        $this->priceHelper = $priceHelper;
    }

    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getRrp(): float
    {
        return (float)$this->getProduct()->getData('beautyfort_rrp');
    }

    public function getPrice(): float
    {
        return (float)$this->getProduct()->getFinalPrice();
    }

    public function hasRrp(): bool
    {
        return $this->getRrp() > $this->getPrice();
    }

    public function getSavingAmount(): float
    {
        return $this->getRrp() - $this->getPrice();
    }

    public function getSavingPercentage(): int
    {
        if (!$this->hasRrp()) {
            return 0;
        }

        return (int)round(
            (($this->getRrp() - $this->getPrice()) / $this->getRrp()) * 100
        );
    }

    public function formatPrice(float $price): string
    {
        return $this->priceHelper->currency($price, true, false);
    }
}