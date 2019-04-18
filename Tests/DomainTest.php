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

/**
 * DomainTest.php
 * php-string-domain
 *
 * Created on 2019-04-18 17:31 by thomas
 */

use PHPUnit\Framework\TestCase;
use TASoft\StrDom\Domain;

class DomainTest extends TestCase
{
    public function testExplodeDomain() {
        $this->assertEquals([
            "ch",
            "tasoft",
            "application"
        ], Domain::explode("ch.tasoft.application"));
    }

    public function testImplodeAreas() {
        $this->assertEquals("ch.tasoft.application", Domain::implode(["ch", "tasoft", "application"]));
    }

    public function testGetLast() {
        $this->assertEquals("application", Domain::getLast("ch.tasoft.application", $parent));
        $this->assertEquals("ch.tasoft", $parent);
    }

    public function testGetLast2() {
        $this->assertEquals("", Domain::getLast("", $parent));
        $this->assertEquals("", $parent);
    }

    public function testGetLast3() {
        $this->assertEquals("ch", Domain::getLast("ch", $parent));
        $this->assertEquals("", $parent);
    }

    public function testGetLast4() {
        $this->assertEquals("application", Domain::getLast("ch.tasoft.application"));
    }

    public function testGetParent() {
        $this->assertEquals("ch.tasoft", Domain::getParent("ch.tasoft.application", $last));
        $this->assertEquals("application", $last);
    }

    public function testMembership() {
        $this->assertTrue(Domain::matchesDomainQuery("ch.tasoft.application", "ch.tasoft.application"));
        $this->assertTrue(Domain::matchesDomainQuery("ch.tasoft.application", "ch.tasoft.*"));
        $this->assertTrue(Domain::matchesDomainQuery("ch.tasoft.application", "ch."));

        $this->assertFalse(Domain::matchesDomainQuery("ch.tasoft.application", ""));
        $this->assertFalse(Domain::matchesDomainQuery("ch.tasoft.application", "ch"));
        $this->assertFalse(Domain::matchesDomainQuery("ch.tasoft.application", "ch.tasoft"));
        $this->assertFalse(Domain::matchesDomainQuery("ch.tasoft.application", "ch.tasoft.app"));

        $this->assertFalse(Domain::matchesDomainQuery("ch.tasoft.application", "ch.tasoft.application.test"));
        $this->assertFalse(Domain::matchesDomainQuery("ch.tasoft.application", "ch.tasoft.application.*"));
        $this->assertFalse(Domain::matchesDomainQuery("ch.tasoft.application", "ch.tasoft.application."));

        $this->assertTrue(Domain::matchesDomainQuery("ch.tasoft.application", "ch.*.application"));
        $this->assertTrue(Domain::matchesDomainQuery("ch.test.application", "ch.*.application"));
        $this->assertTrue(Domain::matchesDomainQuery("ch.abc.application", "ch.*.application"));

        $this->assertTrue(Domain::matchesDomainQuery("ch.tasoft.application.test.2", "ch.*.application.*.2"));
        $this->assertTrue(Domain::matchesDomainQuery("ch.test.application.hello.world", "ch.*.application."));
        $this->assertTrue(Domain::matchesDomainQuery("ch.abc.application.hello", "ch.*.application.*"));
    }
}
