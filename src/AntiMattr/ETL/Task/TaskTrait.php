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
use AntiMattr\ETL\Load\LoaderInterface;
use AntiMattr\ETL\Task\Data\CommonData;
use AntiMattr\ETL\Task\Data\DataInterface;
use AntiMattr\ETL\Transform\TransformationInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
trait TaskTrait
{
    /** @var \AntiMattr\ETL\Task\Data\DataInterface */
    protected $data;

    /** @var string */
    public $defaultTransformationClass = 'AntiMattr\ETL\Transform\CommonTransformation';

    /** @var \AntiMattr\ETL\Extract\ExtractorInterface */
    protected $extractor;

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
        $this->data = new CommonData();
        $this->data->setTask($this);
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
     * @return \AntiMattr\ETL\Task\Data\DataInterface
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \AntiMattr\ETL\Task\Data\DataInterface
     */
    public function setData(DataInterface $data)
    {
        $this->data = $data;
        if ($this !== $data->getTask()) {
            $data->setTask($this);
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
