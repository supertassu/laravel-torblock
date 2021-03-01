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

namespace Taavi\LaravelTorblock\Service;

use Taavi\LaravelTorblock\TorBlocked;

/**
 * Fake implementation of {@link TorExitNodeService} that will use addresses in the TEST-NET-1 defined in RFC 5737
 * instead of reaching out to the real Onionoo server.
 */
class FakeTorExitNodeService extends BaseTorExitNodeService
{
    const NOT_BLOCKED_EXAMPLE_ADDRESS = '192.0.2.123';
    const BLOCKED_EXAMPLE_ADDRESS = '192.0.2.111';

    /**
     * Two addresses in the TEST-NET-1 defined in RFC 5737, intended for documentation and examples.
     */
    const BLOCKED_EXAMPLE_ADDRESSES = [
        self::BLOCKED_EXAMPLE_ADDRESS,
        '192.0.2.222'
    ];

    protected function getExitNodes(): array
    {
        return self::BLOCKED_EXAMPLE_ADDRESSES;
    }

    public function createException(string $ip): TorBlocked
    {
        // no need to waste time creating messages here
        return new TorBlocked("$ip is blocked as a Tor exit node.");
    }
}
