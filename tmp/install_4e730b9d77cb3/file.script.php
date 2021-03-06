<?php
/**
* @package   com_zoo Component
* @file      file.script.php
* @version   2.4.9 May 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class Com_ZOOInstallerScript {

	function install($parent) {

		// get installer
		$installer = method_exists($parent, 'getParent') ? $parent->getParent() : $parent->parent;

		// try to set time limit
		@set_time_limit(0);

		// try to increase memory limit
		if((int) ini_get('memory_limit') < 32) {
			@ini_set('memory_limit', '32M');
		}

		// check requirements
		require_once($installer->getPath('source').'/admin/installation/requirements.php');

		$requirements = new AppRequirements();

		$fulfilled = $requirements->checkRequirements();

		$requirements->displayResults();

		if (!$fulfilled) {
			$installer->abort(JText::_('Component').' '.JText::_('Install').': '.JText::_('Minimum requirements not fulfilled.'));
			return false;
		}

		// requirements fulfilled, install the ZOO
		require_once($installer->getPath('source').'/admin/installation/zooinstall.php');
		return ZooInstall::doInstall($installer);
		
	}

	function uninstall($parent) {

		// get installer
		$installer = method_exists($parent, 'getParent') ? $parent->getParent() : $parent->parent;

		require_once($installer->getPath('source').'/installation/zooinstall.php');
		return ZooInstall::doUninstall($installer);
	}

	function update($parent) {
		return $this->install($parent);
	}

	function preflight($type, $parent) {

		// remove ZOO from admin menus
		$db = JFactory::getDBO();
		$query = 'DELETE FROM #__menu WHERE alias = "zoo" AND menutype = "main"';
		$db->setQuery($query);
		$db->query();

	}

	function postflight($type, $parent) {
		
		$row = JTable::getInstance('extension');
		if ($row->load($row->find(array('element' => 'com_zoo'))) && strlen($row->element)) {
			$row->client_id = 1;
			$row->store();
		}
		
	}
}
