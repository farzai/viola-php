<?php

namespace Farzai\Viola\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Farzai\Viola\Viola;

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