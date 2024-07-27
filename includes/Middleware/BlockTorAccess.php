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

namespace Taavi\LaravelTorblock\Middleware;

use Closure;
use Illuminate\Http\Request;
use Taavi\LaravelTorblock\Service\TorExitNodeService;

/**
 * Middleware to block access from a Tor exit node.
 */
class BlockTorAccess
{
    private TorExitNodeService $torExitNodeService;

    public function __construct(TorExitNodeService $torExitNodeService)
    {
        $this->torExitNodeService = $torExitNodeService;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, $next)
    {
        if ($this->torExitNodeService->isTorExitNode($request->ip())) {
            throw $this->torExitNodeService->createException($request->ip());
        }

        return $next($request);
    }
}
