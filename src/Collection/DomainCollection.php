<?php
/**
 * Copyright (c) 2019 TASoft Applications, Th. Abplanalp <info@tasoft.ch>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace TASoft\StrDom\Collection;


use TASoft\Collection\AbstractCollection;
use TASoft\Collection\StrictEqualObjectsTrait;
use TASoft\StrDom\Domain;

/**
 * Class DomainCollection stores a plain list of domains with assigned elements.
 *
 * @package TASoft\StrDom\Collection
 */
class DomainCollection extends AbstractCollection
{
    use StrictEqualObjectsTrait;

    const OPTION_MATCH_CASE_SENSITIVE = 1<<0;

    /**
     * DomainCollection constructor.
     *
     * Allows a keyed array, where the keys should be strings and should be a valid domains
     *
     * @param array $collection
     */
    public function __construct($collection = [])
    {
        parent::__construct([]);

        if($collection instanceof DomainCollection)
            $this->collection = $collection->collection;
        else {
            foreach($collection as $domain => $element) {
                if(is_string($domain)) {
                    if(Domain::isValid($domain)) {
                        $this[$domain] = $element;
                    } else {
                        trigger_error("Domain $domain is invalid", E_USER_WARNING);
                    }
                } else
                    trigger_error("Iterable key must be a string and a valid domain. Entry was skipped", E_USER_WARNING);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        $sum = 0;
        array_walk($this->collection, function($A) use (&$sum) {
            $sum += count($A);
        });
        return $sum;
    }

    /**
     * Enables mutation
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if(Domain::isValid($offset)) {
            if(is_array($value))
                $this->collection[$offset] = $value;
            else
                $this->collection[$offset][] = $value;
        }
    }

    /**
     * Enables mutations
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if(isset($this->collection[$offset]))
            unset($this->collection[$offset]);
    }

    /**
     * Add elements to the collection under the given domain
     *
     * @param string $domain
     * @param mixed ...$elements
     */
    public function add(string $domain, ...$elements) {
        if(Domain::isValid($domain)) {
            foreach($elements as $element)
                $this[$domain] = $element;
        }
    }

    /**
     * Removes an element from all domains
     * @param $element
     */
    public function remove($element) {
        foreach($this->collection as &$elements) {
            $elements = array_filter($elements, function($A) use ($element) {
                return ! $this->objectsAreEqual($A, $element);
            });
        }
    }

    /**
     * Yields all elements whose domain matches to the domain query.
     *
     * @param string $domainQuery
     * @param int $options
     * @return \Generator
     * @see DomainCollection::OPTION_MATCH_* constants
     */
    public function yieldElementsByQuery(string $domainQuery, int $options = 0) {
        foreach($this->collection as $domain => $elements) {
            if(Domain::matchesDomainQuery($domain, $domainQuery, $options & self::OPTION_MATCH_CASE_SENSITIVE ? true : false)) {
                foreach($elements as $element)
                    yield $domain => $element;
            }
        }
    }

    /**
     * Returns an array containing all elements whose domain matches to the domain query.
     *
     * @param string $domainQuery
     * @param int $options
     * @return array
     * @see DomainCollection::OPTION_MATCH_* constants
     */
    public function getElementsByQuery(string $domainQuery, int $options = 0): array {
        $list = [];
        foreach($this->yieldElementsByQuery($domainQuery, $options) as $element) {
            $list[] = $element;
        }
        return $list;
    }
}