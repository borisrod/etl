<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Task;

use AntiMattr\ETL\Extract\ExtractorInterface;
use AntiMattr\ETL\Listener\TaskListener;
use AntiMattr\ETL\Listener\TaskListenerInterface;
use AntiMattr\ETL\Load\LoaderInterface;
use AntiMattr\ETL\Task\DataContext\CommonDataContext;
use AntiMattr\ETL\Task\DataContext\DataContextInterface;
use AntiMattr\ETL\Transform\TransformationInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
trait TaskTrait
{
    /** @var \AntiMattr\ETL\Task\DataContext\DataContextInterface */
    protected $dataContext;

    /** @var string */
    public $defaultTransformationClass = 'AntiMattr\ETL\Transform\CommonTransformation';

    /** @var \AntiMattr\ETL\Extract\ExtractorInterface */
    protected $extractor;

    /** @var \AntiMattr\ETL\Listener\TransformationListenerInterface */
    protected $listener;

    /** @var \AntiMattr\ETL\Load\LoaderInterface */
    protected $loader;

    /** @var array */
    public $configuration = array();

    /** @var array */
    protected $options = array();

    /** @var \Doctrine\Common\Collections\Collection */
    protected $transformations;

    public function __construct()
    {
        $this->dataContext = new CommonDataContext();
        $this->dataContext->setTask($this);
        $this->listener = new TaskListener();
        $this->transformations = new ArrayCollection();
    }

    public function initialize()
    {
        if (empty($this->configuration)) {
           return;
        }
        foreach ($this->configuration as $config) {
            if (!isset($config['class'])) {
                $config['class'] = $this->defaultTransformationClass;
            }
            $transformation = new $config['class']();
            $this->addTransformation($transformation);
            $transformation->initialize($config);
        }
    }

    /**
     * @param string
     */
    public function setDefaultTransformationClass($defaultTransformationClass)
    {
        $this->defaultTransformationClass = $defaultTransformationClass;
    }

    /**
     * @return \AntiMattr\ETL\Task\DataContext\DataContextInterface
     */
    public function getDataContext()
    {
        return $this->dataContext;
    }

    /**
     * @param \AntiMattr\ETL\Task\DataContext\DataContextInterface
     */
    public function setDataContext(DataContextInterface $dataContext)
    {
        $this->dataContext = $dataContext;
        if ($this !== $dataContext->getTask()) {
            $dataContext->setTask($this);
        }
    }

    /**
     * @return \AntiMattr\ETL\Extract\ExtractorInterface
     */
    public function getExtractor()
    {
        return $this->extractor;
    }

    /**
     * @param \AntiMattr\ETL\Extract\ExtractorInterface
     */
    public function setExtractor(ExtractorInterface $extractor)
    {
        $this->extractor = $extractor;
        if ($this !== $extractor->getTask()) {
            $extractor->setTask($this);
        }
    }

    /**
     * @param \AntiMattr\ETL\Listener\TaskListenerInterface
     */
    public function setListener(TaskListenerInterface $listener)
    {
        $this->listener = $listener;
    }

    /**
     * @return \AntiMattr\ETL\Listener\TaskListenerInterface
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @return \AntiMattr\ETL\Load\LoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @param \AntiMattr\ETL\Load\LoaderInterface
     */
    public function setLoader(LoaderInterface $loader)
    {
        $this->loader = $loader;
        if ($this !== $loader->getTask()) {
            $loader->setTask($this);
        }
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array
     */
    public function setOptions(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * @param \AntiMattr\ETL\Transform\TransformationInterface
     */
    public function addTransformation(TransformationInterface $transformation)
    {
        $this->transformations->add($transformation);
        if ($this !== $transformation->getTask()) {
            $transformation->setTask($this);
        }
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransformations()
    {
        if ($this->transformations->isEmpty()) {
            $this->initialize();
        }
        return $this->transformations;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection
     */
    public function setTransformations(Collection $transformations)
    {
        $this->transformations = $transformations;
        foreach ($this->transformations as $transformation) {
            $transformation->setTask($this);
        }
    }
}
