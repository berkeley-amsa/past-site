<?php

/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	JoomDOC
 * @author      ARTIO s.r.o., info@artio.net, http:://www.artio.net
 * @copyright	Copyright (C) 2011 Artio s.r.o.. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die();

class JoomDOCViewDocument extends JoomDOCView {
    /**
     * @var JForm
     */
    protected $form;
    /**
     * @var JObject
     */
    protected $document;
    /**
     * @var JoomDOCAccessHelper
     */
    protected $access;
    /**
     * @var JObject
     */
    protected $state;
        /**
     * Document edit page.
     *
     * @param string $tpl used template name
     * @return void
     */
    public function display ($tpl = null) {
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JAdministrator */
        $config = &JoomDOCConfig::getInstance();
        /* @var $config JoomDOCConfig */
        $model = &$this->getModel();
        /* @var $model JoomDOCModelDocument */

        $this->form = &$model->getForm();
        $this->document = &$model->getItem();
        $this->state = &$model->getState();
        $this->access = new JoomDOCAccessHelper($this->document);

        if (!isset($this->document->id) || empty($this->document->id)) {
            $this->form->setValue('path', null, $mainframe->getUserState('path'));
            $this->form->setValue('title', null, JFile::getName($mainframe->getUserState('path')));
        }
        
                $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar () {
        JRequest::setVar('hidemainmenu', true);
        JToolBarHelper::title(JText::_('JOOMDOC_DOCUMENT'), 'document');
        if ($this->access->canEdit || $this->access->canCreate) {
            JToolBarHelper::apply(JoomDOCHelper::getTask(JOOMDOC_DOCUMENT, JOOMDOC_TASK_APPLY), 'JTOOLBAR_APPLY');
            JToolBarHelper::save(JoomDOCHelper::getTask(JOOMDOC_DOCUMENT, JOOMDOC_TASK_SAVE), 'JTOOLBAR_SAVE');
        } else {
            $bar =& JToolBar::getInstance('toolbar');
            /* @var $bar JToolBar */
            $bar->appendButton('Disabled', 'apply', 'JTOOLBAR_APPLY');
            $bar->appendButton('Disabled', 'save', 'JTOOLBAR_SAVE');
        }
        JToolBarHelper::cancel(JoomDOCHelper::getTask(JOOMDOC_DOCUMENT, JOOMDOC_TASK_CANCEL), 'JTOOLBAR_CLOSE');
    }
}
