<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: AppTemplate
		Template related attributes and functions.
*/
class AppTemplate {

    /*
		Variable: name
			Template unique name.
    */
	public $name;

    /*
		Variable: resource
			Template resource.
    */
	public $resource;

    /*
		Variable: metaxml_file
			Template meta xml filename.
    */
	public $metaxml_file = "template.xml";

    /*
		Variable: app
			App instance.
    */
	public $app;

    /*
		Variable: _metaxml
			Template meta data AppXMLElement.
    */
	public $_metaxml;

	/*
    	Function: __construct
    	  Default Constructor

		Parameters:
	      $name - Template name
 	*/
	public function __construct($name, $resource) {
		// set vars
		$this->name = $name;
		$this->resource = rtrim($resource, '\/') . '/';
	}

	/*
		Function: getPath
			Get template path.

		Returns:
			String - Template path
	*/
	public function getPath() {
		return $this->app->path->path($this->resource);
	}

	/*
		Function: getURI
			Get template uri for CSS/JS loading.

		Returns:
			String - Template uri
	*/
	public function getURI() {
		return $this->app->path->url($this->resource);
	}

	/*
		Function: getParamsForm
			Gets template parameter form.

		Returns:
			AppParameterForm
	*/
	public function getParamsForm($global = false) {

		// get parameter xml file
		if ($file = $this->app->path->path($this->resource.$this->metaxml_file)) {
			
			// set xml file
			$xml = $file;
			
			// parse xml and add global
			if ($global) {
				$xml = $this->app->xml->loadFile($file);
				foreach ($xml->params as $param) {
					foreach ($param->children() as $element) {
						$type = (string) $element->attributes()->type;
						
						if (in_array($type, array('list', 'radio', 'text'))) {
							$element->attributes()->type = $type.'global';
						}
					}
				}				

				$xml = $xml->asXML(true);
			}

			// get form
			$form = $this->app->parameterform->create($xml);
			$form->addElementPath($this->app->path->path('joomla:elements'));

			return $form;
		}
		
		return null;
	}
	
	/*
		Function: getMetaData
			Get template xml meta data.

		Returns:
			Array - Meta information
	*/
	public function getMetaData() {

		$data = array();
		$xml  = $this->getMetaXML();

		if (!$xml) {
			return false;
		}
		
		if ($xml->getName() != 'template') {
			return false;
		}
		$data['name'] 		  = (string) $xml->name;
		$data['creationdate'] = $xml->creationDate ? (string) $xml->creationDate : 'Unknown';
		$data['author'] 	  = $xml->author ? (string) $xml->author : 'Unknown';
		$data['copyright'] 	  = (string) $xml->copyright;
		$data['authorEmail']  = (string) $xml->authorEmail;
		$data['authorUrl']    = (string) $xml->authorUrl;
		$data['version'] 	  = (string) $xml->version;
		$data['description']  = (string) $xml->description;

		$data['positions'] = array();
		if (isset($xml->positions)) {
			foreach ($xml->positions->children() as $element) {
				$data['positions'][] = (string) $element;
			}
		}

		return $data;
	}
	
	/*
		Function: getMetaXML
			Get template xml meta file.

		Returns:
			Object - AppXMLElement
	*/
	public function getMetaXML() {

		if (empty($this->_metaxml)) {
			$this->_metaxml = $this->app->xml->loadFile($this->app->path->path($this->resource . $this->metaxml_file));
		}

		return $this->_metaxml;
	}

}

/*
	Class: TemplateException
*/
class TemplateException extends AppException {}