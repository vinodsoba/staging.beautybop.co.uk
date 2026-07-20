<?php

declare(strict_types=1);

namespace BeautyBop\Core\Console;

use BeautyBop\Core\Model\Seo\CategoryStructureValidator;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeoValidate extends Command
{
    private CategoryStructureValidator $validator;

    public function __construct(
        CategoryStructureValidator $validator
    ) {
        $this->validator = $validator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('beautybop:seo:validate')
            ->setDescription('Validate exported category structure.');

        parent::configure();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {

        $output->writeln('');
        $output->writeln('<info>BeautyBop SEO Validator</info>');
        $output->writeln(str_repeat('-', 50));

        $file = BP . '/var/export/seo-sync.json';

        if (!file_exists($file)) {

            $output->writeln('<error>seo-sync.json not found.</error>');

            return Cli::RETURN_FAILURE;
        }

        $json = json_decode(
            file_get_contents($file),
            true
        );

        if (!isset($json['categories'])) {

            $output->writeln('<error>No categories found in export.</error>');

            return Cli::RETURN_FAILURE;
        }

        $report = $this->validator->validate(
            $json['categories']
        );

        $output->writeln('');
        $output->writeln(sprintf(
            'Categories Checked : %d',
            $report['total']
        ));

        $output->writeln(sprintf(
            'Found              : %d',
            $report['found']
        ));

        $output->writeln(sprintf(
            'Missing            : %d',
            count($report['missing'])
        ));

        if (!$report['valid']) {

            $output->writeln('');
            $output->writeln('<comment>Missing Categories:</comment>');

            foreach ($report['missing'] as $missing) {

                $output->writeln(
                    ' - ' . implode(' > ', $missing['path'])
                );
            }

            return Cli::RETURN_FAILURE;
        }

        $output->writeln('');
        $output->writeln('<info>Validation PASSED.</info>');

        return Cli::RETURN_SUCCESS;
    }
}