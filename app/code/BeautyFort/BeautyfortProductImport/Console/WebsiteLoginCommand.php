<?php

declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Console;

use BeautyFort\BeautyfortProductImport\Model\HighResImageService;

use BeautyFort\BeautyfortProductImport\Model\WebsiteLogin;
use BeautyFort\BeautyfortProductImport\Model\WebsiteSearch;
use BeautyFort\BeautyfortProductImport\Model\PreviewParser;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebsiteLoginCommand extends Command
{
    /**
     * @var WebsiteLogin
     */
    private $websiteLogin;

    /**
     * @var HighResImageService
     */
    private $highResImageService;

    public function __construct(
        WebsiteLogin $websiteLogin,
        HighResImageService $highResImageService
    ) {
        $this->websiteLogin = $websiteLogin;
        $this->highResImageService = $highResImageService;


        parent::__construct();  
     
    }

    protected function configure()
    {
        $this->setName('beautyfort:image:login');
        $this->setDescription('Test BeautyFort website login');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {

    $output->writeln('');
    $output->writeln('<info>========================================</info>');
    $output->writeln('<info> BeautyFort High Resolution Image Test </info>');
    $output->writeln('<info>========================================</info>');
    $output->writeln('');

    $sku = 'B406518';

    $output->writeln('Processing SKU B406518...');
    $output->writeln('');

    $imageUrl = $this->highResImageService->getImageUrlForSku($sku);

    if (!$imageUrl) {
        $output->writeln('<error>No high resolution image found.</error>');

        return Command::FAILURE;
    }

    $output->writeln('<info>High Resolution Image URL Found</info>');
    $output->writeln($imageUrl);

    return Command::SUCCESS;

    }
}