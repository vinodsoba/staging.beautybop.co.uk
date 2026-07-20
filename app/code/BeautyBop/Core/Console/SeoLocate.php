<?php

declare(strict_types=1);

namespace BeautyBop\Core\Console;

use BeautyBop\Core\Model\Seo\CategoryLocator;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeoLocate extends Command
{
    /**
     * @var CategoryLocator
     */
    private CategoryLocator $categoryLocator;

    public function __construct(
        CategoryLocator $categoryLocator
    ) {
        $this->categoryLocator = $categoryLocator;

        parent::__construct();
    }

    /**
     * Configure command.
     */
    protected function configure(): void
    {
        $this->setName('beautybop:seo:locate')
            ->setDescription('Locate a category using its exported path.');

        parent::configure();
    }

    /**
     * Execute command.
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {

        $output->writeln('');
        $output->writeln('<info>BeautyBop SEO Category Locator</info>');
        $output->writeln(str_repeat('-', 50));

        /**
         * Temporary test path.
         */
        $path = [
            'Brands',
            'Burberry Fragrance',
            'My Test Category'
        ];

        $output->writeln('');
        $output->writeln('Testing path:');
        $output->writeln('  ' . implode(' > ', $path));
        $output->writeln('');

        /**
         * Test 1
         * Standard path lookup.
         */
        $category = $this->categoryLocator->findByPath($path);

        if ($category) {

            $output->writeln('<info>findByPath()</info>');

            $output->writeln(sprintf(
                'Category: %s (ID %d)',
                $category->getName(),
                $category->getId()
            ));

        } else {

            $output->writeln('<comment>findByPath(): Category not found.</comment>');
        }

        $output->writeln('');

        /**
         * Test 2
         * Deepest existing path.
         */
        $analysis = $this->categoryLocator->findDeepestExistingPath($path);

        $output->writeln('<info>findDeepestExistingPath()</info>');
        $output->writeln('');

        $output->writeln(sprintf(
            'Deepest Parent : %s',
            $analysis['parent']
                ? $analysis['parent']->getName()
                : 'None'
        ));

        $output->writeln(sprintf(
            'Complete Path  : %s',
            $analysis['complete'] ? 'Yes' : 'No'
        ));

        $output->writeln(sprintf(
            'Missing Levels : %s',
            empty($analysis['missing'])
                ? 'None'
                : implode(' > ', $analysis['missing'])
        ));

        $output->writeln('');

        return Cli::RETURN_SUCCESS;
    }
}