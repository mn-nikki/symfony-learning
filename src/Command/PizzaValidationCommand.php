<?php declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PizzaValidationCommand extends Command
{
    protected static $defaultName = 'app:pizza:validation';

    protected function configure(): void
    {
        $this
            ->setDescription('Pizza name validator')
            ->addArgument('pizzaName', InputArgument::OPTIONAL, 'Name for pizza')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        return 0;
    }
}
