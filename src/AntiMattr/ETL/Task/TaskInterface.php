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
use AntiMattr\ETL\Task\Data\DataInterface;
use AntiMattr\ETL\Transform\TransformationInterface;
use Doctrine\Common\Collections\Collection;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
interface TaskInterface
{
    public function initialize();

    /**
     * @return \AntiMattr\ETL\Task\Data\DataInterface
     */
    public function getData();

    /**
     * @param \AntiMattr\ETL\Task\Data\DataInterface
     */
    public function setData(DataInterface $data);

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
