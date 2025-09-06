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

use Wikimedia\IPUtils;

abstract class BaseTorExitNodeService implements TorExitNodeService
{
    /**
     * {@inheritDoc}
     */
    public function isTorExitNode(string $ip): bool
    {
        // Sanitize the address the same way it's sanitized when collecting data,
        // this avoids bugs like https://github.com/supertassu/laravel-torblock/issues/1
        $ip = IPUtils::sanitizeIP($ip);

        return isset($this->getExitNodes()[$ip]);
    }

    /**
     * Get a cached list of Tor exit nodes, or load and cache a new list if it's not currently available.
     * @return array<string, 1>
     */
    protected abstract function getExitNodes(): array;
}
