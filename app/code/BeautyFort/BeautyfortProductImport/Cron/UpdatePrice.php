<?php

namespace BeautyFort\BeautyfortProductImport\Cron;

use BeautyFort\BeautyfortProductImport\Model\PriceUpdater;

class UpdatePrice
{
    /** @var PriceUpdater */
    private $priceUpdater;

    public function __construct(
        PriceUpdater $priceUpdater
    ) {
        $this->priceUpdater = $priceUpdater;
    }

    public function execute(): void
    {
        $this->priceUpdater->execute();
    }
}
