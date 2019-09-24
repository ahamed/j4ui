<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_cache
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ModuleHelper;

//include js
HTMLHelper::_('script', 'mod_cache/cache.min.js', ['version' => 'auto', 'relative' => true]);
// include helper file
require_once __DIR__ . '/helper.php';
$cacheInfo   = ModCacheHelper::getCacheSize();

require ModuleHelper::getLayoutPath('mod_cache', $params->get('layout', 'default'));