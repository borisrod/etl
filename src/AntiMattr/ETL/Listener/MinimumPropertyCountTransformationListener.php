<?php

namespace AntiMattr\ETL\Listener;

use AntiMattr\ETL\Event\TransformationEvent;

class MinimumPropertyCountTransformationListener implements TransformationListenerInterface
{
    protected $minimum;

    public function __construct($minimum)
    {
        $this->minimum = $minimum;
    }

    /**
     * @param AntiMattr\ETL\Event\TransformationEvent
     */
    public function onComplete(TransformationEvent $event)
    {
        $transformation = $event->getTransformation();
        $task = $transformation->getTask();
        $dataContext = $task->getDataContext();

        $current = $dataContext->getCurrentTransformedRecord();

        if ($this->minimum == count($current)) {
            return;
        };

        $currentIteration = $dataContext->getCurrentIteration();
        $dataContext->unsetExtractedOffset($currentIteration);
        $dataContext->unsetTransformedOffset($currentIteration);
    }
}
