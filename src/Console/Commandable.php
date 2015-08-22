<?php

namespace NewUp\Console;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application as ApplicationContract;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

trait Commandable
{

    /**
     * The OutputInterface implementation instance.
     *
     * @var OutputInterface
     */
    protected $output;

    /**
     * The InputInterface implementation instance.
     *
     * @var InputInterface
     */
    protected $input;

    /**
     * The ApplicationContract implementation instance.
     *
     * @var ApplicationContract
     */
    protected $application;

    /**
     * The HelperSet instance.
     * 
     * @var HelperSet
     */
    protected $helperSet;

    /**
     * Sets the ApplicationContract instance.
     *
     * @param ApplicationContract|null $application
     */
    public function setApplication(ApplicationContract $application = null)
    {
        $this->application = $application;
        if ($application) {
            $this->setHelperSet($application->getHelperSet());
        } else {
            $this->helperSet = null;
        }
    }

    /**
     * Sets the HelperSet instance.
     *
     * @param HelperSet $helperSet
     */
    public function setHelperSet(HelperSet $helperSet)
    {
        $this->helperSet = $helperSet;
    }

    /**
     * Gets the HelperSet instance.
     *
     * @return HelperSet
     */
    public function getHelperSet()
    {
        return $this->helperSet;
    }

    /**
     * Sets the OutputInterface instance.
     *
     * @param OutputInterface $outputInterface
     */
    public function setOutputInstance(OutputInterface &$outputInterface)
    {
        $this->output = $outputInterface;
    }

    /**
     * Returns the OutputInterface implementation instance.
     *
     * @return OutputInterface
     */
    public function output()
    {
        return $this->output;
    }

    /**
     * Sets the InputInterface implementation instance.
     *
     * @param InputInterface $inputInterface
     */
    public function setInputInstance(InputInterface &$inputInterface)
    {
        $this->input = $inputInterface;
    }

    /**
     * Returns the InputInterface implementation instance.
     *
     * @return InputInterface
     */
    public function input()
    {
        return $this->input;
    }

    /**
     * Confirm a question with the user.
     *
     * @param  string  $question
     * @param  bool    $default
     * @return bool
     */
    public function confirm($question, $default = false)
    {
        $helper = $this->getHelperSet()->get('question');

        $question = new ConfirmationQuestion("<question>{$question}</question> ", $default);

        return $helper->ask($this->input, $this->output, $question);
    }

    /**
     * Prompt the user for input.
     *
     * @param  string  $question
     * @param  string  $default
     * @return string
     */
    public function ask($question, $default = null)
    {
        $helper = $this->getHelperSet()->get('question');

        $question = new Question("<question>$question</question> ", $default);

        return $helper->ask($this->input, $this->output, $question);
    }

    /**
     * Prompt the user for input with auto completion.
     *
     * @param  string  $question
     * @param  array   $choices
     * @param  string  $default
     * @return string
     */
    public function askWithCompletion($question, array $choices, $default = null)
    {
        $helper = $this->getHelperSet()->get('question');

        $question = new Question("<question>$question</question> ", $default);

        $question->setAutocompleterValues($choices);

        return $helper->ask($this->input, $this->output, $question);
    }

    /**
     * Prompt the user for input but hide the answer from the console.
     *
     * @param  string  $question
     * @param  bool    $fallback
     * @return string
     */
    public function secret($question, $fallback = true)
    {
        $helper = $this->getHelperSet()->get('question');

        $question = new Question("<question>$question</question> ");

        $question->setHidden(true)->setHiddenFallback($fallback);

        return $helper->ask($this->input, $this->output, $question);
    }

    /**
     * Give the user a single choice from an array of answers.
     *
     * @param  string  $question
     * @param  array   $choices
     * @param  string  $default
     * @param  mixed   $attempts
     * @param  bool    $multiple
     * @return bool
     */
    public function choice($question, array $choices, $default = null, $attempts = null, $multiple = null)
    {
        $helper = $this->getHelperSet()->get('question');

        $question = new ChoiceQuestion("<question>$question</question> ", $choices, $default);

        $question->setMaxAttempts($attempts)->setMultiselect($multiple);

        return $helper->ask($this->input, $this->output, $question);
    }

    /**
     * Format input to textual table.
     *
     * @param  array   $headers
     * @param  array   $rows
     * @param  string  $style
     * @return void
     */
    public function table(array $headers, array $rows, $style = 'default')
    {
        $table = new Table($this->output);

        $table->setHeaders($headers)->setRows($rows)->setStyle($style)->render();
    }

    /**
     * Write a string as information output.
     *
     * @param  string  $string
     * @return void
     */
    public function info($string)
    {
        $this->output->writeln("<info>$string</info>");
    }

    /**
     * Write a string as standard output.
     *
     * @param  string  $string
     * @return void
     */
    public function line($string)
    {
        $this->output->writeln($string);
    }

    /**
     * Write a string as comment output.
     *
     * @param  string  $string
     * @return void
     */
    public function comment($string)
    {
        $this->output->writeln("<comment>$string</comment>");
    }

    /**
     * Write a string as question output.
     *
     * @param  string  $string
     * @return void
     */
    public function question($string)
    {
        $this->output->writeln("<question>$string</question>");
    }

    /**
     * Write a string as error output.
     *
     * @param  string  $string
     * @return void
     */
    public function error($string)
    {
        $this->output->writeln("<error>$string</error>");
    }

}