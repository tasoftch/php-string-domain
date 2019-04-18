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

namespace TASoft\StrDom;

/**
 * Domain Util
 * @package TASoft\StrDom
 */
final class Domain
{
    /**
     * Divides a domain into areas
     *
     * @param string $domain
     * @return array
     */
    public static function explode(string $domain): array {
        return explode(".", $domain);
    }

    /**
     * Combine areas into a domain
     *
     * @param array $areas
     * @return string
     */
    public static function implode(array $areas): string {
        return implode(".", $areas);
    }

    /**
     * Gets the last area by yielding the parent domain into parent.
     *
     * @param string $domain
     * @param string|NULL $parent
     * @return string
     */
    public static function getLast(string $domain, string &$parent = NULL): string {
        $p = self::explode($domain);
        $l = array_pop($p);
        $parent = self::implode($p);
        return $l;
    }

    /**
     * Gets the parent domain by yielding the last area
     *
     * @param string $domain
     * @param string|NULL $last
     * @return string
     */
    public static function getParent(string $domain, string &$last = NULL): string {
        $p = self::explode($domain);
        $last = array_pop($p);
        return self::implode($p);
    }

    /**
     * Checks if subdomain is a plain sub domain of domain.
     *
     * @param string $domain
     * @param string $subDomain
     * @param bool $caseSensitive
     * @return bool
     */
    public static function isSubDomain(string $domain, string $subDomain, bool $caseSensitive = false): bool {
        return (
            $caseSensitive ?
                strcmp($domain, substr($subDomain, 0, strlen($subDomain))) :
                strcasecmp($domain, substr($subDomain, 0, strlen($subDomain)))
        ) === 0 ? true : false;
    }

    /**
     * Checks, if $domain matches a specified domain query.
     * The membership domain may use wildcard syntax:
     *   - CHILDREN         => *
     *   - ALL SUBDOMAINS   => tailing dot .
     *
     * @example Domain::matchesDomainQuery( 'ch.tasoft.application', 'ch' ) => false
     *
     * @param string $domain
     * @param string $domainQuery
     * @param bool $caseSensitive
     * @return bool
     */
    public static function matchesDomainQuery(string $domain, string $domainQuery, bool $caseSensitive = false): bool {
        if(!$domainQuery)
            return false;

        $dp = self::explode($caseSensitive == false ? strtoupper($domain) : $domain);
        $qp = self::explode($caseSensitive == false ? strtoupper($domainQuery) : $domainQuery);

        if(count($qp) > $dp)
            return false;

        $deepSearch = function($queries, $array) use (&$deepSearch) {
            $query = array_shift($queries);
            $part = array_shift($array);

            if($query === '' && $part !== NULL) {
                yield true;
            } elseif(!fnmatch($query, $part)) {
                yield false;
            } elseif($part === NULL && $query !== NULL) {
                yield false;
            } elseif($query !== NULL) {
                yield from $deepSearch($queries, $array);
            }
        };

        foreach($deepSearch($qp, $dp) as $res) {
            return $res;
        }
        return true;
    }
}