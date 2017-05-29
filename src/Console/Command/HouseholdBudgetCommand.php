<?php
namespace Themis\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HouseholdBudgetCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('projection:household-budget')
            ->setDescription('Execute the projection for household-budget view')
            ->setHelp('This command allows you to project the projection')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);
    }
}
