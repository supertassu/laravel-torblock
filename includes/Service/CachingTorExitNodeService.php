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

use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository as Config;
use Illuminate\Log\Logger;
use Taavi\LaravelTorblock\TorBlocked;

/**
 * Fetch and cache Tor exit nodes.
 *
 * Adapted from the TorBlock MediaWiki extension, see:
 * https://github.com/wikimedia/mediawiki-extensions-TorBlock/blob/master/includes/TorExitNodes.php
 */
class CachingTorExitNodeService extends BaseTorExitNodeService
{
    private TorExitNodeService $base;
    private Config $config;
    private CacheManager $cache;
    private Logger $logger;

    public function __construct(
        TorExitNodeService $base,
        Config $config,
        CacheManager $cache,
        Logger $logger
    )
    {
        $this->base = $base;
        $this->config = $config;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function createException(string $ip): TorBlocked
    {
        return $this->base->createException($ip);
    }

    /**
     * {@inheritDoc}
     */
    protected function getExitNodes(): array
    {
        return $this->cache
            ->remember(
                $this->config->get('torblock.cache-prefix').'exit-nodes',
                $this->config->get('torblock.cache-ttl'),
                function () {
                    $this->logger->debug('TorBlock: Loading exit node data.');
                    return $this->base->getExitNodes();
                }
            );
    }
}
