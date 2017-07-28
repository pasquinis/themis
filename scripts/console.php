#!/usr/bin/env php
<?php
require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Themis\Console\Command\HouseholdBudgetCommand;

$application = new Application();

$application->add(new HouseholdBudgetCommand());

$application->run();
