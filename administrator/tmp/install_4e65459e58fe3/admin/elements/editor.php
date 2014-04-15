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

class JElementEditor extends JElement
{
    
    var $_name = 'Editor';

    function fetchElement($name, $value, &$node, $control_name)
    {
        $editor = &JFactory::getEditor();
        /* @var $editor JEditor */
        $code = $editor->display($control_name . '[' . $name . ']', $value, 550, 350, 1, 1, false);
        return $code;
    }
}

?>