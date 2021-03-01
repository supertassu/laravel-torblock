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

use Illuminate\Http\Request;
use Mockery;
use Taavi\LaravelTorblock\Middleware\BlockTorAccess;
use Taavi\LaravelTorblock\Service\FakeTorExitNodeService;
use Taavi\LaravelTorblock\TorBlocked;

class BlockTorAccessTest extends BaseTestCase
{
    /**
     * @dataProvider provideMaybeBlockedIps
     * @param string $ip
     * @param bool $blocked
     */
    public function testMiddleware(string $ip, bool $blocked) {
        $middleware = new BlockTorAccess(new FakeTorExitNodeService());

        $request = Mockery::mock(Request::class, function (Mockery\MockInterface $mock) use ($ip) {
            $mock->shouldReceive('ip')
                ->atLeast()->once()
                ->andReturn($ip);
        });

        if ($blocked) {
            $this->expectException(TorBlocked::class);
            $neverCalled = function (Request $request) {
                $this->fail('Should not have continued.');
            };

            $middleware->handle($request, $neverCalled);
        } else {
            $this->expectException(ShouldBeCalled::class);
            $shouldBeCalled = function (Request $request) {
                throw new ShouldBeCalled();
            };

            $middleware->handle($request, $shouldBeCalled);
        }
    }
}
