<?php
/**
* @package   com_zoo Component
* @file      system.php
* @version   2.4.9 May 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: SystemHelper
		Joomla system helper class, provides Joomla CMS integration
*/
class SystemHelper extends AppHelper {

	/* item id */
	public $itemid;
	
	/* factory variables */
	protected static $_factory = array('application', 'config', 'language', 'user', 'session', 'document', 'acl', 'template', 'dbo', 'mailer', 'editor');

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// init additional site vars
		if ($this->application->isSite()) {
			$this->itemid = $this->app->request->get('Itemid', 'int', 0);
		}

	}

	/*
		Function: __get
			Retrieve a Joomla environment variable

		Parameters:
			$name - Variable name

		Returns:
			Mixed
	*/	
	public function __get($name) {

		$name = strtolower($name);

		if (in_array($name, self::$_factory)) {
			return call_user_func(array('JFactory', 'get'.$name));
		} elseif ($name == 'dispatcher') {
			return JDispatcher::getInstance();
		}

		return null;
	}
	
	/*
		Function: __call
			Map all functions to JFactory class

		Parameters:
			$name - Method name
			$args - Method arguments

		Returns:
			Mixed
	*/	
    public function __call($method, $args) {
		return $this->_call(array('JFactory', $method), $args);
    }	
	
}