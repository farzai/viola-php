<?php

namespace Farzai\Viola\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Farzai\Viola\Viola;

class AskQuestion extends Command
{
    protected static $defaultName = 'ask';

    private Viola $viola;

    public function __construct()
    {
        parent::__construct();

        $this->viola = new Viola();
    }

    protected function configure()
    {
        $this
            ->setDescription('Ask a question to the ChatGPT')
            ->addArgument('question', InputArgument::REQUIRED, 'The question to ask');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $question = $input->getArgument('question');
        
        // if ($this->viola->getTokenStore()->getToken() === null) {
        //     $accessToken = $this->ask(' API Token', $input, $output);

        //     if ($accessToken === null) {
        //         $output->writeln('<error>API Token is required.</error>');
        //         return Command::FAILURE;
        //     }

        //     $this->viola->getTokenStore()->setToken($accessToken);
        // }
        $this->ensureAccessTokenIsValid($input, $output);

        return Command::SUCCESS;
    }

    /**
     * Ensure the access token is valid.
     *
     * @return void
     */
    private function ensureAccessTokenIsValid(InputInterface $input, OutputInterface $output)
    {
        $currentToken = $this->viola->getTokenStore()->getToken();

        if ($currentToken === null) {
            $accessToken = $this->ask(' API Token', $input, $output);

            if ($accessToken === null) {
                throw new \Exception('API Token is required.');
            }

            $this->viola->getTokenStore()->setToken($accessToken);
        }
    }

    /**
     * Ask a question to the user.
     *
     * @return string
     */
    private function ask(string $question, InputInterface $input, OutputInterface $output)
    {
        /** @var \Symfony\Component\Console\Helper\QuestionHelper $helper */
        $helper = $this->getHelper('question');

        return $helper->ask(
            $input, $output, new Question("Enter {$question}: ")
        );
    }
}