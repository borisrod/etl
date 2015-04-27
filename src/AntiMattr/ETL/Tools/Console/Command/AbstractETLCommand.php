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

use AntiMattr\ETL\Processor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
abstract class AbstractETLCommand extends Command implements ContainerAwareInterface
{
    /** @var array */
    protected $container;

    /** @var \AntiMattr\ETL\Processor */
    protected $processor;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "antimattr:etl";
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return "ETL Process runner";
    }

    protected function configure()
    {
        $this
            ->setName($this->getName())
            ->setDescription($this->getDescription())
            ->addArgument(
                'processor',
                InputArgument::REQUIRED
            )
        ;
    }
}