<?php
declare(strict_types=1);

namespace BeautyBop\Core\Console;

use BeautyBop\Core\Model\Seo\CategorySeoExporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeoExport extends Command
{
    /** @var  CategorySeoExporter */
    private $categorySeoExporter;

    public function __construct(
        CategorySeoExporter $categorySeoExporter
    ) {
        $this->categorySeoExporter = $categorySeoExporter;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('beautybop:seo:export');
        $this->setDescription('Export BeautyBop SEO data.');

        parent::configure();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {

        $result = $this->categorySeoExporter->execute();

        $output->writeln('');
        $output->writeln('<info>BeautyBop SEO Export</info>');
        $output->writeln('-----------------------------');
        $output->writeln('Categories : ' . $result['categories']);
        $output->writeln('File       : ' . $result['file']);
        $output->writeln('');
        $output->writeln('<info>Export Complete</info>');

        return Command::SUCCESS;
    }
}