<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Tools\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class ExecuteTaskCommand extends AbstractETLCommand
{
    /**
     * @return string
     */
    public function getName()
    {
        return "antimattr:etl:execute";
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return "Execute one task";
    }

    protected function configure()
    {
        parent::configure();
        $this
            ->addOption(
                'task',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Identify which tasks to run'
            )
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface
     * @param \Symfony\Component\Console\Output\OutputInterface
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $processor = $input->getArgument('processor');
        $this->processor = $this->container->get($processor);

        $tasks = $input->getOption('task');
        foreach ($tasks as $task) {
            $this->processor->executeTask($task);
        }
    }
}