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
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\RequestException;
use Illuminate\Log\Logger;
use Taavi\LaravelTorblock\TorBlocked;
use Wikimedia\IPUtils;

/**
 * Fetch and cache Tor exit nodes.
 *
 * Adapted from the TorBlock MediaWiki extension, see:
 * https://github.com/wikimedia/mediawiki-extensions-TorBlock/blob/master/includes/TorExitNodes.php
 */
class CachingTorExitNodeService extends BaseTorExitNodeService
{
    /** @var Translator */
    private $translator;

    /** @var Config */
    private $config;

    /** @var CacheManager */
    private $cache;

    /** @var HttpClient */
    private $httpClient;

    /** @var Logger */
    private $logger;

    public function __construct(
        Translator $translator,
        Config $config,
        CacheManager $cache,
        HttpClient $httpClient,
        Logger $logger
    )
    {
        $this->translator = $translator;
        $this->config = $config;
        $this->cache = $cache;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function isTorExitNode(string $ip): bool
    {
        return in_array($ip, $this->getExitNodes());
    }

    /**
     * {@inheritDoc}
     */
    public function createException(string $ip): TorBlocked
    {
        $message = $this->translator->get('torblock::messages.blocked', ['ip' => $ip]);
        return new TorBlocked($message);
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
                    $this->logger->debug('TorBlock: Loading exit nodes from the Onionoo service.');
                    return $this->fetchExitNodes();
                }
            );
    }

    /**
     * Load all Tor exit nodes from the Onionoo service.
     * @return string[]
     * @throws RequestException
     */
    protected function fetchExitNodes(): array
    {
        $relays = $this->httpClient
            ->get($this->config->get('torblock.onionoo-base').'/details?type=relay&running=true&flag=Exit')
            ->throw()
            ->json('relays');

        return collect($relays)
            ->flatMap(function (array $relay) {
                return isset($relay['exit_addresses'])
                    ? array_merge($relay['or_addresses'], $relay['exit_addresses'])
                    : $relay['or_addresses'];
            })
            ->map(function ($ip) {
                // Trim the port if it has one.
                $portPosition = strrpos($ip, ':');
                if ($portPosition !== false) {
                    $ip = substr($ip, 0, $portPosition);
                }

                // Trim surrounding brackets for IPv6 addresses.
                $hasBrackets = $ip[0] == '[';
                if ($hasBrackets) {
                    $ip = substr( $ip, 1, -1 );
                }

                if (!IPUtils::isValid($ip)) {
                    $this->logger->debug('TorBlock: Invalid IP address in Onionoo response.', ['ip' => $ip]);
                    return null;
                }

                return IPUtils::sanitizeIP($ip);
            })
            ->whereNotNull()
            ->toArray();
    }
}
