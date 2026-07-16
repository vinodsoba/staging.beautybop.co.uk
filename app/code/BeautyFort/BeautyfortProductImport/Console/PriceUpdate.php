<?php

namespace BeautyFort\BeautyfortProductImport\Console;

use BeautyFort\BeautyfortProductImport\Model\PriceUpdater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PriceUpdate extends Command
{
    /** @var PriceUpdater */
    private $priceUpdater;

    public function __construct(
        PriceUpdater $priceUpdater
    ) {
        $this->priceUpdater = $priceUpdater;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('beautyfort:price:update');
        $this->setDescription('Run the BeautyFort price updater');

        parent::configure();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {

        $output->writeln('<info>Starting BeautyFort Price Update...</info>');
        $output->writeln('');

        $this->priceUpdater->execute();

        $output->writeln('');
        $output->writeln('<info>✓ BeautyFort price update completed successfully.</info>');

        return Command::SUCCESS;
    }
}