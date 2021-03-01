<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

namespace Taavi\LaravelTorblock\Tests;

use Generator;
use Orchestra\Testbench\TestCase;
use Taavi\LaravelTorblock\LaravelTorblockServiceProvider;
use Taavi\LaravelTorblock\Service\FakeTorExitNodeService;

abstract class BaseTestCase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelTorblockServiceProvider::class,
        ];
    }

    public static function provideMaybeBlockedIps(): Generator
    {
        yield 'Blocked address' => [FakeTorExitNodeService::BLOCKED_EXAMPLE_ADDRESS, true];
        yield 'Open address' => [FakeTorExitNodeService::NOT_BLOCKED_EXAMPLE_ADDRESS, false];
    }
}