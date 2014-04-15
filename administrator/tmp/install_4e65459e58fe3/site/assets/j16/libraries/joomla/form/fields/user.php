<?php
/**
 * @version		$Id: usergroup.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Framework
 * @subpackage	Form
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldUser extends JFormField {
    /**
     * The form field type.
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'User';

    /**
     * Method to get the field input markup.
     *
     * @return	string	The field input markup.
     * @since	1.6
     */
    protected function getInput () {
        $db =& JFactory::getDbo();
        /* @var $db JDatabase */
        $db->setQuery('SELECT `name` FROM `#__users` WHERE `id` = ' . (int) $this->value);
        return $db->loadResult();
    }
}
?>