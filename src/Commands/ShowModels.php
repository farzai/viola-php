<?php

namespace Farzai\Viola\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowModels extends Command
{
    protected static $defaultName = 'models';

    protected function configure()
    {
        $this
            ->setDescription('Show all models available in the ChatGPT');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        return Command::SUCCESS;
    }
}
