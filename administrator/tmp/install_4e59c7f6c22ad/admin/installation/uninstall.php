<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// init vars
$path = dirname(dirname(__FILE__));

// load install script
require_once($path.'/file.script.php');

return Com_ZOOInstallerScript::uninstall($this);