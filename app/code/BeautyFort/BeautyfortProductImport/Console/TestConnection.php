<?php
namespace BeautyFort\BeautyfortProductImport\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestConnection extends Command
{
    protected function configure()
    {
        $this->setName('beautyfort:test:connection');
        $this->setDescription('Smoke test command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('BeautyFort command loaded');
        return Command::SUCCESS;
    }
}
