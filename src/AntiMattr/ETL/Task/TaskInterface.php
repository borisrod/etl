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
use AntiMattr\ETL\Listener\TaskListenerInterface;
use AntiMattr\ETL\Load\LoaderInterface;
use AntiMattr\ETL\Task\DataContext\DataContextInterface;
use AntiMattr\ETL\Transform\TransformationInterface;
use Doctrine\Common\Collections\Collection;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
interface TaskInterface
{
    public function initialize();

    /**
     * @return \AntiMattr\ETL\Task\DataContext\DataContextInterface
     */
    public function getDataContext();

    /**
     * @param \AntiMattr\ETL\Task\DataContext\DataContextInterface
     */
    public function setDataContext(DataContextInterface $data);

    /**
     * @param string
     */
    public function setDefaultTransformationClass($defaultTransformationClass);

    /**
     * @return \AntiMattr\ETL\Extract\ExtractorInterface
     */
    public function getExtractor();

    /**
     * @param \AntiMattr\ETL\Extract\ExtractorInterface
     */
    public function setExtractor(ExtractorInterface $extractor);

    /**
     * @param \AntiMattr\ETL\Listener\TransformationListenerInterface
     */
    public function setListener(TaskListenerInterface $listener);

    /**
     * @return \AntiMattr\ETL\Listener\TransformationListenerInterface
     */
    public function getListener();

    /**
     * @return \AntiMattr\ETL\Load\LoaderInterface
     */
    public function getLoader();

    /**
     * @param \AntiMattr\ETL\Load\LoaderInterface
     */
    public function setLoader(LoaderInterface $loader);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param array
     */
    public function setOptions(array $options = []);

    /**
     * @param \AntiMattr\ETL\Transform\TransformationInterface
     */
    public function addTransformation(TransformationInterface $transformation);

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransformations();

    /**
     * @param \Doctrine\Common\Collections\Collection
     */
    public function setTransformations(Collection $transformations);
}
