<?php

namespace Farzai\Viola\Commands;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

abstract class AbstractCommand extends SymfonyCommand
{
    protected InputInterface $input;

    protected OutputInterface $output;

    abstract protected function handle(): int;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        return $this->handle();
    }

    /**
     * Ask the user a question.
     *
     * @param  string|null  $default
     * @return mixed
     */
    protected function ask(string $question, $default = null)
    {
        $helper = $this->getQuestionHelper();

        $question = new Question($question, $default);

        return $helper->ask($this->input, $this->output, $question);
    }

    /**
     * Ask the user a question with a hidden answer.
     *
     * @param  string|null  $default
     * @return mixed
     */
    protected function askWithHiddenInput(string $question, $default = null)
    {
        $helper = $this->getQuestionHelper();

        $question = new Question($question, $default);
        $question->setHidden(true);

        return $helper->ask($this->input, $this->output, $question);
    }

    /**
     * Ask the user a question with a choice.
     *
     * @param  string|null  $default
     * @return mixed
     */
    protected function choice(string $question, array $choices, $default = null)
    {
        $helper = $this->getQuestionHelper();

        $question = new ChoiceQuestion($question, $choices, $default);

        return $helper->ask($this->input, $this->output, $question);
    }

    /**
     * Ask the user a confirmation question.
     *
     * @return mixed
     */
    protected function confirm(string $question, bool $default = true)
    {
        $helper = $this->getQuestionHelper();

        $question .= '<comment>'.($default ? ' [Y/n]' : ' [y/N]').'</comment> ';

        $confirmation = new ConfirmationQuestion($question, $default);

        return $helper->ask($this->input, $this->output, $confirmation);
    }

    /**
     * Write a string as standard output.
     *
     * @return void
     */
    protected function write(string $string)
    {
        $this->output->write($string);
    }

    /**
     * Write a string as information output.
     *
     * @return void
     */
    protected function info(string $string)
    {
        $this->output->writeln("<info>{$string}</info>");
    }

    /**
     * Write a string as success output.
     *
     * @return void
     */
    protected function success(string $string)
    {
        $this->output->writeln("<info>{$string}</info>");
    }

    /**
     * Write a string as error output.
     *
     * @return void
     */
    protected function error(string $string)
    {
        $this->output->writeln("<error>{$string}</error>");
    }

    /**
     * Write a string as comment output.
     *
     * @return void
     */
    protected function comment(string $string)
    {
        $this->output->writeln("<comment>{$string}</comment>");
    }

    /**
     * Write a string as question output.
     *
     * @return void
     */
    protected function question(string $string)
    {
        $this->output->writeln("<question>{$string}</question>");
    }

    /**
     * Get the Symfony question helper.
     *
     * @return \Symfony\Component\Console\Helper\QuestionHelper
     */
    private function getQuestionHelper()
    {
        return $this->getHelper('question');
    }

    /**
     * Display the given table as a table.
     *
     * @return void
     */
    protected function displayAsTable(array $items)
    {
        if (count($items) === 0) {
            $this->info('No items to display.');

            return;
        }

        $table = new Table($this->output);

        $table
            ->setHeaders(array_keys($items[0]))
            ->setRows($items);

        $table->render();
    }
}
