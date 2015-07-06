<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Lock;

use AntiMattr\ETL\Processor;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
trait FileLockTrait
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $lockPath;

    /**
     * @param string|null $lockPath The directory to store the lock. Default values will use temporary directory
     */
    public function __construct($lockPath = null)
    {
        $this->filesystem = new Filesystem();

        $lockPath = $lockPath ?: sys_get_temp_dir();
        if (!is_dir($lockPath)) {
            $this->filesystem->mkdir($lockPath);
        }
        if (!is_writable($lockPath)) {
            throw new IOException(sprintf('The directory "%s" is not writable.', $lockPath), 0, null, $lockPath);
        }

        $this->lockPath = $lockPath;
    }

    /**
     * @param \AntiMattr\ETL\Processor $processor
     * @param string                   $taskName
     */
    public function hasLock(Processor $processor, $taskName)
    {
        $filePath = $this->createFilePath($processor, $taskName);
        return $this->filesystem->exists($filePath);
    }

    /**
     * @param \AntiMattr\ETL\Processor $processor
     * @param string                   $taskName
     */
    public function lock(Processor $processor, $taskName)
    {
        $filePath = $this->createFilePath($processor, $taskName);
        $this->filesystem->touch($filePath);
    }

    /**
     * @param \AntiMattr\ETL\Processor $processor
     * @param string                   $taskName
     */
    public function unLock(Processor $processor, $taskName)
    {
        $filePath = $this->createFilePath($processor, $taskName);
        $this->filesystem->remove($filePath);
    }

    /**
     * @param \AntiMattr\ETL\Processor $processor
     * @param string                   $taskName
     *
     * @return string
     */
    protected function createFilePath(Processor $processor, $taskName)
    {
        return sprintf(
            '%s/%s.%s.lock',
            $this->lockPath,
            $processor->getAlias(),
            $taskName
        );
    }
}
