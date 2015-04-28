<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Transform;

use AntiMattr\ETL\Task\TaskInterface;
use AntiMattr\ETL\Transform\Transformer\TransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
interface TransformationInterface
{
    /**
     * @throws \AntiMattr\ETL\Exception\TransformationContinueException
     */
    public function shouldContinue();

    /**
     * @param array $configuration
     *
     * @throws \AntiMattr\ETL\Exception\TransformException
     */
    public function initialize(array $configuration = []);

    /**
     * @throws \AntiMattr\ETL\Exception\TransformException
     */
    public function postTransform();

    /**
     * @return string
     */
    public function getDefaultTransformerClass();

    /**
     * @return string
     */
    public function getDefaultValue();

    /**
     * @return string
     */
    public function getField();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param \AntiMattr\ETL\Task\TaskInterface
     */
    public function setTask(TaskInterface $task);

    /**
     * @return \AntiMattr\ETL\Task\TaskInterface
     */
    public function getTask();

    /**
     * @param \AntiMattr\ETL\Transform\Transformer\TransformerInterface
     */
    public function addTransformer(TransformerInterface $transformer);

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransformers();

    /**
     * @param \Doctrine\Common\Collections\Collection
     */
    public function setTransformers(Collection $transformers);
}
