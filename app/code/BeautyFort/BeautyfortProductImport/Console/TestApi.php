<?php
namespace BeautyFort\BeautyfortProductImport\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BeautyFort\BeautyfortProductImport\Helper\Api;


class TestApi extends Command
{
    /** @var Api */
    private $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
        parent::__construct();
    }


    protected function configure()
    {
        $this->setName('beautyfort:test:connection');
        $this->setDescription('Smoke test command');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
         $output->writeln('<info>BeautyFort API Test</info>');
        $output->writeln('');

        $output->writeln('Downloading stock file...');

        $stock = $this->api->getStockFile();

        $output->writeln('');

        $output->writeln(
            'Products returned: ' . count($stock)
        );

        $output->writeln('');
        if (empty($stock)) {
            $output->writeln('<error>No products returned.</error>');
            return Command::SUCCESS;
        }

        $output->writeln('<info>First Product:</info>');

        print_r($stock[0]);

        return Command::SUCCESS;
    }
}
