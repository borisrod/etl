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

use AntiMattr\ETL\Exception\ExtractException;
use AntiMattr\ETL\Exception\LoadException;
use AntiMattr\ETL\Exception\TransformException;
use AntiMattr\ETL\Exception\TransformationContinueException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Psr\Log\LoggerInterface;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class Processor
{
    /** @var string 'default' */
    protected $alias;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Doctrine\Common\Collections\Collection */
    protected $tasks;

    /**
     * @param string                   $alias
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct($alias = 'default', LoggerInterface $logger = null)
    {
        $this->alias = $alias;
        $this->logger = $logger;
        $this->tasks = new ArrayCollection();
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

        if (!$task = $this->tasks->get($taskName)) {
            $this->logError(sprintf("%s.%s %s not configured", $this->alias, $taskName, 'RuntimeException', 'TaskInterface'));
            $this->finish($startTime, $taskName);
            return;
        }

        if (!$dataObject = $task->getData()) {
            $this->logError(sprintf("%s.%s %s not configured", $this->alias, $taskName, 'RuntimeException', 'DataInterface'));
            $this->finish($startTime, $taskName);
            return;
        }

        $dataObject->setStartedAt($startTime);

        if (!$extractor = $task->getExtractor()) {
            $this->logError(sprintf("%s.%s %s not configured", $this->alias, $taskName, 'RuntimeException', 'ExtractorInterface'));
            $this->finish($startTime, $taskName);
            return;
        }

        // Run Extract
        try {
            $pages = $extractor->getPages();
            $this->logInfo(sprintf("%s.%s %s Extracted pages of records", $this->alias, $taskName, count($pages)));
        } catch (LoadException $e) {
            $this->logError(sprintf("%s.%s %s %s", $this->alias, $taskName, 'LoadException', $e->getMessage()));
            $this->finish($startTime, $taskName);
            return;
        }

        if (empty($pages)) {
            $this->finish($startTime, $taskName);
            return;
        }

        // Run Transformations
        $transformations = $task->getTransformations();

        foreach ($pages as $page) {
            $data = clone $dataObject;
            $task->setData($data);
            $data->setExtracted($page);
            foreach ($data->getExtracted() as $iteration => $extractedRecord) {
                $data->setCurrentIteration($iteration);
                $data->setCurrentExtractedRecord($extractedRecord);
                $data->setCurrentTransformedRecord([]);
                foreach ($transformations as $transformation) {
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
                }
                $data->unsetExtractedOffset($iteration);
            }

            // Run Load
            if (!$loader = $task->getLoader()) {
                $this->logError(sprintf("%s.%s %s not configured", $this->alias, $taskName, 'RuntimeException', 'LoaderInterface'));
                $this->finish($startTime, $taskName);
                return;
            }

            $data->setEndedAt(new \DateTime());
            try {
                $this->logInfo(sprintf("%s.%s %s Transformed records to load via %s", $this->alias, $taskName, $data->getTransformedCount(), get_class($loader)));
                $loader->load();
                $this->logInfo(sprintf("%s.%s %s Loaded records affected", $this->alias, $taskName, $data->getLoadedCount()));
                $loader->postLoad();
            } catch (LoadException $e) {
                $this->logError(sprintf("%s.%s %s %s", $this->alias, $taskName, 'LoadException', $e->getMessage()));
            }
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
