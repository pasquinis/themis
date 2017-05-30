<?php
namespace Themis\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Themis\Application;
use Themis\Projection\CategoryExpenditureItems;
use Themis\Projection\HouseholdBudget;

class HouseholdBudgetCommand extends Command
{
    private $expenditureItems;
    private $budget;

    protected function configure()
    {
        $this
            ->setName('projection:household-budget')
            ->setDescription('Execute the projection for household-budget view')
            ->setHelp('This command allows you to project the projection')
            ->addArgument('from', InputArgument::REQUIRED, 'The start date, E.g. YYYY-MM-DD')
            ->addArgument('to', InputArgument::REQUIRED, 'The end date, E.g. YYYY-MM-DD')
        ;

        $this->expenditureItems = new CategoryExpenditureItems();
        $this->budget = new HouseholdBudget(new Application, $this->expenditureItems);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $from = $input->getArgument('from');
        $to = $input->getArgument('to');

        $this->budget->handle(
            $startDate = '01/01/2011', //TODO custom
            $endDate = '31/01/2011'
        );

        $values = [
            'event-streamed' => 10
        ];

        $output->writeln([
            (new \ReflectionClass(HouseholdBudgetCommand::class))->getShortName(),
            "from: {$from} to: {$to}",
            '============',
            var_export($values, true),
        ]);
    }
}
