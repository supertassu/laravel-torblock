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

namespace Taavi\LaravelTorblock;

use Illuminate\Support\ServiceProvider;
use Taavi\LaravelTorblock\Service\CachingTorExitNodeService;
use Taavi\LaravelTorblock\Service\TorExitNodeService;

class LaravelTorblockServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../resources/config/torblock.php', 'torblock'
        );

        $this->app->bind(TorExitNodeService::class, CachingTorExitNodeService::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../resources/config/torblock.php' => $this->app->configPath('torblock.php'),
            __DIR__.'/../resources/lang' => $this->app->resourcePath('lang/vendor/torblock'),
        ]);

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'torblock');
    }
}
