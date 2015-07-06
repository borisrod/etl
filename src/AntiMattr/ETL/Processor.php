<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL;

use AntiMattr\ETL\Event\TaskEvent;
use AntiMattr\ETL\Event\TransformationEvent;
use AntiMattr\ETL\Exception\ExtractException;
use AntiMattr\ETL\Exception\LoadException;
use AntiMattr\ETL\Exception\TransformException;
use AntiMattr\ETL\Exception\TransformationContinueException;
use AntiMattr\ETL\Lock\LockInterface;
use AntiMattr\ETL\Task\DataContext\DataContextInterface;
use AntiMattr\ETL\Task\TaskInterface;
use AntiMattr\ETL\Transform\TransformationInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class Processor
{
    /** @var string 'default' */
    protected $alias;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var \AntiMattr\ETL\Lock\LockInterface */
    protected $locker;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Doctrine\Common\Collections\Collection */
    protected $tasks;

    /**
     * @param string                                                      $alias
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Psr\Log\LoggerInterface                                    $logger
     */
    public function __construct(
        $alias = 'default',
        EventDispatcherInterface $eventDispatcher,
        LockInterface $locker,
        LoggerInterface $logger = null)
    {
        $this->alias = $alias;
        $this->eventDispatcher = $eventDispatcher;
        $this->locker = $locker;
        $this->logger = $logger;
        $this->tasks = new ArrayCollection();
    }

    /**
     * @return string $alias
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param \AntiMattr\ETL\Task\TaskTrait
     */
    public function addTask($task)
    {
        $this->tasks->add($task);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection
     */
    public function setTasks(Collection $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * @param string $taskName
     * @param array  $options
     *
     * @throws \RuntimeException
     */
    public function executeTask($taskName, $options = [])
    {
        $startTime = new \DateTime();
        $this->logInfo(sprintf("%s.%s started", $this->alias, $taskName));

        if ($this->locker->hasLock($this, $taskName)) {
            $this->logInfo(sprintf("%s.%s has lock", $this->alias, $taskName));
            $this->finish($startTime, $taskName);
            return;
        }

        $this->locker->lock($this, $taskName);

        if (!$task = $this->tasks->get($taskName)) {
            $this->logError(sprintf("%s.%s %s not configured", $this->alias, $taskName, 'RuntimeException', 'TaskInterface'));
            $this->finish($startTime, $taskName);
            return;
        }

        if (!$dataContext = $task->getDataContext()) {
            $this->logError(sprintf("%s.%s %s not configured", $this->alias, $taskName, 'RuntimeException', 'DataContextInterface'));
            $this->finish($startTime, $taskName);
            return;
        }

        $dataContext->setStartedAt($startTime);

        if (!$extractor = $task->getExtractor()) {
            $this->logError(sprintf("%s.%s %s not configured", $this->alias, $taskName, 'RuntimeException', 'ExtractorInterface'));
            $this->finish($startTime, $taskName);
            return;
        }

        if (!$taskListener = $task->getListener()) {
            $this->logError(sprintf("%s.%s %s not configured", $this->alias, $taskName, 'RuntimeException', 'TaskListenerInterface'));
            $this->finish($startTime, $taskName);
            return;
        }

        if (!$loader = $task->getLoader()) {
            $this->logError(sprintf("%s.%s %s not configured", $this->alias, $taskName, 'RuntimeException', 'LoaderInterface'));
            $this->finish($startTime, $taskName);
            return;
        }

        // Run Extract
        try {
            $iterator = $extractor->getIterator();
            $this->logInfo(sprintf("%s.%s Extracted Batch Iterator", $this->alias, $taskName));
        } catch (LoadException $e) {
            $this->logError(sprintf("%s.%s %s %s", $this->alias, $taskName, 'LoadException', $e->getMessage()));
            $this->finish($startTime, $taskName);
            return;
        }

        // Run Transformations
        $transformations = $task->getTransformations();
        $task->setDataContext($dataContext);
        $taskEventName = sprintf(
            "%s.%s.task.complete",
            $this->alias,
            $taskName
        );

        $this->eventDispatcher->addListener($taskEventName, array($taskListener, 'onComplete'));

        foreach ($iterator as $iteration => $extractedRecord) {
            $dataContext->setCurrentIteration($iteration);
            $dataContext->setCurrentExtractedRecord($extractedRecord);
            $dataContext->setCurrentTransformedRecord([]);

            foreach ($transformations as $transformationKey => $transformation) {
                $transformationEventName = sprintf(
                    "%s.%s.transformation.%s.complete",
                    $this->alias,
                    $taskName,
                    $transformationKey
                );

                if (!$this->eventDispatcher->hasListeners($transformationEventName) && $transformationListener = $transformation->getListener()) {
                    $this->eventDispatcher->addListener($transformationEventName, array($transformationListener, 'onComplete'));
                }

                try {
                    $transformation->shouldContinue();
                    $field = $transformation->getField();
                    $value = $extractedRecord[$field];

                    $transformers = $transformation->getTransformers();
                    foreach ($transformers as $transformer) {
                        try {
                            $value = $transformer->transform($value, $transformation);
                            $transformer->bind($value, $transformation);
                        } catch (TransformException $e) {
                            $this->logError(sprintf("%s.%s %s %s", $this->alias, $taskName, 'TransformException', $e->getMessage()));
                        }
                    }
                    try {
                        $transformation->postTransform();
                    } catch (TransformException $e) {
                        $this->logError(sprintf("%s.%s %s %s", $this->alias, $taskName, 'TransformException', $e->getMessage()));
                    }
                } catch (TransformationContinueException $e) {
                    continue;
                }

                $transformationEvent = $this->createTransformationEvent($transformation);

                try {
                    $this->eventDispatcher->dispatch($transformationEventName, $transformationEvent);
                } catch (LoadException $e) {
                    $this->logError(sprintf("%s.%s %s %s", $this->alias, $taskName, 'LoadException', $e->getMessage()));
                }
            }

            if (true === $iterator->isBatchComplete()) {
                $this->logInfo(sprintf("%s.%s %s Transformed extract records to load via %s", $this->alias, $taskName, $dataContext->getTransformedCount(), get_class($loader)));
                $taskEvent = $this->createTaskEvent($task);

                try {
                    $this->eventDispatcher->dispatch($taskEventName, $taskEvent);
                    $this->logInfo(sprintf("%s.%s %s Loaded records affected", $this->alias, $taskName, $dataContext->getLoadedCount()));
                } catch (LoadException $e) {
                    $this->logError(sprintf("%s.%s %s %s", $this->alias, $taskName, 'LoadException', $e->getMessage()));
                }

                $dataContext = $this->cloneDataContext($dataContext);
                $task->setDataContext($dataContext);
            }
        }

        $this->logInfo(sprintf("%s.%s %s Transformed extract records to load via %s", $this->alias, $taskName, $dataContext->getTransformedCount(), get_class($loader)));
        $taskEvent = $this->createTaskEvent($task);

        try {
            $this->eventDispatcher->dispatch($taskEventName, $taskEvent);
            $this->logInfo(sprintf("%s.%s %s Loaded records affected", $this->alias, $taskName, $dataContext->getLoadedCount()));
        } catch (LoadException $e) {
            $this->logError(sprintf("%s.%s %s %s", $this->alias, $taskName, 'LoadException', $e->getMessage()));
        }

        $this->finish($startTime, $taskName);
    }

    /**
     * @param \DateTime $startTime
     * @param string $taskName
     */
    protected function finish(\DateTime $startTime, $taskName)
    {
        $endTime = new \DateTime();
        $diff = $startTime->diff($endTime);

        $this->logInfo(
            sprintf(
                "%s.%s finished - Ellapsed time %s",
                $this->alias,
                $taskName,
                $diff->format('%H:%I:%S')
            )
        );

        $this->locker->unLock($this, $taskName);
    }

    /**
     * @param \AntiMattr\ETL\Task\DataContext\DataContextInterface $dataContext
     */
    protected function cloneDataContext(DataContextInterface $dataContext)
    {
        return clone $dataContext;
    }

    /**
     * @param \AntiMattr\ETL\Task\TaskInterface $task
     * @param \AntiMattr\ETL\Event\TransformationEvent
     */
    protected function createTaskEvent(TaskInterface $task)
    {
        return new TaskEvent($task);
    }

    /**
     * @param \AntiMattr\ETL\Transform\TransformationInterface $transformation
     * @param \AntiMattr\ETL\Event\TransformationEvent
     */
    protected function createTransformationEvent(TransformationInterface $transformation)
    {
        return new TransformationEvent($transformation);
    }

    protected function logError($message)
    {
        if ($this->logger) {
            $this->logger->error($message);
        }
    }

    protected function logInfo($message)
    {
        if ($this->logger) {
            $this->logger->info($message);
        }
    }
}
