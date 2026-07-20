<?php

declare(strict_types=1);

namespace BeautyBop\Core\Console;

use BeautyBop\Core\Model\Seo\CategoryCreator;
use BeautyBop\Core\Model\Seo\CategoryStructureValidator;
use Magento\Framework\App\State;
use Magento\Framework\Filesystem\DirectoryList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeoCreate extends Command
{
    private State $appState;
    private DirectoryList $directoryList;
    private CategoryStructureValidator $validator;
    private CategoryCreator $creator;

    public function __construct(
        State $appState,
        DirectoryList $directoryList,
        CategoryStructureValidator $validator,
        CategoryCreator $creator
    ) {
        parent::__construct();

        $this->appState = $appState;
        $this->directoryList = $directoryList;
        $this->validator = $validator;
        $this->creator = $creator;
    }

    protected function configure(): void
    {
        $this->setName('beautybop:seo:create');
        $this->setDescription('Dry run category creation from SEO export.');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {

        $this->appState->setAreaCode('adminhtml');

        $output->writeln('');
        $output->writeln('<info>BeautyBop SEO Category Creator</info>');
        $output->writeln(str_repeat('-', 50));
        $output->writeln('');

        $file = $this->directoryList->getPath('var')
            . '/export/seo-sync.json';

        if (!file_exists($file)) {
            $output->writeln('<error>seo-sync.json not found.</error>');
            return Command::FAILURE;
        }

        $json = json_decode(
            file_get_contents($file),
            true
        );

        if (!isset($json['categories'])) {
            $output->writeln('<error>No categories found in export.</error>');
            return Command::FAILURE;
        }

        $report = $this->validator->validate(
            $json['categories']
        );

        $missing = $report['missing'];

        $output->writeln(
            sprintf(
                'Missing Categories: %d',
                count($missing)
            )
        );

        $output->writeln('');

        $created = $this->creator->create($missing);

        if (empty($created)) {
            $output->writeln('<info>No categories require creation.</info>');
        } else {

            $output->writeln('<comment>DRY RUN</comment>');
            $output->writeln('');

            foreach ($created as $category) {

                    $output->writeln(
                    sprintf(
                        'Would create: %s',
                        implode(' > ', $category['path'])
                    )
                );

                $output->writeln('  URL Key : ' . $category['url_key']);
                $output->writeln('  Active  : ' . ($category['is_active'] ? 'Yes' : 'No'));
                $output->writeln('  Menu    : ' . ($category['include_in_menu'] ? 'Yes' : 'No'));
                $output->writeln('  Position: ' . $category['position']);
                $output->writeln('');
            }
        }

        $output->writeln('');
        $output->writeln('<info>No changes have been made.</info>');
        $output->writeln('');

        return Command::SUCCESS;
    }
}