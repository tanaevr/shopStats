<?php
/**
 * shopstats
 *
 * Copyright 2015 by Tanaev Roman <tanaevr@gmail.com>
 *
 * shopStats is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * shopStats is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * analytics; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package shopStats
 */
/**
 * Define the MODX path constants necessary for core installation
 */

/* define package */
define('PKG_NAME', 'shopStats');
define('PKG_NAME_LOWER', strtolower(PKG_NAME));

define('PKG_VERSION', '1.0.1');
define('PKG_RELEASE', 'beta');
define('PKG_AUTO_INSTALL', false);
define('PKG_NAMESPACE_PATH', '{core_path}components/' . PKG_NAME_LOWER . '/');


define('MODX_BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/');
define('MODX_CORE_PATH', MODX_BASE_PATH . 'core/');
define('MODX_MANAGER_PATH', MODX_BASE_PATH . 'manager/');
define('MODX_CONNECTORS_PATH', MODX_BASE_PATH . 'connectors/');
define('MODX_ASSETS_PATH', MODX_BASE_PATH . 'assets/');