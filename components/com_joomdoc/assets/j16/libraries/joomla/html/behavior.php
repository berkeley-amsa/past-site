<?php
/**
 * @version		$Id: behavior.php 21156 2011-04-15 03:58:11Z ian $
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Utility class for javascript behaviors
 *
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @version		1.5
 */
abstract class JHtmlBehavior
{
	/**
	 * Method to load the mootools framework into the document head
	 *
	 * - If debugging mode is on an uncompressed version of mootools is included for easier debugging.
	 *
	 * @param	string	$extras	Mootools file to load
	 * @param	boolean	$debug	Is debugging mode on? [optional]
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function framework($extras = false, $debug = null)
	{
		static $loaded = array();

		$type = $extras ? 'more' : 'core';

		// Only load once
		if (!empty($loaded[$type])) {
			return;
		}

		//JHtml::core($debug);

		// If no debugging value is set, use the configuration setting
		if ($debug === null) {
			$config = JFactory::getConfig();
			$debug = $config->get('debug');
		}

		$uncompressed	= $debug ? '-uncompressed' : '';

		if ($type != 'core' && empty($loaded['core'])) {
			self::framework(false, $debug);
		}
		J16Html::script('system/mootools-'.$type.$uncompressed.'.js', false, true, false, false);
		$loaded[$type] = true;

		return;
	}

	/**
	 * Deprecated. Use JHtmlBehavior::framework() instead.
	 *
	 * @param	boolean	$debug	Is debugging mode on? [optional]
	 * @deprecated
	 * @return	void
	 * @since	1.5
	 */
	public static function mootools($debug = null)
	{
		self::framework(true, $debug);
	}

	/**
	 * Add unobtrusive javascript support for image captions.
	 *
	 * @return	void
	 * @since	1.5
	 */
	public static function caption()
	{
		static $loaded = false;

		// only load once
		if ($loaded) {
			return;
		}

		// Include mootools framework
		self::framework();

		$uncompressed = JFactory::getConfig()->get('debug') ? '-uncompressed' : '';
		J16Html::script('system/caption'.$uncompressed.'.js', true, true);
		$loaded = true;
	}

	/**
	 * Add unobtrusive javascript support for form validation.
	 *
	 * To enable form validation the form tag must have class="form-validate".
	 * Each field that needs to be validated need to have class="validate".
	 * Additional handlers can be added to the handler for username, password,
	 * numberic and email. To use these add class="validate-email" and so on.
	 *
	 * @return	void
	 * @since	1.5
	 */
	public static function formvalidation()
	{
		static $loaded = false;

		// only load once
		if ($loaded) {
			return;
		}

		// Include mootools framework
		self::framework();

		$uncompressed = JFactory::getConfig()->get('debug') ? '-uncompressed' : '';
		J16Html::script('system/validate'.$uncompressed.'.js', true, true);
		$loaded = true;
	}

	/**
	 * Add unobtrusive javascript support for submenu switcher support in
	 * Global Configuration and System Information.
	 *
	 * @return	void
	 * @since	1.5
	 */
	public static function switcher()
	{
		static $loaded = false;

		// only load once
		if ($loaded) {
			return;
		}

		// Include mootools framework
		self::framework();

		$uncompressed = JFactory::getConfig()->get('debug') ? '-uncompressed' : '';
		J16Html::script('system/switcher'.$uncompressed.'.js', true, true);

		$script = "
			document.switcher = null;
			window.addEvent('domready', function(){
				toggler = document.id('submenu');
				element = document.id('config-document');
				if (element) {
					document.switcher = new JSwitcher(toggler, element, {cookieName: toggler.getProperty('class')});
				}
			});";

		JFactory::getDocument()->addScriptDeclaration($script);
		$loaded = true;
	}

	/**
	 * Add unobtrusive javascript support for a comboox effect.
	 *
	 * Note that this control is only reliable in absolutely positioned elements.
	 * Avoid using a combobox in a slider or dynamic pane.
	 *
	 * @return	void
	 * @since	1.5
	 */
	public static function combobox()
	{
		static $loaded = false;

		// only load once
		if ($loaded) {
			return;
		}

		// Include mootools framework
		self::framework();

		$uncompressed = JFactory::getConfig()->get('debug') ? '-uncompressed' : '';
		J16Html::script('system/combobox'.$uncompressed.'.js', true, true);
		$loaded = true;
	}

	function tooltip($selector='.hasTip', $params = array())
	{
		static $tips;

		if (!isset($tips)) {
			$tips = array();
		}

		// Include mootools framework
		JHTMLBehavior::mootools();

		$sig = md5(serialize(array($selector,$params)));
		if (isset($tips[$sig]) && ($tips[$sig])) {
			return;
		}

		// Setup options object
		$opt['maxTitleChars']	= (isset($params['maxTitleChars']) && ($params['maxTitleChars'])) ? (int)$params['maxTitleChars'] : 50 ;
		// offsets needs an array in the format: array('x'=>20, 'y'=>30)
		$opt['offsets']			= (isset($params['offsets']) && (is_array($params['offsets']))) ? $params['offsets'] : null;
		$opt['showDelay']		= (isset($params['showDelay'])) ? (int)$params['showDelay'] : null;
		$opt['hideDelay']		= (isset($params['hideDelay'])) ? (int)$params['hideDelay'] : null;
		$opt['className']		= (isset($params['className'])) ? $params['className'] : null;
		$opt['fixed']			= (isset($params['fixed']) && ($params['fixed'])) ? '\\true' : '\\false';
		$opt['onShow']			= (isset($params['onShow'])) ? '\\'.$params['onShow'] : null;
		$opt['onHide']			= (isset($params['onHide'])) ? '\\'.$params['onHide'] : null;

		$options = JHTMLBehavior::_getJSObject($opt);

		// Attach tooltips to document
		$document =& JFactory::getDocument();
		$tooltipInit = '		window.addEvent(\'domready\', function(){ var JTooltips = new Tips($$(\''.$selector.'\'), '.$options.'); });';
		$document->addScriptDeclaration($tooltipInit);

		// Set static array
		$tips[$sig] = true;
		return;
	}

	function modal($selector='a.modal', $params = array())
	{
		static $modals;
		static $included;

		$document =& JFactory::getDocument();

		// Load the necessary files if they haven't yet been loaded
		if (!isset($included)) {

			// Load the javascript and css
			JHTML::script('modal.js');
			JHTML::stylesheet('modal.css');

			$included = true;
		}

		if (!isset($modals)) {
			$modals = array();
		}

		$sig = md5(serialize(array($selector,$params)));
		if (isset($modals[$sig]) && ($modals[$sig])) {
			return;
		}

		// Setup options object
		$opt['ajaxOptions']	= (isset($params['ajaxOptions']) && (is_array($params['ajaxOptions']))) ? $params['ajaxOptions'] : null;
		$opt['size']		= (isset($params['size']) && (is_array($params['size']))) ? $params['size'] : null;
		$opt['onOpen']		= (isset($params['onOpen'])) ? $params['onOpen'] : null;
		$opt['onClose']		= (isset($params['onClose'])) ? $params['onClose'] : null;
		$opt['onUpdate']	= (isset($params['onUpdate'])) ? $params['onUpdate'] : null;
		$opt['onResize']	= (isset($params['onResize'])) ? $params['onResize'] : null;
		$opt['onMove']		= (isset($params['onMove'])) ? $params['onMove'] : null;
		$opt['onShow']		= (isset($params['onShow'])) ? $params['onShow'] : null;
		$opt['onHide']		= (isset($params['onHide'])) ? $params['onHide'] : null;

		$options = JHTMLBehavior::_getJSObject($opt);

		// Attach modal behavior to document
		$document->addScriptDeclaration("
		window.addEvent('domready', function() {

			SqueezeBox.initialize(".$options.");

			$$('".$selector."').each(function(el) {
				el.addEvent('click', function(e) {
					new Event(e).stop();
					SqueezeBox.fromElement(el);
				});
			});
		});");

		// Set static array
		$modals[$sig] = true;
		return;
	}

	/**
	 * Add unobtrusive javascript support for the advanced uploader.
	 *
	 * @param	string	$id
	 * @param	array	$params	An array of options for the uploader.
	 * @param	string	$upload_queue
	 *
	 * @return	void
	 * @since	1.5
	 */
	public static function uploader($id='file-upload', $params = array(), $upload_queue='upload-queue')
	{
		// Include mootools framework
		self::framework();
		
		$uncompressed	= JFactory::getConfig()->get('debug') ? '-uncompressed' : '';
		J16Html::script('system/swf'.$uncompressed.'.js', true, true);
		J16Html::script('system/progressbar'.$uncompressed.'.js', true, true);
		J16Html::script('system/uploader'.$uncompressed.'.js', true, true);

		$document = JFactory::getDocument();

		static $uploaders;

		if (!isset($uploaders)) {
			$uploaders = array();

			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_FILENAME');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_UPLOAD_COMPLETED');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_OCCURRED');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ALL_FILES');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_PROGRESS_OVERALL');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_CURRENT_TITLE');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_REMOVE');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_REMOVE_TITLE');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_CURRENT_FILE');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_CURRENT_PROGRESS');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_FILE_ERROR');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_FILE_SUCCESSFULLY_UPLOADED');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_DUPLICATE');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_SIZELIMITMIN');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_SIZELIMITMAX');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_FILELISTMAX');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_FILELISTSIZEMAX');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_HTTPSTATUS');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_SECURITYERROR');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_IOERROR');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ALL_FILES');
		}

		if (isset($uploaders[$id]) && ($uploaders[$id])) {
			return;
		}

		$onFileSuccess = '\\function(file, response) {
			var json = new Hash(JSON.decode(response, true) || {});

			if (json.get(\'status\') == \'1\') {
				file.element.addClass(\'file-success\');
				file.info.set(\'html\', \'<strong>\' + Joomla.JText._(\'JLIB_HTML_BEHAVIOR_UPLOADER_FILE_SUCCESSFULLY_UPLOADED\') + \'</strong>\');
			} else {
				file.element.addClass(\'file-failed\');
				file.info.set(\'html\', \'<strong>\' +
					Joomla.JText._(\'JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_OCCURRED\', \'An Error Occurred\').substitute({ error: json.get(\'error\') }) + \'</strong>\');
			}
		}';

		// Setup options object
		$opt['verbose']				= true;
		$opt['url']					= (isset($params['targetURL'])) ? $params['targetURL'] : null ;
		$opt['path']				= (isset($params['swf'])) ? $params['swf'] : JURI::root(true).'/media/system/swf/uploader.swf';
		$opt['height']				= (isset($params['height'])) && $params['height'] ? (int)$params['height'] : null;
		$opt['width']				= (isset($params['width'])) && $params['width'] ? (int)$params['width'] : null;
		$opt['multiple']			= (isset($params['multiple']) && !($params['multiple'])) ? '\\false' : '\\true';
		$opt['queued']				= (isset($params['queued']) && !($params['queued'])) ? (int)$params['queued'] : null;
		$opt['target']				= (isset($params['target'])) ? $params['target'] : '\\document.id(\'upload-browse\')';
		$opt['instantStart']		= (isset($params['instantStart']) && ($params['instantStart'])) ? '\\true' : '\\false';
		$opt['allowDuplicates']		= (isset($params['allowDuplicates']) && !($params['allowDuplicates'])) ? '\\false' : '\\true';
		// limitSize is the old parameter name.  Remove in 1.7
		$opt['fileSizeMax']			= (isset($params['limitSize']) && ($params['limitSize'])) ? (int)$params['limitSize'] : null;
		// fileSizeMax is the new name.  If supplied, it will override the old value specified for limitSize
		$opt['fileSizeMax']			= (isset($params['fileSizeMax']) && ($params['fileSizeMax'])) ? (int)$params['fileSizeMax'] : $opt['fileSizeMax'];
		$opt['fileSizeMin']			= (isset($params['fileSizeMin']) && ($params['fileSizeMin'])) ? (int)$params['fileSizeMin'] : null;
		// limitFiles is the old parameter name.  Remove in 1.7
		$opt['fileListMax']			= (isset($params['limitFiles']) && ($params['limitFiles'])) ? (int)$params['limitFiles'] : null;
		// fileListMax is the new name.  If supplied, it will override the old value specified for limitFiles
		$opt['fileListMax']			= (isset($params['fileListMax']) && ($params['fileListMax'])) ? (int)$params['fileListMax'] : $opt['fileListMax'];
		$opt['fileListSizeMax']		= (isset($params['fileListSizeMax']) && ($params['fileListSizeMax'])) ? (int)$params['fileListSizeMax'] : null;
		// types is the old parameter name.  Remove in 1.7
		$opt['typeFilter']			= (isset($params['types'])) ? '\\'.$params['types'] : '\\{Joomla.JText._(\'JLIB_HTML_BEHAVIOR_UPLOADER_ALL_FILES\'): \'*.*\'}';
		$opt['typeFilter']			= (isset($params['typeFilter'])) ? '\\'.$params['typeFilter'] : $opt['typeFilter'];

		// Optional functions
		$opt['createReplacement'] 	= (isset($params['createReplacement'])) ? '\\'.$params['createReplacement'] : null;
		$opt['onFileComplete'] 		= (isset($params['onFileComplete'])) ? '\\'.$params['onFileComplete'] : null;
		$opt['onBeforeStart'] 		= (isset($params['onBeforeStart'])) ? '\\'.$params['onBeforeStart'] : null;
		$opt['onStart'] 			= (isset($params['onStart'])) ? '\\'.$params['onStart'] : null;
		$opt['onComplete'] 			= (isset($params['onComplete'])) ? '\\'.$params['onComplete'] : null;
		$opt['onFileSuccess'] 		= (isset($params['onFileSuccess'])) ? '\\'.$params['onFileSuccess'] : $onFileSuccess;

		if (!isset($params['startButton'])) {
			$params['startButton'] = 'upload-start';
		}

		if (!isset($params['clearButton'])) {
			$params['clearButton'] = 'upload-clear';
		}

		$opt['onLoad'] =
			'\\function() {
				document.id(\''.$id.'\').removeClass(\'hide\'); // we show the actual UI
				document.id(\'upload-noflash\').destroy(); // ... and hide the plain form

				// We relay the interactions with the overlayed flash to the link
				this.target.addEvents({
					click: function() {
						return false;
					},
					mouseenter: function() {
						this.addClass(\'hover\');
					},
					mouseleave: function() {
						this.removeClass(\'hover\');
						this.blur();
					},
					mousedown: function() {
						this.focus();
					}
				});

				// Interactions for the 2 other buttons

				document.id(\''.$params['clearButton'].'\').addEvent(\'click\', function() {
					Uploader.remove(); // remove all files
					return false;
				});

				document.id(\''.$params['startButton'].'\').addEvent(\'click\', function() {
					Uploader.start(); // start upload
					return false;
				});
			}';

		$options = JHtmlBehavior::_getJSObject($opt);

		// Attach tooltips to document
		$uploaderInit =
				'window.addEvent(\'domready\', function(){
				var Uploader = new FancyUpload2(document.id(\''.$id.'\'), document.id(\''.$upload_queue.'\'), '.$options.' );
				});';
		$document->addScriptDeclaration($uploaderInit);

		// Set static array
		$uploaders[$id] = true;

		return;
	}

	/**
	 * Add unobtrusive javascript support for a collapsible tree.
	 *
	 * @param	$id		string
	 * @param	$params	array	An array of options.
	 * @param	$root	array
	 *
	 * @return	void
	 * @since	1.5
	 */
	public static function tree($id, $params = array(), $root = array())
	{
		static $trees;

		if (!isset($trees)) {
			$trees = array();
		}

		// Include mootools framework
		self::framework();

		$uncompressed	= JFactory::getConfig()->get('debug') ? '-uncompressed' : '';
		J16Html::script('system/mootree'.$uncompressed.'.js', true, true, false, false);
		J16HTML::stylesheet('system/mootree.css', array(), true);

		if (isset($trees[$id]) && ($trees[$id])) {
			return;
		}

		// Setup options object
		$opt['div']		= (array_key_exists('div', $params)) ? $params['div'] : $id.'_tree';
		$opt['mode']	= (array_key_exists('mode', $params)) ? $params['mode'] : 'folders';
		$opt['grid']	= (array_key_exists('grid', $params)) ? '\\'.$params['grid'] : '\\true';
		$opt['theme']	= (array_key_exists('theme', $params)) ? $params['theme'] : JHtml::_('image','system/mootree.gif', '', array(), true, true);

		// Event handlers
		$opt['onExpand']	= (array_key_exists('onExpand', $params)) ? '\\'.$params['onExpand'] : null;
		$opt['onSelect']	= (array_key_exists('onSelect', $params)) ? '\\'.$params['onSelect'] : null;
		$opt['onClick']		= (array_key_exists('onClick', $params)) ? '\\'.$params['onClick'] : '\\function(node){  window.open(node.data.url, $chk(node.data.target) ? node.data.target : \'_self\'); }';
		$options = JHtmlBehavior::_getJSObject($opt);

		// Setup root node
		$rt['text']		= (array_key_exists('text', $root)) ? $root['text'] : 'Root';
		$rt['id']		= (array_key_exists('id', $root)) ? $root['id'] : null;
		$rt['color']	= (array_key_exists('color', $root)) ? $root['color'] : null;
		$rt['open']		= (array_key_exists('open', $root)) ? '\\'.$root['open'] : '\\true';
		$rt['icon']		= (array_key_exists('icon', $root)) ? $root['icon'] : null;
		$rt['openicon']	= (array_key_exists('openicon', $root)) ? $root['openicon'] : null;
		$rt['data']		= (array_key_exists('data', $root)) ? $root['data'] : null;
		$rootNode = JHtmlBehavior::_getJSObject($rt);

		$treeName		= (array_key_exists('treeName', $params)) ? $params['treeName'] : '';

		$js = '		window.addEvent(\'domready\', function(){
			tree'.$treeName.' = new MooTreeControl('.$options.','.$rootNode.');
			tree'.$treeName.'.adopt(\''.$id.'\');})';

		// Attach tooltips to document
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($js);

		// Set static array
		$trees[$id] = true;

		return;
	}

	function calendar()
	{
		$document =& JFactory::getDocument();
		J16HTML::stylesheet('media/system/css/calendar-jos.css', array(' title' => JText::_( 'green' ) ,' media' => 'all' ));
		J16HTML::script('media/system/js/calendar.js' );
		J16HTML::script('media/system/js/calendar-setup.js' );

		$translation = JHTMLBehavior::_calendartranslation();
		if($translation) {
			$document->addScriptDeclaration($translation);
		}
	}
	
	
	/**
	 * Keep session alive, for example, while editing or creating an article.
	 *
	 * @return	void
	 * @since	1.5
	 */
	public static function keepalive()
	{
		static $loaded = false;

		// only load once
		if ($loaded) {
			return;
		}

		// Include mootools framework
		self::framework();

		$config		= JFactory::getConfig();
		$lifetime	= ($config->get('lifetime') * 60000);
		$refreshTime =  ($lifetime <= 60000) ? 30000 : $lifetime - 60000;
		//refresh time is 1 minute less than the liftime assined in the configuration.php file

		// the longest refresh period is one hour to prevent integer overflow.
		if ($refreshTime > 3600000 || $refreshTime <= 0) {
			$refreshTime = 3600000;
		}

		$document = JFactory::getDocument();
		$script  = '';
		$script .= 'function keepAlive() {';
		$script .=  '	var myAjax = new Request({method: "get", url: "index.php"}).send();';
		$script .=  '}';
		$script .=	' window.addEvent("domready", function()';
		$script .=	'{ keepAlive.periodical('.$refreshTime.'); }';
		$script .=  ');';

		$document->addScriptDeclaration($script);
		$loaded = true;

		return;
	}

	/**
	 * Break us out of any containing iframes
	 *
	 * @return	void
	 * @since	1.5
	 */
	public static function noframes($location='top.location.href')
	{
		static $loaded = false;

		// only load once
		if ($loaded) {
			return;
		}

		// Include mootools framework
		self::framework();

		$js = "window.addEvent('domready', function () {if (top == self) {document.documentElement.style.display = 'block'; } else {top.location = self.location; }});";
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('html { display:none }');
		$document->addScriptDeclaration($js);
		
		JResponse::setHeader('X-Frames-Options', 'SAME-ORIGIN');

		$loaded = true;
	}

	/**
	 * Internal method to get a JavaScript object notation string from an array
	 *
	 * @param	array	$array	The array to convert to JavaScript object notation
	 *
	 * @return	string	JavaScript object notation representation of the array
	 * @since	1.5
	 */
	protected static function _getJSObject($array=array())
	{
		// Initialise variables.
		$object = '{';

		// Iterate over array to build objects
		foreach ((array)$array as $k => $v)
		{
			if (is_null($v)) {
				continue;
			}

			if (is_bool($v)) {
				if ($k === 'fullScreen') {
					$object .= 'size: { ';
					$object .= 'x: ';
					$object .= 'window.getSize().x-80';
					$object .= ',';
					$object .= 'y: ';
					$object .= 'window.getSize().y-80';
					$object .= ' }';
					$object .= ',';
				} else {
					$object .= ' '.$k.': ';
					$object .= ($v) ? 'true' : 'false';
					$object .= ',';
				}
			} else if (!is_array($v) && !is_object($v)) {
				$object .= ' '.$k.': ';
				$object .= (is_numeric($v) || strpos($v, '\\') === 0) ? (is_numeric($v)) ? $v : substr($v, 1) : "'".$v."'";
				$object .= ',';
			} else {
				$object .= ' '.$k.': '.JHtmlBehavior::_getJSObject($v).',';
			}
		}

		if (substr($object, -1) == ',') {
			$object = substr($object, 0, -1);
		}

		$object .= '}';

		return $object;
	}

	/**
	 * Internal method to translate the JavaScript Calendar
	 *
	 * @return	string	JavaScript that translates the object
	 * @since	1.5
	 */
	function _calendartranslation()
	{
		static $jsscript = 0;

		if($jsscript == 0)
		{
			$return = 'Calendar._DN = new Array ("'.JText::_('Sunday').'", "'.JText::_('Monday').'", "'.JText::_('Tuesday').'", "'.JText::_('Wednesday').'", "'.JText::_('Thursday').'", "'.JText::_('Friday').'", "'.JText::_('Saturday').'", "'.JText::_('Sunday').'");Calendar._SDN = new Array ("'.JText::_('Sun').'", "'.JText::_('Mon').'", "'.JText::_('Tue').'", "'.JText::_('Wed').'", "'.JText::_('Thu').'", "'.JText::_('Fri').'", "'.JText::_('Sat').'", "'.JText::_('Sun').'"); Calendar._FD = 0;	Calendar._MN = new Array ("'.JText::_('January').'", "'.JText::_('February').'", "'.JText::_('March').'", "'.JText::_('April').'", "'.JText::_('May').'", "'.JText::_('June').'", "'.JText::_('July').'", "'.JText::_('August').'", "'.JText::_('September').'", "'.JText::_('October').'", "'.JText::_('November').'", "'.JText::_('December').'");	Calendar._SMN = new Array ("'.JText::_('January_short').'", "'.JText::_('February_short').'", "'.JText::_('March_short').'", "'.JText::_('April_short').'", "'.JText::_('May_short').'", "'.JText::_('June_short').'", "'.JText::_('July_short').'", "'.JText::_('August_short').'", "'.JText::_('September_short').'", "'.JText::_('October_short').'", "'.JText::_('November_short').'", "'.JText::_('December_short').'");Calendar._TT = {};Calendar._TT["INFO"] = "'.JText::_('About the calendar').'";
 		Calendar._TT["ABOUT"] =
 "DHTML Date/Time Selector\n" +
 "(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" +
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"Date selection:\n" +
"- Use the \xab, \xbb buttons to select year\n" +
"- Use the " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " buttons to select month\n" +
"- Hold mouse button on any of the above buttons for faster selection.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Time selection:\n" +
"- Click on any of the time parts to increase it\n" +
"- or Shift-click to decrease it\n" +
"- or click and drag for faster selection.";

		Calendar._TT["PREV_YEAR"] = "'.JText::_('Prev. year (hold for menu)').'";Calendar._TT["PREV_MONTH"] = "'.JText::_('Prev. month (hold for menu)').'";	Calendar._TT["GO_TODAY"] = "'.JText::_('Go Today').'";Calendar._TT["NEXT_MONTH"] = "'.JText::_('Next month (hold for menu)').'";Calendar._TT["NEXT_YEAR"] = "'.JText::_('Next year (hold for menu)').'";Calendar._TT["SEL_DATE"] = "'.JText::_('Select date').'";Calendar._TT["DRAG_TO_MOVE"] = "'.JText::_('Drag to move').'";Calendar._TT["PART_TODAY"] = "'.JText::_('(Today)').'";Calendar._TT["DAY_FIRST"] = "'.JText::_('Display %s first').'";Calendar._TT["WEEKEND"] = "0,6";Calendar._TT["CLOSE"] = "'.JText::_('Close').'";Calendar._TT["TODAY"] = "'.JText::_('Today').'";Calendar._TT["TIME_PART"] = "'.JText::_('(Shift-)Click or drag to change value').'";Calendar._TT["DEF_DATE_FORMAT"] = "'.JText::_('%Y-%m-%d').'"; Calendar._TT["TT_DATE_FORMAT"] = "'.JText::_('%a, %b %e').'";Calendar._TT["WK"] = "'.JText::_('wk').'";Calendar._TT["TIME"] = "'.JText::_('Time:').'";';
			$jsscript = 1;
			return $return;
		} else {
			return false;
		}
	}
}
