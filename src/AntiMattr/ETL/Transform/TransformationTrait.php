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


use AntiMattr\ETL\Exception\TransformationContinueException;
use AntiMattr\ETL\Exception\TransformException;
use AntiMattr\ETL\Listener\TransformationListener;
use AntiMattr\ETL\Listener\TransformationListenerInterface;
use AntiMattr\ETL\Task\TaskInterface;
use AntiMattr\ETL\Transform\Transformer\TransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
trait TransformationTrait
{
    /** @var string */
    public $defaultTransformerClass = 'AntiMattr\ETL\Transform\Transformer\NoopTransformer';

    /** @var mixed */
    public $defaultValue = null;

    /** @var string */
    public $field;

    /** @var string */
    public $name;

    /** @var \AntiMattr\ETL\Listener\TransformationListener */
    protected $listener;

    /** @var \AntiMattr\ETL\Task\TaskTrait */
    protected $task;

    /** @var \Doctrine\Common\Collections\Collection */
    protected $transformers;

    public function __construct()
    {
        $this->listener = new TransformationListener();
        $this->transformers = new ArrayCollection();
    }

    /**
     * @param array $configuration
     *
     * @throws \AntiMattr\ETL\Exception\TransformException
     */
    public function initialize(array $configuration = [])
    {
        if (!isset($configuration['field'])) {
            throw new TransformException('field is a required configuration');
        }

        $this->field = $configuration['field'];
        if (isset($configuration['name'])) {
            $this->name = $configuration['name'];
        } else {
            $this->name = $configuration['field'];
        }

        if (isset($configuration['defaultValue'])) {
            $this->defaultValue = $configuration['defaultValue'];
        }

        if (!isset($configuration['transformers']) || empty($configuration['transformers'])) {
            $object = new $this->defaultTransformerClass();
            $this->addTransformer($object);
            return;
        }

        foreach ($configuration['transformers'] as $transformer) {
            if (is_object($transformer)) {
                $this->addTransformer($transformer);
                continue;
            }

            if (!isset($transformer['class'])) {
                continue;
            }
            $object = new $transformer['class']();
            if (isset($transformer['properties'])) {
                foreach($transformer['properties'] as $key => $value) {
                    $object->{$key} = $value;
                }
            }
            $this->addTransformer($object);
        }
    }

    /**
     * @throws \AntiMattr\ETL\Exception\TransformationContinueException
     */
    public function shouldContinue()
    {
        $dataContext = $this->task->getDataContext();
        $currentExtractedRecord = $dataContext->getCurrentExtractedRecord();

        if (!isset($currentExtractedRecord[$this->field])) {
            $currentTransformedRecord = $dataContext->getCurrentTransformedRecord();
            $currentTransformedRecord[$this->name] = $this->defaultValue;
            $dataContext->setCurrentTransformedRecord($currentTransformedRecord);
            $this->postTransform();
            throw new TransformationContinueException();
        }
    }

    /**
     * @throws \AntiMattr\ETL\Exception\TransformException
     */
    public function postTransform()
    {
        $dataContext = $this->task->getDataContext();
        $currentTransformedRecord = $dataContext->getCurrentTransformedRecord();

        if (empty($currentTransformedRecord)) {
            return;
        }

        $transformed = $dataContext->getTransformed();
        $currentIteration = $dataContext->getCurrentIteration();

        $transformed[$currentIteration] = $currentTransformedRecord;
        $dataContext->setTransformed($transformed);
    }

    /**
     * @return string
     */
    public function getDefaultTransformerClass()
    {
        return $this->defaultTransformerClass;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \AntiMattr\ETL\Listener\TransformationListenerInterface
     */
    public function setListener(TransformationListenerInterface $listener)
    {
        $this->listener = $listener;
    }

    /**
     * @return \AntiMattr\ETL\Listener\TransformationListenerInterface
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @param \AntiMattr\ETL\Task\TaskInterface
     */
    public function setTask(TaskInterface $task)
    {
        $this->task = $task;
    }

    /**
     * @return \AntiMattr\ETL\Task\TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param \AntiMattr\ETL\Transform\Transformer\TransformerInterface
     */
    public function addTransformer(TransformerInterface $transformer)
    {
        $this->transformers->add($transformer);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransformers()
    {
        return $this->transformers;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection
     */
    public function setTransformers(Collection $transformers)
    {
        $this->transformers = $transformers;
    }
}
