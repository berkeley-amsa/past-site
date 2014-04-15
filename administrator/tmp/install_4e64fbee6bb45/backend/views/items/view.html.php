<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 */

defined('_JEXEC') or die('Restricted Access');

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'base.view.html.php';

class ArsViewItems extends ArsViewBase
{
	protected function onDisplay()
	{
		$app = JFactory::getApplication();
		$hash = $this->getHash();
		
		// ...filter states
		$this->lists->set('fltCategory',	$app->getUserStateFromRequest($hash.'filter_category',
			'category', null));
		$this->lists->set('fltRelease',		$app->getUserStateFromRequest($hash.'filter_release',
			'release', null));
		$this->lists->set('fltPublished',$app->getUserStateFromRequest($hash.'filter_published',
			'published', null));

		// Add toolbar buttons
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', 'ARS_GLOBAL_COPY', false);
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		JToolBarHelper::divider();
		JToolBarHelper::back(version_compare(JVERSION,'1.6.0','ge') ? 'JTOOLBAR_BACK' : 'Back', 'index.php?option='.JRequest::getCmd('option'));

		// Add submenus (those nifty text links below the toolbar!)
		// -- Categories
		$link = JURI::base().'?option='.JRequest::getCmd('option').'&view=categories';
		JSubMenuHelper::addEntry(JText::_('ARS_TITLE_CATEGORIES'), $link);
		// -- Releases
		$link = JURI::base().'?option='.JRequest::getCmd('option').'&view=releases';
		JSubMenuHelper::addEntry(JText::_('ARS_TITLE_RELEASES'), $link);
		// -- Items
		$link = JURI::base().'?option='.JRequest::getCmd('option').'&view=items';
		JSubMenuHelper::addEntry(JText::_('ARS_TITLE_ITEMS'), $link);
		// -- Import
		$link = JURI::base().'?option='.JRequest::getCmd('option').'&view=impjed';
		JSubMenuHelper::addEntry(JText::_('ARS_TITLE_IMPORT_JED'), $link);
		
		// Load the select box helper
		require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'select.php';

		// Run the parent method
		parent::onDisplay();
	}

	protected function onAdd()
	{
		// Load the select box helper
		require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'select.php';
		parent::onAdd();
		
		$model = $this->getModel();
		$fltRelease		= $model->getState('release', null, 'int');

		if($fltRelease) {
			$item = $model->getItem();
			$item->release_id = $fltRelease;
			$this->assignRef( 'item',		$item );
		}
	}
}