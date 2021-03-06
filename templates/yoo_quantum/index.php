<?php
/**
* @package   yoo_quantum
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/



// no direct access
defined('_JEXEC') or die;
JHTML::_('behavior.framework', true);

// include config	
include_once(dirname(__FILE__).'/config.php');

// get warp
$warp = Warp::getInstance();

// load main template file, located in /layouts/template.php
echo $warp['template']->render('template');