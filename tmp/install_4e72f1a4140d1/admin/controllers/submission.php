<?php
/**
* @package   com_zoo Component
* @file      submission.php
* @version   2.4.9 May 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: SubmissionController
		The controller class for submission
*/
class SubmissionController extends AppController {

	public $application;

	public function __construct($default = array()) {
		parent::__construct($default);

		// set table
		$this->table = $this->app->table->submission;

		// get application
		$this->application 	= $this->app->zoo->getApplication();

		// set base url
		$this->baseurl = $this->app->link(array('controller' => $this->controller), false);

		// register tasks
        $this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('saveandnew', 'save' );
	}

	public function display() {

		jimport('joomla.html.pagination');

		// get Joomla application
		$this->joomla = $this->app->system->application;

		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Items')));
		$this->app->toolbar->custom('docopy', 'copy.png', 'copy_f2.png', 'Copy');
		$this->app->toolbar->deleteList();
		$this->app->toolbar->editListX();
		$this->app->toolbar->addNewX();
		$this->app->zoo->toolbarHelp();

		$this->app->html->_('behavior.tooltip');

		$state_prefix       = $this->option.'_'.$this->application->id.'.submission';
		$filter_order	    = $this->joomla->getUserStateFromRequest($state_prefix.'filter_order', 'filter_order', 'name', 'cmd');
		$filter_order_Dir   = $this->joomla->getUserStateFromRequest($state_prefix.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');

		$this->groups = $this->app->zoo->getGroups();
        
        // get data from the table
		$where = array();

		// application filter
		$where[] = 'application_id = ' . (int) $this->application->id;

		$options = array(
			'conditions' => array(implode(' AND ', $where)),
			'order' => $filter_order.' '.$filter_order_Dir);

		$this->submissions = $this->table->all($options);
        $this->submissions = array_merge($this->submissions);

		// table ordering and search filter
		$this->lists['order_Dir'] = $filter_order_Dir;
		$this->lists['order']	  = $filter_order;

		// display view
		$this->getView()->display();
	}

	public function edit($tpl = null) {

		// disable menu
		$this->app->request->setVar('hidemainmenu', 1);

		// get request vars
		$cid  = $this->app->request->get('cid.0', 'int');
		$edit = $cid > 0;

		// get item
		if ($edit) {
			$this->submission = $this->table->get($cid);
		} else {
			$this->submission = $this->app->object->create('Submission');
			$this->submission->application_id = $this->application->id;
            $this->submission->access = 1;
		}

		// set toolbar items
		$this->app->system->application->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Submission').': '.$this->submission->name.' <small><small>[ '.($edit ? JText::_('Edit') : JText::_('New')).' ]</small></small>'));
		$this->app->toolbar->save();
		$this->app->toolbar->custom('saveandnew', 'saveandnew', 'saveandnew', 'Save  New', false);
		$this->app->toolbar->apply();
		$this->app->toolbar->cancel('cancel', $edit ? 'Close' : 'Cancel');
		$this->app->zoo->toolbarHelp();

        // published select
		$this->lists['select_published'] = $this->app->html->_('select.booleanlist', 'state', null, $this->submission->state);

		// access select
		$this->lists['select_access'] = $this->app->html->_('zoo.accesslevel', array(), 'access', 'class="inputbox"', 'value', 'text',$this->submission->access);

        // tooltip select
		$this->lists['select_tooltip'] = $this->app->html->_('select.booleanlist', 'params[show_tooltip]', null, $this->submission->showTooltip());

        // type select
        $this->types = array();
        foreach ($this->application->getTypes() as $type) {

            // list types with submission layouts only
            if (count($this->app->zoo->getLayouts($this->application, $type->id, 'submission')) > 0) {

                $form = $this->submission->getForm($type->id);

                $this->types[$type->id]['name'] = $type->name;

                $options = array($this->app->html->_('select.option', '', '- '.JText::_('not submittable').' -'));
                $this->types[$type->id]['select_layouts'] = $this->app->html->_('zoo.layoutList', $this->application, $type->id, 'submission', $options, 'params[form]['.$type->id.'][layout]', '', 'value', 'text', $form->get('layout'));

                $options = array($this->app->html->_('select.option', '', '- '.JText::_('uncategorized').' -'));
                $this->types[$type->id]['select_categories'] = $this->app->html->_('zoo.categorylist', $this->application, $options, 'params[form]['.$type->id.'][category]', 'size="1"', 'value', 'text', $form->get('category'));

            }
        }

        // display view
		$this->getView()->setLayout('edit')->display();
	}

	public function save() {

		// check for request forgeries
		$this->app->request->checkToken() or jexit('Invalid Token');

		// init vars
		$post = $this->app->request->get('post:', 'array', array());
		$cid  = $this->app->request->get('cid.0', 'int');

		try {

			// get item
			if ($cid) {
				$submission = $this->table->get($cid);
			} else {
				$submission = $this->app->object->create('Submission');
				$submission->application_id = $this->application->id;
			}

			// bind submission data
			$this->bind($submission, $post, array('params'));
            
            // generate unique slug
            $submission->alias = $this->app->submission->getUniqueAlias($submission->id, $this->app->string->sluggify($submission->alias));

			// set params
			$submission->getParams()
				->clear()
                ->set('form.', @$post['params']['form'])
                ->set('trusted_mode', @$post['params']['trusted_mode'])
				->set('show_tooltip', @$post['params']['show_tooltip'])
				->set('email_notification', @$post['params']['email_notification'])
				->set('config.', @$post['params']['config'])
				->set('content.', @$post['params']['content']);

			// save submission
			$this->table->save($submission);

			// set redirect message
			$msg = JText::_('Submission Saved');

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error Saving Submission').' ('.$e.')');
			$this->_task = 'apply';
			$msg = null;

		}

		$link = $this->baseurl;
		switch ($this->getTask()) {
			case 'apply' :
				$link .= '&task=edit&cid[]='.$submission->id;
				break;
			case 'saveandnew' :
				$link .= '&task=add';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	public function remove() {

		// check for request forgeries
		$this->app->request->checkToken() or jexit('Invalid Token');

		// init vars
		$cid = $this->app->request->get('cid', 'array', array());

		if (count($cid) < 1) {
			$this->app->error->raiseError(500, JText::_('Select a submission to delete'));
		}

		try {

			// delete items
			foreach ($cid as $id) {
				$this->table->delete($this->table->get($id));
			}

			// set redirect message
			$msg = JText::_('Submission Deleted');

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseWarning(0, JText::_('Error Deleting Submission').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl, $msg);
	}

	public function docopy() {
		// check for request forgeries
		$this->app->request->checkToken() or jexit('Invalid Token');

		// init vars
		$cid = $this->app->request->get('cid', 'array', array());

		if (count($cid) < 1) {
			$this->app->error->raiseError(500, JText::_('Select a submission to copy'));
		}

		try {

			// copy submissions
			foreach ($cid as $id) {

				// get submission
				$submission = $this->table->get($id);

				// copy submission
				$submission->id         = 0;                         // set id to 0, to force new category
				$submission->name      .= ' ('.JText::_('Copy').')'; // set copied name
				$submission->alias      = $this->app->submission->getUniqueAlias($id, $submission->alias.'-copy'); // set copied alias

				// track parent for ordering update
				$parents[] = $submission->parent;

				// save copied category data
				$this->table->save($submission);
			}

            // set redirect message
			$msg = JText::_('Submission Copied');

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error Copying Category').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl, $msg);
	}

	public function publish() {
		$this->_editState(1);
	}

	public function unpublish() {
		$this->_editState(0);
	}

	protected function _editState($state) {

		// check for request forgeries
		$this->app->request->checkToken() or jexit('Invalid Token');

		// init vars
		$cid = $this->app->request->get('cid', 'array', array());

		if (count($cid) < 1) {
			$this->app->error->raiseError(500, JText::_('Select a submission to edit publish state'));
		}

		try {

			// update item state
			foreach ($cid as $id) {
				$submission = $this->table->get($id);
				$submission->state = $state;
				$this->table->save($submission);
			}

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error editing Submission Published State').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl);
	}

	public function accessPublic() {
		$this->_editAccess(0);
		$this->_editTrustedMode(0);
	}

	public function accessRegistered() {
		$this->_editAccess(1);
	}

	public function accessSpecial() {
		$this->_editAccess(2);
	}

	protected function _editAccess($access) {

		// check for request forgeries
		$this->app->request->checkToken() or jexit('Invalid Token');

		// init vars
		$cid = $this->app->request->get('cid', 'array', array());

		if (count($cid) < 1) {
			$this->app->error->raiseError(500, JText::_('Select a submission to edit access'));
		}

		try {

			// update item access
			foreach ($cid as $id) {
				$submission = $this->table->get($id);
				$submission->access = $access;
				$this->table->save($submission);
			}

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error Editing Submission Access').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl);
	}

	public function enableTrustedMode() {
		$this->_editTrustedMode(1);
	}

	public function disableTrustedMode() {
		$this->_editTrustedMode(0);
	}

	protected function _editTrustedMode($enabled) {

		// check for request forgeries
		$this->app->request->checkToken() or jexit('Invalid Token');

		// init vars
		$cid = $this->app->request->get('cid', 'array', array());

		if (count($cid) < 1) {
			$this->app->error->raiseError(500, JText::_('Select a submission to enable/disable Trusted Mode'));
		}

		try {

			// update item state
			foreach ($cid as $id) {
				$submission = $this->table->get($id);

				$submission->getParams()
					->set('trusted_mode', $enabled);

				$this->table->save($submission);
			}

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error enabling/disabling Submission Trusted Mode').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl);
	}

}

/*
	Class: SubmissionControllerException
*/
class SubmissionControllerException extends AppException {}