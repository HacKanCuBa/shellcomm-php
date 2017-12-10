<?php

namespace HC;

/**
 * Autoloader for HC libraries.
 *
 * @version v0.1.1
 * ----------------------------------------------------------------------------
 *     Copyright (C) 2017 HacKan (https://hackan.net)
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * ----------------------------------------------------------------------------
 *
 */
class Autoloader
{
    /**
     * Register autoloader
     */
    public static function register()
    {
        if (function_exists('__autoload')) {
            // Just in case...
            spl_autoload_register('__autoload');
        }
        return spl_autoload_register([\HC\Autoloader::class, 'load'], true, true);
    }

    /**
     * Autoload a HC class identified by name.
     *
     * @param  string  $className  Name of the object to load
     */
    public static function load($className)
    {
        $prefix = 'HC\\';
        if ((class_exists($className, false)) || (strpos($className, $prefix) !== 0)) {
            // Either already loaded, or not a HC class request
            return false;
        }

        $classFilePath = __DIR__ . DIRECTORY_SEPARATOR .
            str_replace([$prefix, '\\'], ['', DIRECTORY_SEPARATOR], $className) .
            '.php';

        if ((file_exists($classFilePath) === false) || (is_readable($classFilePath) === false)) {
            // Can't load
            return false;
        }

        require $classFilePath;
    }
}
