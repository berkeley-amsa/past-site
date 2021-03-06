<?php
/**
* @package   com_zoo Component
* @file      itemprint.php
* @version   2.4.9 May 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ElementItemPrint
		The item print element class
*/
class ElementItemPrint extends Element {

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		return true;
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
		return null;
	}

	/*
		Function: render
			Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		$params = $this->app->data->create($params);

		// include assets css
		$this->app->document->addStylesheet('elements:itemprint/assets/css/itemprint.css');

		if ($this->app->request->getBool('print', 0)) {

			return '<a class="element-print-button" onclick="window.print();return false;" href="#"></a>';

		} else {

			$this->app->html->_('behavior.modal', 'a.modal');
			$text  = $params->get('showicon') ? '' : JText::_('Print');
			$class = $params->get('showicon') ? 'modal element-print-button' : 'modal';
			return '<a href="'.JRoute::_($this->app->route->item($this->_item).'&amp;tmpl=component&amp;print=1').'" title="'.JText::_('Print').'" rel="{handler: \'iframe\', size: {x: 850, y: 500}}" class="'.$class.'">'.$text.'</a>';
			
		}
	}

}