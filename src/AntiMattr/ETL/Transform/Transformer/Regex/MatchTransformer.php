<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Transform\Transformer\Regex;

use AntiMattr\ETL\Exception\TransformException;
use AntiMattr\ETL\Transform\TransformationInterface;
use AntiMattr\ETL\Transform\Transformer\TransformerInterface;
use AntiMattr\ETL\Transform\Transformer\TransformerTrait;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class MatchTransformer implements TransformerInterface
{
    use TransformerTrait;

    /**
     * @param mixed                                                        $value
     * @param \AntiMattr\ETL\Transform\Transformer\TransformationInterface $transformation
     *
     * @return mixed $value
     *
     * @throws \AntiMattr\ETL\Exception\TransformException
     */
    public function transform($value, TransformationInterface $transformation)
    {
        if (!isset($this->options['pattern'])) {
            throw new TransformException("MatchTransformer: Required options::pattern");
        }
        if (!isset($this->options['true'])) {
            throw new TransformException("MatchTransformer: Required options::true");
        }
        if (!isset($this->options['false'])) {
            throw new TransformException("MatchTransformer: Required options::false");
        }

        if (!isset($value) || !is_string($value)) {
            return;
        }

        return 0 === preg_match($this->options['pattern'], $value) ? $this->options['false']: $this->options['true'];
    }
}
