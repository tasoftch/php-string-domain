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

    const OPTION_MATCH_LEAFES_ONLY = 1<<1;

    private $_treeCollection = [];


    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        parent::offsetSet($offset, $value);

        if(Domain::isValid($offset)) {
            $p = &$this->_treeCollection;
            foreach(Domain::explode($offset) as $part) {
                $l = $p[$part] ?? [];
                $p[$part] = $l;
                $p = &$p[$part];
            }
            $p['#'][] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        parent::offsetUnset($offset);

        if($root = Domain::explode($offset) [0] ?? NULL) {
            if(isset($this->collection[$root]))
                unset($this->collection[$root]);
        }
    }

    /**
     * @inheritDoc
     */
    public function yieldElementsByQuery(string $domainQuery, int $options = 0)
    {
        $deepSearch = function($parts, $collection, $root = "", $yieldAll = false) use ($options, &$deepSearch) {
            $part = array_shift($parts);
            if($yieldAll)
                $part = '';

            foreach($collection as $area => $container) {
                if($part == '' || fnmatch($part, $area, $options & self::OPTION_MATCH_CASE_SENSITIVE ? FNM_CASEFOLD : 0)) {
                    $domain = $root ? "$root.$area" : $area;

                    if(
                        ($content = $container["#"] ?? NULL) && (
                            $yieldAll || // If the query is a subdomains wildcard, yield everything!
                            ($options & self::OPTION_MATCH_LEAFES_ONLY) == 0 || // Not leafes only
                                count($container) == 1                          // Or with leafes only, when container does not hold more areas
                        )
                    ) {
                        foreach($content as $c)
                            yield $domain => $c;
                    }

                    unset($container["#"]);

                    if($parts || ($container && $part == ''))
                        yield from $deepSearch($parts, $container, $domain, $part == '');
                }
            }
        };

        yield from $deepSearch(Domain::explode($domainQuery), $this->_treeCollection);
    }
}