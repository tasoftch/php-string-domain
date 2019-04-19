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


use TASoft\Collection\StrictEqualObjectsTrait;
use TASoft\StrDom\Domain;

/**
 * Class DomainTreeCollection stores elements in a hierarchical way, so children and parents can be filtered
 *
 * @package TASoft\StrDom\Collection
 */
class DomainTreeCollection extends DomainCollection
{
    use StrictEqualObjectsTrait;

    private $_reverse = [];

    /**
     * @inheritDoc
     */
    public function count()
    {
        $sum = 0;
        array_walk($this->_reverse, function($A) use (&$sum) {
            $sum += count($A);
        });
        return $sum;
    }

    public function offsetSet($offset, $value)
    {
        if(Domain::isValid($offset)) {
            $p = &$this->collection;
            foreach(Domain::explode($offset) as $part) {
                $l = $p[$part] ?? [];
                $p[$part] = $l;
                $p = &$p[$part];
            }
            $p['#'][] = $value;
        }
    }

    public function offsetGet($offset)
    {
        if(Domain::isValid($offset)) {
            $p = $this->collection;
            foreach(Domain::explode($offset) as $part) {
                if(!isset($p[$part]))
                    return NULL;
                $p = $p[$part];
            }
            return $p["#"] ?? NULL;
        }
        return NULL;
    }
}